<?php

namespace Pelmered\FilamentMoneyField\Concerns;

use Closure;
use Money\Money;
use Pelmered\LaraPara\Currencies\Currency;
use Pelmered\LaraPara\Currencies\CurrencyRepository;
use Pelmered\LaraPara\Exceptions\UnsupportedCurrency;
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

        $state = match (true) {
            $this instanceof \Pelmered\FilamentMoneyField\Forms\Components\MoneyInput => $this->getState(),
            $this instanceof \Pelmered\FilamentMoneyField\Forms\Components\MoneyColumn => $this->getState(),
            default => null,
        };
        if ($state instanceof Money) {
            return Currency::fromMoney($state);
        }

        if ($record = $this->getRecord()) {
            $currencyCode = $record->{$this->getCurrencyColumn()};
            if ($currencyCode) {
                return Currency::fromCode($currencyCode);
            }
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

        $storeFormat = config('larapara.store.format');

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
        return $this->currencyColumn ?? $this->getCurrencyColumnDefault();
    }

    protected function getCurrencyColumnDefault(): string
    {
        return $this->getName().config('larapara-filament-money-field.currency_column_suffix', '_currency');
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
