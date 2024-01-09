<?php
namespace Pelmered\FilamentMoneyField;

use Filament\Infolists\Infolist;
use Money\Currencies\ISOCurrencies;
use Money\Currency;

trait hasMoneyAttributes
{
    protected Currency $currency;
    protected string $locale = 'sv_SE';

    protected function getCurrency(): Currency
    {
        return $this->currency ?? $this->currency(config('filament-money-field.default_currency') ?? Infolist::$defaultCurrency)->getCurrency();
    }

    protected function getLocale(): string
    {
        return $this->locale ?? config('filament-money-field.default_locale');
    }

    public function currency(string|\Closure|null $currencyCode = null): static
    {
        $this->currency = new Currency($currencyCode);
        $currencies     = new ISOCurrencies();

        if (! $currencies->contains($this->currency)) {
            throw new \Exception('Currency not supported: '.$currencyCode);
        }

        return $this;
    }

    public function locale(string|\Closure|null $locale = null): static
    {
        $this->locale = $locale;

        return $this;
    }
}
