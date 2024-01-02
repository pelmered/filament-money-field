<?php
namespace Pelmered\FilamentMoneyField\Tests;

use Pelmered\FilamentMoneyField\Infolists\Components\MoneyEntry;

class MoneyEntryTest extends TestCase
{
    public function testInfoListMoneyFormat()
    {
        $this->markTestSkipped('Not implemented yet');

        $moneyEntry = (new MoneyEntry('name'))->currency('SEK')->locale('sv_SE');

        $moneyEntry->initMoneyEntry();

        //MoneyTest::callMethod($moneyEntry, 'setUp', []);
        //$moneyEntry->__call('setUp', []);

        $state = 1000000;

        dd($moneyEntry->formatState('1000000'));

        $value = $moneyEntry->evaluate($moneyEntry->formatStateUsing, [
            'state' => $state,
        ]);

        dd($value);

        $this->assertEquals('10000,00 kr', $moneyEntry->formatState('1000000'));
    }
}
