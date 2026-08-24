<?php

namespace Pelmered\FilamentMoneyField\Concerns;

use Closure;
use Illuminate\Database\Eloquent\Model;
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

    public function getCurrency(): Currency
    {
        // The record's own currency comes first: it is the one its stored amount was
        // written in, so it is the one that describes the number being displayed. A
        // currency set on the field says what an amount with none of its own is counted
        // in - a new record, or a table without a currency column.
        $storedCurrency = $this->getStoredCurrency();

        if (! blank($storedCurrency)) {
            return Currency::fromCode((string) $storedCurrency);
        }

        return $this->currency ?? MoneyFormatter::getDefaultCurrency();
    }

    public function getLocale(): string
    {
        return $this->locale ?? (config('filament-money-field.default_locale') ?: app()->getLocale());
    }

    /**
     * A schema component that was never attached to a container - a validation rule built
     * from a bare field, say - has no record to ask, and Filament's getRecord() throws on
     * the uninitialized container rather than saying so. A table column has no container
     * to begin with and answers for itself.
     *
     * @return Model|array<string, mixed>|null
     */
    private function getRecordOrNull(): Model|array|null
    {
        if (property_exists($this, 'container') && ! isset($this->container)) {
            return null;
        }

        return $this->getRecord();
    }

    /**
     * Filament hands back either a model or a plain array as the record, so the currency
     * column is read the way the record it came from allows. Reading an array with `->`
     * yields a warning and no value, which would silently report the default currency for
     * a record that names one.
     */
    private function getStoredCurrency(): mixed
    {
        $record = $this->getRecordOrNull();
        $column = $this->getCurrencyColumn();

        if (is_array($record)) {
            return $record[$column] ?? null;
        }

        return $record?->{$column};
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

    protected function getCurrencyColumn(): string
    {
        return $this->currencyColumn ?? $this->getCurrencyColumnDefault();
    }

    protected function getCurrencyColumnDefault(): string
    {
        return $this->getName().config('larapara.currency_column_suffix', '_currency');
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

    private function getDecimals(): ?int
    {
        if (! is_null($this->decimals)) {
            return $this->decimals;
        }

        $decimals = config('filament-money-field.decimal_digits');

        // An absent MONEY_DECIMAL_DIGITS gives null, an empty one gives '', and both mean
        // "leave the decimals to the currency" - (int) '' would pin every amount to 0.
        return blank($decimals) ? null : (int) $decimals;
    }

    /**
     * LaraPara asks for precision by name and refuses a negative number of decimals.
     * A negative decimal_digits is this package's way of asking for significant digits,
     * so it moves over to the parameter that means that. Null for both leaves the
     * decimals to the minor unit of the currency.
     *
     * @return array{0: int|null, 1: int|null} decimals, significant digits
     */
    private function getPrecision(): array
    {
        $decimals = $this->getDecimals();

        return ! is_null($decimals) && $decimals < 0
            ? [null, abs($decimals)]
            : [$decimals, null];
    }

    /**
     * LaraPara names its formatters for what they take: a Money carries the currency
     * that scales it, while a bare amount is minor units and needs the field's.
     */
    protected function formatMoney(Money|int|string $state, bool $showCurrencySymbol = true): string
    {
        [$decimals, $significantDigits] = $this->getPrecision();

        if ($state instanceof Money) {
            return MoneyFormatter::format(
                $state,
                $this->getLocale(),
                decimals: $decimals,
                significantDigits: $significantDigits,
                showCurrencySymbol: $showCurrencySymbol,
            );
        }

        return MoneyFormatter::formatFromMinor(
            $state,
            $this->getCurrency(),
            $this->getLocale(),
            decimals: $decimals,
            significantDigits: $significantDigits,
            showCurrencySymbol: $showCurrencySymbol,
        );
    }

    protected function formatMoneyShort(Money|int|string $state, bool $showCurrencySymbol = true): string
    {
        [$decimals, $significantDigits] = $this->getPrecision();

        if ($state instanceof Money) {
            return MoneyFormatter::formatShort(
                $state,
                $this->getLocale(),
                decimals: $decimals,
                significantDigits: $significantDigits,
                showCurrencySymbol: $showCurrencySymbol,
            );
        }

        return MoneyFormatter::formatShortFromMinor(
            $state,
            $this->getCurrency(),
            $this->getLocale(),
            decimals: $decimals,
            significantDigits: $significantDigits,
            showCurrencySymbol: $showCurrencySymbol,
        );
    }

    // This should typically be provided by the Filament\Support\Concerns\EvaluatesClosures trait in Filament
    abstract protected function evaluate(string|Closure|null $value): mixed;
}
