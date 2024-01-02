<?php

namespace Pelmered\FilamentMoneyField\Infolists\Components;

use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\Formatter\IntlMoneyFormatter;
use Money\Money;

class MoneyEntry extends TextEntry
{
    protected Currency $currency;
    protected string $locale = 'sv_SE';
    protected int $divideBy = 100;

    protected function setUp(): void
    {
        parent::setUp();

        $this->isMoney = true;
        $this->numeric();

        $this->formatStateUsing(static function (MoneyEntry $component, $state): ?string {

            $currency = $component->getCurrency();
            $locale = $component->getLocale();

            $currencies = new ISOCurrencies();
            $numberFormatter = new \NumberFormatter($locale, \NumberFormatter::CURRENCY);
            $moneyFormatter = new IntlMoneyFormatter($numberFormatter, $currencies);

            $money = new Money($state, $currency);
            return $moneyFormatter->format($money);
        });
  }

    public function currency(string | \Closure | null $currencyCode = null): static
    {
        $this->currency = new Currency($currencyCode);
        $currencies = new ISOCurrencies();

        if(!$currencies->contains($this->currency)) {
            throw new \Exception('Currency not supported: '.$currencyCode);
        }

        return $this;
    }

    protected function getCurrency(): Currency
    {
        return $this->currency ?? $this->currency(config('app.currency') ?? Infolist::$defaultCurrency)->getCurrency();
    }

    protected function getLocale(): string
    {
        return $this->locale ?? config('app.locale');
        //return $this->locale ?? app()->getLocale();
    }

    public function locale(string | \Closure | null $locale = null): static
    {
        $this->locale = $locale;

        return $this;
    }

    public function divideBy(int | \Closure | null $divideBy = null): static
    {
        $this->divideBy = $divideBy;

        return $this;
    }
}
