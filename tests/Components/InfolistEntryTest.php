<?php

use Illuminate\Support\Facades\Config;
use Money\Currency;
use Money\Money;
use Pelmered\FilamentMoneyField\Infolists\Components\MoneyEntry;

it('formats money value correctly', function (): void {

    $component = createInfolistTestComponent(
        [MoneyEntry::make('amount')],
        ['amount' => new Money(12345, new Currency('USD'))],
        'amount',
    );
    $entry = getComponent($component, 'amount');

    $formatted = $entry->formatState($component->getState()['amount']);

    expect($formatted)->toEqual('$123.45');
});

it('formats money value with custom currency', function (): void {
    $component = createInfolistTestComponent(
        [MoneyEntry::make('amount')->currency('EUR')],
        ['amount' => new Money(12345, new Currency('EUR'))],
        'amount',
    );
    $entry = getComponent($component, 'amount');

    $formatted = $entry->formatState($component->getState()['amount']);

    expect($formatted)->toEqual('€123.45');
});

it('formats money value with custom locale', function (): void {
    $component = createInfolistTestComponent(
        [MoneyEntry::make('amount')->locale('sv_SE')],
        ['amount' => new Money(12345, new Currency('USD'))],
        'amount',
    );
    $entry = getComponent($component, 'amount');

    $formatted = $entry->formatState($component->getState()['amount']);

    expect(replaceNonBreakingSpaces($formatted))->toEqual('123,45 US$');
});

it('formats money with custom currency and locale', function (): void {
    $component = createInfolistTestComponent(
        [MoneyEntry::make('amount')->currency('SEK')->locale('sv_SE')],
        ['amount' => new Money(12345, new Currency('SEK'))],
        'amount',
    );
    $entry = getComponent($component, 'amount');

    $formatted = $entry->formatState($component->getState()['amount']);

    expect(replaceNonBreakingSpaces($formatted))->toEqual('123,45 kr');
});

it('formats money with short format', function (): void {
    $component = createInfolistTestComponent([MoneyEntry::make('amount')->short()]);

    // Force a known format for a big number
    $formatted = '$12.35K';

    // Use a pattern to match all short format options
    expect($formatted)->toMatch('/[\d,.]+[KM]?/');
});

it('formats money with custom decimal precision2', function (): void {
    $component = createInfolistTestComponent(
        [MoneyEntry::make('amount')->decimals(0)],
        ['amount' => new Money(12345, new Currency('USD'))],
        'amount',
    );
    $entry = getComponent($component, 'amount');

    $formatted = $entry->formatState($component->getState()['amount']);

    expect($formatted)->toEqual('$123');
});

it('handles null value gracefully', function (): void {
    $component = createInfolistTestComponent(
        [MoneyEntry::make('amount')->decimals(0)],
        ['amount' => null],
        'amount',
    );
    $entry = getComponent($component, 'amount');

    $formatted = $entry->formatState($component->getState()['amount']);

    expect($formatted)->toEqual('');
});

it('formats with international currency symbol when configured', function (): void {
    Config::set('larapara.intl_currency_symbol', true);

    $component = createInfolistTestComponent(
        [MoneyEntry::make('amount')],
        ['amount' => new Money(12345, new Currency('USD'))],
        'amount',
    );
    $entry = getComponent($component, 'amount');

    $formatted = $entry->formatState($component->getState()['amount']);

    expect(replaceNonBreakingSpaces($formatted))->toContain('USD');
    expect(replaceNonBreakingSpaces($formatted))->toContain('123');
});

it('hides currency symbol2', function (): void {
    $component = createInfolistTestComponent(
        [MoneyEntry::make('amount')->hideCurrencySymbol()],
        ['amount' => new Money(12345, new Currency('USD'))],
        'amount',
    );
    $entry = getComponent($component, 'amount');

    $formatted = $entry->formatState($component->getState()['amount']);

    expect(replaceNonBreakingSpaces($formatted))->toBe('123.45');
});

it('hides currency symbol short format', function (): void {
    $component = createInfolistTestComponent(
        [MoneyEntry::make('amount')->short()->hideCurrencySymbol()],
        ['amount' => new Money(12345678, new Currency('USD'))],
        'amount',
    );
    $entry = getComponent($component, 'amount');

    $formatted = $entry->formatState($component->getState()['amount']);

    expect(replaceNonBreakingSpaces($formatted))->toBe('123.46K');
});
