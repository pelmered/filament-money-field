<?php

namespace Pelmered\FilamentMoneyField\Forms\Components;

use Filament\Forms\Components\TextInput;
use Money\Currencies\ISOCurrencies;
use Money\Formatter\IntlMoneyFormatter;
use Money\Money;
use NumberFormatter;
use Pelmered\FilamentMoneyField\hasMoneyAttributes;
use Pelmered\FilamentMoneyField\MoneyFormatter;

class MoneyInput extends TextInput
{
    use hasMoneyAttributes;

    protected function setUp(): void
    {
        parent::setUp();

        $this->prefix(MoneyFormatter::getCurrencySymbol($this->getCurrency(), $this->getLocale()));

        $this->integer();
        /*
        $this->inputMode('numeric');
        $this->rule('numeric');
        $this->step(1);
        */
        $this->minValue = 0;



        $this->afterStateHydrated(static function (MoneyInput $component, $state): string {

            ray($component->locale, $component->currency, $state)->green();
            $currencies = new ISOCurrencies();
            $numberFormatter = new NumberFormatter($component->locale, NumberFormatter::CURRENCY);
            $moneyFormatter = new IntlMoneyFormatter($numberFormatter, $currencies);

            $money = new Money($state, $component->currency);
            return $moneyFormatter->format($money);
        });

    }
}
