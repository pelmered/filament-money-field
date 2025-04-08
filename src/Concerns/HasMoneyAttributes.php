<?php

namespace Pelmered\FilamentMoneyField\Concerns;

use Closure;
use Pelmered\LaraPara\Exceptions\UnsupportedCurrency;
use Pelmered\LaraPara\Currencies\Currency;
use Pelmered\LaraPara\Currencies\CurrencyRepository;
use Pelmered\LaraPara\MoneyFormatter\MoneyFormatter;

trait HasMoneyAttributes
{
    protected Currency $currency;

    protected ?string $currencyColumn = null;

    protected string $locale;

    protected ?int $decimals = null;

    protected ?bool $inMinor = null;

    public function getCurrency(): Currency
    {
        if (isset($this->currency)) {
            return $this->currency;
        }

        if ($this->getRecord()) {
            return Currency::fromCode($this->getRecord()->{$this->getCurrencyColumn()});
        }

        return MoneyFormatter::getDefaultCurrency();
    }

    public function getLocale(): string
    {
        return $this->locale ?? config('filament-money-field.default_locale');
    }

    public function currency(string|Closure $currencyCode): static
    {
        /** @var non-empty-string $currencyCode */
        $currencyCode = (string) $this->evaluate($currencyCode);
        $currency     = Currency::fromCode($currencyCode);

        if (! CurrencyRepository::isValid($currency)) {
            throw new UnsupportedCurrency($currency->getCode());
        }

        $this->currency = $currency;

        return $this;
    }

    protected function getInMinorUnits(): bool
    {
        if ($this->inMinor !== null) {
            return $this->inMinor;
        }

        $storeFormat = config('filament-money-field.store.format');

        return $storeFormat === 'int';
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

    protected function getCurrencyColumn(): string
    {
        return $this->currencyColumn ?? config('filament-money-field.default_currency_column');
    }

    public function currencyColumn(string|Closure $column): static
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
