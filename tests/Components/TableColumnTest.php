<?php

use Illuminate\Support\Facades\Config;
use Money\Currency;
use Money\Money;
use Pelmered\FilamentMoneyField\Tables\Columns\MoneyColumn;

it('formats money value correctly', function () {
    $column = MoneyColumn::make('amount');
    $state  = new Money(12345, new Currency('USD'));

    $formatted = $column->formatState($state);

    expect($formatted)->toEqual('$123.45');
});

it('formats money value with custom currency correctly', function () {
    $column = MoneyColumn::make('amount')->currency('EUR');
    $state  = new Money(12345, new Currency('EUR'));

    $formatted = $column->formatState($state);

    expect($formatted)->toEqual('€123.45');
});

it('formats money value with custom locale correctly', function () {
    $column = MoneyColumn::make('amount')->locale('sv_SE');
    $state  = new Money(12345, new Currency('USD'));

    $formatted = $column->formatState($state);

    // The actual output might be "123,45 US$" depending on intl settings
    // So we'll check for key formatting indicators
    expect(replaceNonBreakingSpaces($formatted))->toContain('123,45');
    expect(replaceNonBreakingSpaces($formatted))->toContain('$');
});

it('formats money with custom currency and locale', function () {
    $column = MoneyColumn::make('amount')->currency('SEK')->locale('sv_SE');

    $formatted = $column->formatState(new Money(12345, new Currency('USD')));
    $expected  = '123,45 kr';

    expect(replaceNonBreakingSpaces($formatted))->toEqual(replaceNonBreakingSpaces($expected));
});

it('formats money with short format', function () {
    $column = MoneyColumn::make('amount')->short();

    $formatted = $column->formatState(new Money(1234567, new Currency('USD')));

    expect($formatted)->toEqual('$12.35K');
});

it('formats money with custom decimal precision', function () {
    $column = MoneyColumn::make('amount')->decimals(0);

    $formatted = $column->formatState(new Money(12345, new Currency('USD')));

    expect($formatted)->toEqual('$123');
});

it('handles null value gracefully', function () {
    $column = MoneyColumn::make('amount');

    $formatted = $column->formatState(null);

    expect($formatted)->toEqual('');
});

it('hides value when column is hidden', function () {
    $column = MoneyColumn::make('amount')->hidden();

    expect($column->isHidden())->toBeTrue();
});

it('formats money with international currency symbol when configured', function () {
    Config::set('filament-money-field.intl_currency_symbol', true);

    $column = MoneyColumn::make('amount');
    $state  = new Money(12345, new Currency('USD'));

    $formatted = $column->formatState($state);

    // Check that it contains USD and the numeric value
    expect(replaceNonBreakingSpaces($formatted))->toContain('USD');
    expect(replaceNonBreakingSpaces($formatted))->toContain('123');
});

it('can sort records based on money values', function () {
    $column = MoneyColumn::make('amount')->sortable();

    expect($column->isSortable())->toBeTrue();
});
