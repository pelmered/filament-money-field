<?php

namespace Pelmered\FilamentMoneyField\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Money\Currency as MoneyCurrency;
use Money\Money;
use Pelmered\FilamentMoneyField\Currencies\Currency;

/**
 * @implements CastsAttributes<Money, Money>
 */
class MoneyCast implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  ?int  $value
     * @param  array<string, mixed>  $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): ?Money
    {
        if ($value === null) {
            return null;
        }

        $currency = $this->getCurrencyFromModel($model, $key);

        $value = (int) (config('filament-money-field.store.format') === 'decimal'
            ? $value * 10 ** $this->getDecimals($currency->getCode())
            : $value);

        return new Money($value, $currency);
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  Money|string|null  $value
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): array
    {
        $amount   = $this->getAmount($model, $key, $value);
        $currency = $this->getCurrency($model, $key, $value);

        return [
            $key => config('filament-money-field.store.format') === 'decimal'
                ? $amount / 10 ** $this->getDecimals($currency)
                : $amount,
            $key.config('currency_column_suffix') => $currency,
        ];
    }

    protected function getAmount($model, $key, $value): ?int
    {
        return match (true) {
            $value instanceof Money => $value->getAmount(),
            is_array($value)  => $value['amount'] ?? $value[0],
            default                 => $value,
        };
    }

    public function getCurrency($model, $key, $value): string
    {
        return match (true) {
            $value instanceof Money => $value->getCurrency(),
            is_array($value)        => $value['currency'] ?? $value[1],
            default                 => $this->getCurrencyFromModel($model, $key)->getCode(),
        };
    }

    public function getCurrencyFromModel(Model $model, string $name): MoneyCurrency
    {
        $currency = $model->{$name.config('currency_column_suffix')} ?? (string) (config('filament-money-field.default_currency'));

        return $currency instanceof MoneyCurrency ? $currency : new MoneyCurrency($currency);
    }

    public function getDecimals(string $currencyCode): int
    {
        $currency = Currency::fromCode($currencyCode);

        if ($currency->minorUnit) {
            return $currency->minorUnit;
        }

        return 2;
    }
}
