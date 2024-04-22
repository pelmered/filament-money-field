<?php

namespace Pelmered\FilamentMoneyField\Tests;

use Pelmered\FilamentMoneyField\Tables\Columns\MoneyColumn;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(MoneyColumn::class)]
class MoneyColumnTest extends TestCase
{
    public function testMoneyColumn(): void
    {
        $column = MoneyColumn::make('price');
        $this->assertEquals('$25.00', $column->formatState(2500));
        $this->assertEquals('$2,500.00', $column->formatState(250000));
        $this->assertEquals('$25,000.01', $column->formatState(2500001));
    }

    public function testMoneyColumnWithCurrency(): void
    {
        $column = MoneyColumn::make('price')->currency('EUR');
        $this->assertEquals('€25.00', $column->formatState(2500));
        $this->assertEquals('€2,500.00', $column->formatState(250000));
        $this->assertEquals('€25,000.01', $column->formatState(2500001));
    }
}
