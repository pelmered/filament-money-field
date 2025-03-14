<?php

namespace Pelmered\FilamentMoneyField\Concerns;

use Closure;
use Filament\Infolists\Infolist;
use Money\Currencies\ISOCurrencies;
use Pelmered\FilamentMoneyField\Currencies\Currency;
use Pelmered\FilamentMoneyField\Currencies\CurrencyRepository;
use Pelmered\FilamentMoneyField\Exceptions\UnsupportedCurrency;

trait HasMoneyAttributes
{
    protected Currency $currency;
    protected string $currencyColumn;

    protected string $locale;

    protected ?int $decimals = null;

    protected bool $inMinor = true;

    protected ?string $monetarySeparator = null;

    public function getCurrency(): Currency
    {
        //dd($this->currencyColumn, $this->getRecord(), $this->getRecord()->{$this->currencyColumn});

        return $this->currency
               //?? Currency::fromCode($this->getRecord()->{$this->currencyColumn})
               ?? ($this->getRecord() && isset($this->currencyColumn) ? Currency::fromCode($this->getRecord()->{$this->currencyColumn}) : null)
               ?? $this->getDefaultCurrency();
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
        $currency = Currency::fromCode($currencyCode);

        if (!CurrencyRepository::isValid($currency))
        {
            throw new UnsupportedCurrency($currency->getCode());
        }

        $this->currency = $currency;

        return $this;
    }

    public function inMajorUnits(): static
    {
        $this->inMinor = false;

        return $this;
    }

    public function notInMinor(): static
    {
        return $this->inMajorUnits();
    }

    public function inMinor(): static
    {
        $this->inMinor = true;

        return $this;
    }

    public function currencyColumn(string | Closure $column): static
    {
        $this->currencyColumn = $this->evaluate($column);

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
