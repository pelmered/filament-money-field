<?php

use Pelmered\FilamentMoneyField\Tables\Columns\MoneyColumn;
use Pelmered\FilamentMoneyField\Tests\TestCase;

class MoneyColumnTest extends TestCase
{
    public function testMoneyColumnn(): void
    {
        $column = MoneyColumn::make('price');
        $this->assertEquals('$25.00', $column->formatState(2500));
        $this->assertEquals('$2,500.00', $column->formatState(250000));
        $this->assertEquals('$25,000.01', $column->formatState(2500001));
    }
}
