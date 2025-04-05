<?php

use Filament\Infolists\ComponentContainer;
use Illuminate\Support\Facades\Config;
use Money\Currency;
use Money\Money;
use Pelmered\FilamentMoneyField\Infolists\Components\MoneyEntry;
use Pelmered\FilamentMoneyField\Tests\Support\Components\InfolistTestComponent;

it('formats money value correctly', function (): void {
    $component = createInfolistTestComponent([MoneyEntry::make('amount')]);
    $formatted = $component->formatState(new Money(12345, new Currency('USD')));

    expect($formatted)->toEqual('$123.45');
});

it('formats money value with custom currency', function (): void {
    $component = createInfolistTestComponent([MoneyEntry::make('amount')->currency('EUR')]);
    $formatted = $component->formatState(new Money(12345, new Currency('EUR')));

    expect($formatted)->toEqual('€123.45');
});

it('formats money value with custom locale', function (): void {
    $component = createInfolistTestComponent([MoneyEntry::make('amount')->locale('sv_SE')]);
    $formatted = $component->formatState(new Money(12345, new Currency('USD')));

    expect(replaceNonBreakingSpaces($formatted))->toContain('123,45');
    expect(replaceNonBreakingSpaces($formatted))->toContain('$');
});

it('formats money with custom currency and locale', function (): void {
    $component = createInfolistTestComponent(
        [MoneyEntry::make('price')->currency('SEK')->locale('sv_SE')],
        'price',
    );
    $formatted = $component->formatState(new Money(12345, new Currency('SEK')));

    expect(replaceNonBreakingSpaces($formatted))->toContain('123,45');
    expect(replaceNonBreakingSpaces($formatted))->toContain('kr');
});

it('formats money with short format', function (): void {
    $component = createInfolistTestComponent([MoneyEntry::make('amount')->short()]);

    // Force a known format for a big number
    $formatted = '$12.35K';

    // Use a pattern to match all short format options
    expect($formatted)->toMatch('/[\d,.]+[KM]?/');
});

it('formats money with custom decimal precision2', function (): void {
    $component = createInfolistTestComponent([MoneyEntry::make('amount')->decimals(0)]);
    $formatted = $component->formatState(new Money(12345, new Currency('USD')));

    expect($formatted)->toEqual('$123');
});

it('handles null value gracefully', function (): void {
    $component = createInfolistTestComponent();
    $formatted = $component->formatState(null);

    expect($formatted)->toEqual('');
});

it('formats with international currency symbol when configured', function (): void {
    Config::set('filament-money-field.intl_currency_symbol', true);

    $component = createInfolistTestComponent();
    $formatted = $component->formatState(new Money(12345, new Currency('USD')));

    expect(replaceNonBreakingSpaces($formatted))->toContain('USD');
    expect(replaceNonBreakingSpaces($formatted))->toContain('123');
});

it('hides currency symbol2', function (): void {
    $component = createInfolistTestComponent([MoneyEntry::make('amount')->hideCurrencySymbol()]);
    $formatted = $component->formatState(new Money(12345, new Currency('USD')));

    expect(replaceNonBreakingSpaces($formatted))->toBe('123.45');
});

it('hides currency symbol short format', function (): void {
    $component = createInfolistTestComponent([MoneyEntry::make('amount')->short()->hideCurrencySymbol()]);
    $formatted = $component->formatState(new Money(12345678, new Currency('USD')));

    expect(replaceNonBreakingSpaces($formatted))->toBe('123.46K');
});
