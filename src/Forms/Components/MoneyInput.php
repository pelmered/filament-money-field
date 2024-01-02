<?php

namespace Pelmered\FilamentMoneyField\Forms\Components;

use Filament\Forms\Components\TextInput;
use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\Formatter\IntlMoneyFormatter;
use Money\Money;
use NumberFormatter;

class MoneyInput extends TextInput
{
    protected string | \Closure $prefix = '';
    protected string | \Closure $suffix = '';
    protected Currency | \Closure $currency;
    protected string $locale;


    protected function setUp(): void
    {
        parent::setUp();

        $this->prefix('$');

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

    public function locale(string $locale = 'en_US'): static
    {
        $this->locale = $locale;

        return $this;
    }

    public function currency(string $currencyCode = 'USD'): static
    {
        $currency = new Currency($currencyCode);
        $currencies = new ISOCurrencies();
        $currencies->contains($currency);

        $this->currency = $currency;

        return $this;
    }


    /*
    public function __construct(string $name)
    {
        parent::__construct($name);
        $this->prefix('$');
        $this->type('number');
        $this->step('0.01');
        $this->min('0');
    }
    */
}
