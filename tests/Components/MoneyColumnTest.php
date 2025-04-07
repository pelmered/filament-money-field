<?php

use Pelmered\FilamentMoneyField\Tables\Columns\MoneyColumn;

it('formats money column state with default currency (USD)', function (): void {
    $column = MoneyColumn::make('price');
    expect($column->formatState(2500))->toEqual('$25.00');
    expect($column->formatState(250000))->toEqual('$2,500.00');
    expect($column->formatState(2500001))->toEqual('$25,000.01');
});

it('formats money column state with eur', function (): void {
    $column = MoneyColumn::make('price')->currency('EUR');
    expect($column->formatState(2500))->toEqual('€25.00');
    expect($column->formatState(250000))->toEqual('€2,500.00');
    expect($column->formatState(2500001))->toEqual('€25,000.01');
});

it('formats money column state with sek', function (): void {
    $column = MoneyColumn::make('price')->currency('SEK')->locale('sv_SE');

    $formatted1 = $column->formatState(2500);
    $expected1  = '25,00 kr';
    expect(replaceNonBreakingSpaces($formatted1))->toEqual(replaceNonBreakingSpaces($expected1));

    $formatted2 = $column->formatState(250000);
    $expected2  = '2 500,00 kr';
    expect(replaceNonBreakingSpaces($formatted2))->toEqual(replaceNonBreakingSpaces($expected2));

    $formatted3 = $column->formatState(2500001);
    $expected3  = '25 000,01 kr';
    expect(replaceNonBreakingSpaces($formatted3))->toEqual(replaceNonBreakingSpaces($expected3));
});

it('formats money column state to short format with default currency (USD)', function (): void {
    $column = MoneyColumn::make('price')->short();
    expect($column->formatState(250))->toEqual('$2.50');
    expect($column->formatState(250056))->toEqual('$2.50K');
    expect($column->formatState(24604231))->toEqual('$246.04K');
    expect($column->formatState(2460523122))->toEqual('$24.61M');
});

it('formats money column state to short format with sek', function (): void {
    $column = MoneyColumn::make('price')->currency('SEK')->locale('sv_SE')->short();

    $formatted1 = $column->formatState(651);
    $expected1  = '6,51 kr';
    expect(replaceNonBreakingSpaces($formatted1))->toEqual(replaceNonBreakingSpaces($expected1));

    $formatted2 = $column->formatState(235235);
    $expected2  = '2,35K kr';
    expect(replaceNonBreakingSpaces($formatted2))->toEqual(replaceNonBreakingSpaces($expected2));

    $formatted3 = $column->formatState(23523562);
    $expected3  = '235,24K kr';
    expect(replaceNonBreakingSpaces($formatted3))->toEqual(replaceNonBreakingSpaces($expected3));
});

it('formats money column state to short format with sek and hide currency symbol', function (): void {
    $column = MoneyColumn::make('price')->currency('SEK')->locale('sv_SE')->short()->hideCurrencySymbol();

    $formatted1 = $column->formatState(651);
    $expected1  = '6,51';
    expect(replaceNonBreakingSpaces($formatted1))->toEqual(replaceNonBreakingSpaces($expected1));

    $formatted2 = $column->formatState(235235);
    $expected2  = '2,35K';
    expect(replaceNonBreakingSpaces($formatted2))->toEqual(replaceNonBreakingSpaces($expected2));

    $formatted3 = $column->formatState(23523562);
    $expected3  = '235,24K';
    expect(replaceNonBreakingSpaces($formatted3))->toEqual(replaceNonBreakingSpaces($expected3));

    $formatted4 = $column->formatState(2352356254);
    $expected4  = '23,52M';
    expect(replaceNonBreakingSpaces($formatted4))->toEqual(replaceNonBreakingSpaces($expected4));
});
