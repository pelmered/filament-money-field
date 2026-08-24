<?php

declare(strict_types=1);

use Pelmered\FilamentMoneyField\Forms\Components\MoneyInput;
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
