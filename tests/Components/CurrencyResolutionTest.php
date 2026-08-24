<?php

declare(strict_types=1);

use Pelmered\FilamentMoneyField\Forms\Components\MoneyInput;
use Pelmered\FilamentMoneyField\Forms\Rules\MaxValueRule;
use Pelmered\FilamentMoneyField\Forms\Rules\MinValueRule;
use Pelmered\FilamentMoneyField\Tables\Columns\MoneyColumn;
use Pelmered\FilamentMoneyField\Tests\Support\Models\Post;
use Pelmered\FilamentMoneyField\Tests\Support\Models\PostWithoutCasts;
use Pelmered\LaraPara\Exceptions\UnsupportedCurrency;

/*
 * The whole resolution chain: the record's currency column, then a currency set on the
 * field, then the configured default. Ported from #118, which asserted the old order.
 *
 * The empty-column cases use PostWithoutCasts because CurrencyCast substitutes the default
 * currency for a null on the way in and rejects an empty string outright, so a cast model
 * can never present an empty column to getCurrency() - the fallback under test.
 */

it("takes the currency from the record's currency column", function (): void {
    $record = new Post(['title' => 'Test', 'price_currency' => 'SEK']);

    expect(
        getComponent(createFormTestComponent([MoneyInput::make('price')], fill: null, model: $record), 'price')
            ->getCurrency()->getCode()
    )->toBe('SEK');
});

it('prefers the record over a currency set on the field', function (): void {
    $record = new Post(['title' => 'Test', 'price_currency' => 'SEK']);

    expect(
        getComponent(createFormTestComponent([MoneyInput::make('price')->currency('EUR')], fill: null, model: $record), 'price')
            ->getCurrency()->getCode()
    )->toBe('SEK');
});

it('falls back to the currency set on the field when the record has none', function (mixed $empty): void {
    $record = new PostWithoutCasts(['title' => 'Test', 'price_currency' => $empty]);

    expect(
        getComponent(createFormTestComponent([MoneyInput::make('price')->currency('EUR')], fill: null, model: $record), 'price')
            ->getCurrency()->getCode()
    )->toBe('EUR');
})->with([
    'null'         => [null],
    'empty string' => [''],
    'only spaces'  => ['   '],
]);

it('falls back to the default currency when neither the record nor the field has one', function (): void {
    $record = new PostWithoutCasts(['title' => 'Test', 'price_currency' => null]);

    expect(
        getComponent(createFormTestComponent([MoneyInput::make('price')], fill: null, model: $record), 'price')
            ->getCurrency()->getCode()
    )->toBe('USD');
});

it('falls back to the default currency when the model has no currency column at all', function (): void {
    $record = new Post(['title' => 'No companion currency column exists for this one']);

    expect(
        getComponent(createFormTestComponent([MoneyInput::make('title')], fill: null, model: $record), 'title')
            ->getCurrency()->getCode()
    )->toBe('USD');
});

it('still rejects a currency column holding an unknown code', function (): void {
    $record = new PostWithoutCasts(['title' => 'Test', 'price_currency' => 'NOPE']);

    expect(fn (): string => getComponent(
        createFormTestComponent([MoneyInput::make('price')], fill: null, model: $record), 'price'
    )->getCurrency()->getCode())->toThrow(UnsupportedCurrency::class);
});

/*
 * Filament's getRecord() is typed Model|array|null, and a table fed plain arrays hits the
 * array side. Reading it with `->` warned and returned nothing, so a record that names its
 * currency was rendered in the default one.
 */

it('reads the currency column from an array record', function (): void {
    expect(
        MoneyColumn::make('price')
            ->record(['price' => 123456, 'price_currency' => 'SEK'])
            ->getCurrency()->getCode()
    )->toBe('SEK');
});

it('formats a bare amount on an array record in the currency that record names', function (): void {
    // A bare amount is minor units and has no currency of its own, so this is the render
    // path that has to ask the record - a Money state carries its own and never gets here.
    $formatted = MoneyColumn::make('price')
        ->currency('USD')
        ->locale('en_US')
        ->record(['price' => 123456, 'price_currency' => 'SEK'])
        ->formatState(123456);

    expect(replaceNonBreakingSpaces($formatted))->toContain('1,234.56')
        ->and(replaceNonBreakingSpaces($formatted))->toContain('SEK');
});

/*
 * A stored code this configuration does not list is a real error, and the field raises it
 * where it belongs - on render. The min/max rules resolved the currency outside their own
 * try, so the same bad row turned a save into a 500 instead of a form error.
 */

it('skips the bound instead of failing the save when the stored currency is unresolvable', function (string $rule): void {
    config(['larapara.available_currencies' => ['USD']]);

    $record = new PostWithoutCasts(['title' => 'Test', 'price_currency' => 'SEK']);
    $input  = getComponent(
        createFormTestComponent([MoneyInput::make('price')->minValue(100)->maxValue(1000)], fill: null, model: $record),
        'price'
    );

    $failed = false;
    (new $rule(100, $input))->validate('price', '1', function () use (&$failed): void {
        $failed = true;
    });

    expect($failed)->toBeFalse();
})->with([
    'min' => [MinValueRule::class],
    'max' => [MaxValueRule::class],
]);
