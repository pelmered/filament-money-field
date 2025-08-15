<?php

use Money\Currency;
use Money\Money;
use Pelmered\FilamentMoneyField\Infolists\Components\MoneyEntry;
use Filament\Schemas\Schema;

it('formats infolist money in usd', function (): void {
    $entry = MoneyEntry::make('price');
    $state = new Money(100000000, new Currency('USD'));

    $schema = Schema::make();
    $entry->container($schema);

    $formatted = $entry->formatState($state);

    expect($formatted)->toEqual('$1,000,000.00');
});

it('formats infolist money in sek', function (): void {
    $entry = MoneyEntry::make('price')->currency('SEK')->locale('sv_SE');
    $state = new Money(1000000, new Currency('SEK'));

    $schema = Schema::make();
    $entry->container($schema);

    $formatted = $entry->formatState($state);
    $expected  = '10 000,00 kr';

    expect(replaceNonBreakingSpaces($formatted))->toEqual(replaceNonBreakingSpaces($expected));
});

it('formats infolist money in short format in USD', function (): void {
    $entry = MoneyEntry::make('price')->short();
    $state = new Money(123456789, new Currency('USD'));

    $schema = Schema::make();
    $entry->container($schema);

    $formatted = $entry->formatState($state);

    expect($formatted)->toEqual('$1.23M');
});

it('formats infolist money in short format in sek', function (): void {
    $entry = MoneyEntry::make('price')->short()->currency('SEK')->locale('sv_SE');
    $state = new Money(123456, new Currency('SEK'));

    $schema = Schema::make();
    $entry->container($schema);

    $formatted = $entry->formatState($state);
    $expected  = '1,23K kr';

    expect(replaceNonBreakingSpaces($formatted))->toEqual(replaceNonBreakingSpaces($expected));
});

it('formats infolist money in sek with no decimals', function (): void {
    $entry = MoneyEntry::make('price')->currency('SEK')->locale('sv_SE')->decimals(0);
    $state = new Money(1000000, new Currency('SEK'));

    $schema = Schema::make();
    $entry->container($schema);

    $formatted = $entry->formatState($state);
    $expected  = '10 000 kr';

    expect(replaceNonBreakingSpaces($formatted))->toEqual(replaceNonBreakingSpaces($expected));
});
