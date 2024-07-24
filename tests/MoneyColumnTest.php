<?php

namespace Pelmered\FilamentMoneyField\Tests;

use Pelmered\FilamentMoneyField\Tables\Columns\MoneyColumn;

class MoneyColumnTest extends TestCase
{
    public function testMoneyColumn(): void
    {
        $column = MoneyColumn::make('price');
        $this->assertEquals('$25.00', $column->formatState(2500));
        $this->assertEquals('$2,500.00', $column->formatState(250000));
        $this->assertEquals('$25,000.01', $column->formatState(2500001));
    }

    public function testMoneyColumnWithEur(): void
    {
        $column = MoneyColumn::make('price')->currency('EUR');
        $this->assertEquals('€25.00', $column->formatState(2500));
        $this->assertEquals('€2,500.00', $column->formatState(250000));
        $this->assertEquals('€25,000.01', $column->formatState(2500001));
    }

    public function testMoneyColumnWithSek(): void
    {
        $column = MoneyColumn::make('price')->currency('SEK')->locale('sv_SE');
        $this->assertEquals(static::replaceNonBreakingSpaces('25,00 kr'), $column->formatState(2500));
        $this->assertEquals(static::replaceNonBreakingSpaces('2 500,00 kr'), $column->formatState(250000));
        $this->assertEquals(static::replaceNonBreakingSpaces('25 000,01 kr'), $column->formatState(2500001));
    }

    public function testMoneyColumnShort(): void
    {
        $column = MoneyColumn::make('price')->short();
        $this->assertEquals('$2.50', $column->formatState(250));
        $this->assertEquals('$2.50K', $column->formatState(250056));
        $this->assertEquals('$0.25M', $column->formatState(24604231));
    }

    public function testMoneyColumnShortSek(): void
    {
        $column = MoneyColumn::make('price')->currency('SEK')->locale('sv_SE')->short();
        $this->assertEquals(static::replaceNonBreakingSpaces('6,51 kr'), $column->formatState(651));
        $this->assertEquals(static::replaceNonBreakingSpaces('2,35K kr'), $column->formatState(235235));
        $this->assertEquals(static::replaceNonBreakingSpaces('0,24M kr'), $column->formatState(23523562));
    }
}
