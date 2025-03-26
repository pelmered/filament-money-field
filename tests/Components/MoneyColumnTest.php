<?php

uses(\Pelmered\FilamentMoneyField\Tests\TestCase::class);
use Pelmered\FilamentMoneyField\Tables\Columns\MoneyColumn;

it('formats money column state with default currency (USD)', function () {
    $column = MoneyColumn::make('price');
    expect($column->formatState(2500))->toEqual('$25.00');
    expect($column->formatState(250000))->toEqual('$2,500.00');
    expect($column->formatState(2500001))->toEqual('$25,000.01');
});

it('formats money column state with eur', function () {
    $column = MoneyColumn::make('price')->currency('EUR');
    expect($column->formatState(2500))->toEqual('€25.00');
    expect($column->formatState(250000))->toEqual('€2,500.00');
    expect($column->formatState(2500001))->toEqual('€25,000.01');
});

it('formats money column state with sek', function () {
    $column = MoneyColumn::make('price')->currency('SEK')->locale('sv_SE');
    expect($column->formatState(2500))->toEqual(replaceNonBreakingSpaces('25,00 kr'));
    expect($column->formatState(250000))->toEqual(replaceNonBreakingSpaces('2 500,00 kr'));
    expect($column->formatState(2500001))->toEqual(replaceNonBreakingSpaces('25 000,01 kr'));
});

it('formats money column state to short format with default currency (USD)', function () {
    $column = MoneyColumn::make('price')->short();
    expect($column->formatState(250))->toEqual('$2.50');
    expect($column->formatState(250056))->toEqual('$2.50K');
    expect($column->formatState(24604231))->toEqual('$246.04K');
    expect($column->formatState(2460523122))->toEqual('$24.61M');
});

it('formats money column state to short format with sek', function () {
    $column = MoneyColumn::make('price')->currency('SEK')->locale('sv_SE')->short();
    expect($column->formatState(651))->toEqual(replaceNonBreakingSpaces('6,51 kr'));
    expect($column->formatState(235235))->toEqual(replaceNonBreakingSpaces('2,35K kr'));
    expect($column->formatState(23523562))->toEqual(replaceNonBreakingSpaces('235,24K kr'));
    // expect($column->formatState(23523562))->toEqual(replaceNonBreakingSpaces('235,24K kr'));
});

it('formats money column state to short format with sek and hide currency symbol', function () {
    $column = MoneyColumn::make('price')->currency('SEK')->locale('sv_SE')->short()->hideCurrencySymbol();
    expect($column->formatState(651))->toEqual(replaceNonBreakingSpaces('6,51'));
    expect($column->formatState(235235))->toEqual(replaceNonBreakingSpaces('2,35K'));
    expect($column->formatState(23523562))->toEqual(replaceNonBreakingSpaces('235,24K'));
    expect($column->formatState(2352356254))->toEqual(replaceNonBreakingSpaces('23,52M'));
});
