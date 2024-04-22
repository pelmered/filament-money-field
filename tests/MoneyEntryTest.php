<?php

namespace Pelmered\FilamentMoneyField\Tests;

use JetBrains\PhpStorm\NoReturn;
use Pelmered\FilamentMoneyField\Infolists\Components\MoneyEntry;

class MoneyEntryTest extends TestCase
{
    public function testInfoListMoneyFormat(): void
    {
        $this->markTestSkipped('Not working yet');

        $moneyEntry = (new MoneyEntry('name'))->currency('SEK')->locale('sv_SE');

        self::callMethod($moneyEntry, 'setUp', []);

        $state = 1000000;

        $value = $moneyEntry->evaluate(self::getProperty($moneyEntry, 'formatStateUsing'), [
            'state' => $state,
        ]);

        $this->assertEquals('10000,00 kr', $value);

        $this->assertEquals('10000,00 kr', $moneyEntry->formatState('1000000'));
    }

}
