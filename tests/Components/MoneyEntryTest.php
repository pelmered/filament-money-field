<?php

use Money\Currency;
use Money\Money;
use Pelmered\FilamentMoneyField\Forms\Components\MoneyInput;
use Pelmered\FilamentMoneyField\Infolists\Components\MoneyEntry;

it('formats infolist money in usd', function (): void {
    $component = createInfolistTestComponent(
        [MoneyEntry::make('price')],
        ['price' => 100000000],
        'price',
    );
    $entry = getComponent($component, 'price');
    $state = $component->getState()['price']; // I don't know why $entry->getState() dosen't work.

    expect($entry->formatState($state))->toEqual('$1,000,000.00');
});

it('formats infolist money in usd with Money object', function (): void {
    $component = createInfolistTestComponent(
        [MoneyEntry::make('price')],
        ['price' => new Money(100000000, new Currency('USD'))],
        'price',
    );
    $entry = getComponent($component, 'price');

    $state = $component->getState()['price']; // I don't know why $entry->getState() dosen't work.

    expect($state)->toBeInstanceOf(Money::class);
    expect($entry->formatState($state))->toEqual('$1,000,000.00');
});

it('formats infolist money in sek', function (): void {
    $component = createInfolistTestComponent(
        [MoneyEntry::make('price')->currency('SEK')->locale('sv_SE')],
        ['price' => new Money(1000000, new Currency('SEK'))],
        'price',
    );
    $entry = getComponent($component, 'price');

    $formatted = $entry->formatState($component->getState()['price']);

    expect(replaceNonBreakingSpaces($formatted))->toEqual(replaceNonBreakingSpaces('10 000,00 kr'));
});

it('formats infolist money in short format in USD', function (): void {
    $component = createInfolistTestComponent(
        [MoneyEntry::make('price')->short()],
        [],
        'price',
    );

    $entry = getComponent($component, 'price');

    $formatted = $entry->formatState(new Money(123456789, new Currency('USD')));

    expect($formatted)->toEqual('$1.23M');
});

it('formats infolist money in short format in sek', function (): void {
    $component = createInfolistTestComponent(
        [MoneyEntry::make('price')->currency('SEK')->locale('sv_SE')->short()],
        ['price' => new Money(123600, new Currency('SEK'))],
        'price',
    );
    $entry = getComponent($component, 'price');

    $formatted = $entry->formatState($component->getState()['price']);

    expect(replaceNonBreakingSpaces($formatted))->toEqual(replaceNonBreakingSpaces('1,24K kr'));
});

it('formats infolist money in sek with no decimals', function (): void {
    $component = createInfolistTestComponent(
        [MoneyEntry::make('price')->currency('SEK')->locale('sv_SE')->decimals(0)],
        ['price' => new Money(1000000, new Currency('SEK'))],
        'price',
    );
    $entry = getComponent($component, 'price');

    $formatted = $entry->formatState($component->getState()['price']);

    expect(replaceNonBreakingSpaces($formatted))->toEqual(replaceNonBreakingSpaces('10 000 kr'));
});
