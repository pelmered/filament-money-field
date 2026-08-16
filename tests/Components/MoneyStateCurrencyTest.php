<?php

declare(strict_types=1);

use Money\Currency;
use Money\Money;
use Pelmered\FilamentMoneyField\Infolists\Components\MoneyEntry;
use Pelmered\FilamentMoneyField\Tables\Columns\MoneyColumn;

// https://github.com/pelmered/filament-money-field/pull/95#issuecomment-2889068936
it('formats a column from the currency carried by the Money state', function (): void {
    $formatted = MoneyColumn::make('amount')->formatState(new Money(12345, new Currency('EUR')));

    expect($formatted)->toEqual('€123.45');
});

it('formats an entry from the currency carried by the Money state', function (): void {
    $component = createInfolistTestComponent(
        [MoneyEntry::make('amount')],
        ['amount' => new Money(12345, new Currency('EUR'))],
        'amount',
    );
    $entry = getComponent($component, 'amount');

    expect($entry->formatState(getComponentState($component, 'amount')))->toEqual('€123.45');
});

it('formats a short column from the currency carried by the Money state', function (): void {
    $formatted = MoneyColumn::make('amount')->short()->formatState(new Money(123456789, new Currency('EUR')));

    expect($formatted)->toEqual('€1.23M');
});

it('still lets an explicit currency win over the Money state', function (): void {
    $formatted = MoneyColumn::make('amount')
        ->currency('SEK')
        ->locale('sv_SE')
        ->formatState(new Money(12345, new Currency('USD')));

    expect(replaceNonBreakingSpaces($formatted))->toEqual('123,45 kr');
});
