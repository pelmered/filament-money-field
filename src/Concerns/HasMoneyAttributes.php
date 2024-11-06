<?php

namespace Pelmered\FilamentMoneyField\Concerns;

use Closure;
use Filament\Infolists\Infolist;
use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Pelmered\FilamentMoneyField\Exceptions\UnsupportedCurrency;

trait HasMoneyAttributes
{
    protected Currency $currency;

    protected string $locale;

    protected ?int $decimals = null;

    protected ?string $monetarySeparator = null;

    public function getCurrency(): Currency
    {
        return $this->currency ?? $this->getDefaultCurrency();
    }

    protected function getDefaultCurrency(): Currency
    {
        $defaultCurrencyCode = (string) (config('filament-money-field.default_currency') ?? Infolist::$defaultCurrency);

        return $this->currency($defaultCurrencyCode)->getCurrency();
    }

    public function getLocale(): string
    {
        return $this->locale ?? config('filament-money-field.default_locale');
    }

    public function currency(string|Closure $currencyCode): static
    {
        /** @var non-empty-string $currencyCode */
        $currencyCode   = (string) $this->evaluate($currencyCode);
        $this->currency = new Currency($currencyCode);
        $currencies     = new ISOCurrencies;

        if (! $currencies->contains($this->currency)) {
            throw new UnsupportedCurrency($currencyCode);
        }

        return $this;
    }

    public function locale(string|Closure|null $locale = null): static
    {
        $this->locale = $this->evaluate($locale);

        return $this;
    }

    public function decimals(int|Closure $decimals): static
    {
        $this->decimals = $this->evaluate($decimals);

        return $this;
    }

    private function getDecimals(): int
    {
        if (! is_null($this->decimals)) {
            return $this->decimals;
        }

        return (int) config('filament-money-field.decimal_digits', 2);
    }

    // This should typically be provided by the Filament\Support\Concerns\EvaluatesClosures trait in Filament
    abstract protected function evaluate(string|Closure|null $value): mixed;
}
