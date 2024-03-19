<?php

namespace Pelmered\FilamentMoneyField;

use Filament\Infolists\Infolist;
use Money\Currencies\ISOCurrencies;
use Money\Currency;

trait hasMoneyAttributes
{
    /**
     * The currency of the money value.
     * 
     * @var Currency
     */
    protected Currency $currency;
    
    /**
     * The locale of the money value.
     * 
     * @var string
     */
    protected string $locale;

    /**
     * The decimal separator of the money value.
     * 
     * @var string
     */
    protected ?string $monetarySeparator = null;

    /**
     * Get the currency of the money value.
     * 
     * @return Currency
     */
    protected function getCurrency(): Currency
    {
        return $this->currency ?? $this->currency(config('filament-money-field.default_currency') ?? Infolist::$defaultCurrency)->getCurrency();
    }

    /**
     * Get the locale of the money value.
     * 
     * @return string
     */
    protected function getLocale(): string
    {
        return $this->locale ?? config('filament-money-field.default_locale');
    }

    /**
     * Get the decimal separator of the money value.
     * 
     * @return string
     */
    public function currency(string|\Closure|null $currencyCode = null): static
    {
        $this->currency = new Currency($currencyCode);
        $currencies = new ISOCurrencies();

        if (!$currencies->contains($this->currency)) {
            throw new \RuntimeException('Currency not supported: ' . $currencyCode);
        }

        return $this;
    }

    /**
     * Set the decimal separator of the money value.
     * 
     * @return string
     */
    public function locale(string|\Closure|null $locale = null): static
    {
        $this->locale = $locale;

        return $this;
    }
}
