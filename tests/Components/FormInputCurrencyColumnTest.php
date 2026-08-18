<?php

declare(strict_types=1);

use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;
use Pelmered\FilamentMoneyField\Forms\Components\MoneyInput;
use Pelmered\FilamentMoneyField\Tests\Support\Components\FormTestComponent;
use Pelmered\FilamentMoneyField\Tests\Support\Models\Post;
use Pelmered\FilamentMoneyField\Tests\TestCase;

function currencyColumnField(MoneyInput $input, Model $record): MoneyInput
{
    $schema = Schema::make(FormTestComponent::make())
        ->model($record)
        ->statePath('data')
        ->components([$input]);

    $schema->fill();

    return $input;
}

// https://github.com/pelmered/filament-money-field/pull/95
it('falls back to the default currency when the record has no currency yet', function (): void {
    $field = currencyColumnField(
        MoneyInput::make('price'),
        new Post(['title' => 'Test', 'price' => null, 'price_currency' => null]),
    );

    expect($field->getCurrency()->getCode())->toBe('USD');
});

it('reads the currency from the currency column on the record', function (): void {
    $field = currencyColumnField(
        MoneyInput::make('price'),
        new Post(['title' => 'Test', 'price_currency' => 'SEK']),
    );

    expect($field->getCurrency()->getCode())->toBe('SEK')
        ->and(TestCase::callMethod($field, 'getCurrencyColumn', []))->toBe('price_currency');
});

it('keeps a currency column that does not follow the field name', function (): void {
    $field = currencyColumnField(
        MoneyInput::make('amount')->currencyColumn('price_currency'),
        new Post(['title' => 'Test', 'price_currency' => 'SEK', 'amount_currency' => 'EUR']),
    );

    expect(TestCase::callMethod($field, 'getCurrencyColumn', []))->toBe('price_currency')
        ->and($field->getCurrency()->getCode())->toBe('SEK');
});

it('reads the store format from the larapara config', function (): void {
    config(['larapara.store.format' => 'decimal']);

    $field = currencyColumnField(MoneyInput::make('price'), new Post(['title' => 'Test']));

    expect(TestCase::callMethod($field, 'getInMinorUnits', []))->toBeFalse();

    config(['larapara.store.format' => 'int']);

    expect(TestCase::callMethod($field, 'getInMinorUnits', []))->toBeTrue();
});
