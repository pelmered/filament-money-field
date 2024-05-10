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
}
