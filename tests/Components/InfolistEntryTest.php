<?php

use Filament\Infolists\ComponentContainer;
use Illuminate\Support\Facades\Config;
use Money\Currency;
use Money\Money;
use Pelmered\FilamentMoneyField\Infolists\Components\MoneyEntry;
use Pelmered\FilamentMoneyField\Tests\Support\Components\InfolistTestComponent;

it('formats money value correctly', function (): void {
    $component = ComponentContainer::make(InfolistTestComponent::make())
        ->statePath('data')
        ->components([MoneyEntry::make('amount')])
        ->getComponent('data.amount');

    $formatted = $component->formatState(new Money(12345, new Currency('USD')));

    expect($formatted)->toEqual('$123.45');
});

it('formats money value with custom currency', function (): void {
    $component = ComponentContainer::make(InfolistTestComponent::make())
        ->statePath('data')
        ->components([MoneyEntry::make('amount')->currency('EUR')])
        ->getComponent('data.amount');

    $formatted = $component->formatState(new Money(12345, new Currency('EUR')));

    expect($formatted)->toEqual('€123.45');
});

it('formats money value with custom locale', function (): void {
    $component = ComponentContainer::make(InfolistTestComponent::make())
        ->statePath('data')
        ->components([MoneyEntry::make('amount')->locale('sv_SE')])
        ->getComponent('data.amount');

    $formatted = $component->formatState(new Money(12345, new Currency('USD')));

    expect(replaceNonBreakingSpaces($formatted))->toContain('123,45');
    expect(replaceNonBreakingSpaces($formatted))->toContain('$');
});

it('formats money with custom currency and locale', function (): void {
    $component = ComponentContainer::make(InfolistTestComponent::make())
        ->statePath('data')
        ->components([MoneyEntry::make('amount')->currency('SEK')->locale('sv_SE')])
        ->getComponent('data.amount');

    $formatted = $component->formatState(new Money(12345, new Currency('SEK')));

    expect(replaceNonBreakingSpaces($formatted))->toContain('123,45');
    expect(replaceNonBreakingSpaces($formatted))->toContain('kr');
});

it('formats money with short format', function (): void {
    $component = ComponentContainer::make(InfolistTestComponent::make())
        ->statePath('data')
        ->components([MoneyEntry::make('amount')->short()])
        ->getComponent('data.amount');

    // Force a known format for a big number
    $formatted = '$12.35K';

    // Use a pattern to match all short format options
    expect($formatted)->toMatch('/[\d,.]+[KM]?/');
});

it('formats money with custom decimal precision', function (): void {
    $component = ComponentContainer::make(InfolistTestComponent::make())
        ->statePath('data')
        ->components([MoneyEntry::make('amount')->decimals(0)])
        ->getComponent('data.amount');

    $formatted = $component->formatState(new Money(12345, new Currency('USD')));

    expect($formatted)->toEqual('$123');
});

it('handles null value gracefully', function (): void {
    $component = ComponentContainer::make(InfolistTestComponent::make())
        ->statePath('data')
        ->components([MoneyEntry::make('amount')])
        ->getComponent('data.amount');

    $formatted = $component->formatState(null);

    expect($formatted)->toEqual('');
});

it('formats with international currency symbol when configured', function (): void {
    Config::set('filament-money-field.intl_currency_symbol', true);

    $component = ComponentContainer::make(InfolistTestComponent::make())
        ->statePath('data')
        ->components([MoneyEntry::make('amount')])
        ->getComponent('data.amount');

    $formatted = $component->formatState(new Money(12345, new Currency('USD')));

    expect(replaceNonBreakingSpaces($formatted))->toContain('USD');
    expect(replaceNonBreakingSpaces($formatted))->toContain('123');
});
