<?php

namespace Pelmered\FilamentMoneyField\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Money\Currency;
use Money\Money;
use Spatie\LaravelData\Casts\Cast;

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

        return new Money($value, $this->getCurrencyFromModel($model, $key));
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  Money|string|null  $value
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes)
    {
        //return $this->getAmount($model, $key, $value);

        /*
        ray([
            $key => $this->getAmount($model, $key, $value),
            $key.'_currency' => $this->getCurrency($model, $key, $value),
        ]);
        */


        return [
            $key => $this->getAmount($model, $key, $value),
            $key.'_currency' => $this->getCurrency($model, $key, $value),
        ];

        return $value;
    }

    protected function getAmount($model, $key, $value): ?int
    {
        return match (true) {
            $value instanceof Money => $value->getAmount(),
            is_array($value) => $value['amount'] ?? $value[0],
            default => $value,
        };
    }

    public function getCurrency($model, $key, $value): string
    {
        return match (true) {
            $value instanceof Money => $value->getCurrency(),
            is_array($value) => $value['currency'] ?? $value[1],
            default => $this->getCurrencyFromModel($model, $key)->getCode(),
        };
    }

    public function getCurrencyFromModel(Model $model, string $name): Currency
    {
        $currency = $model->{$name.'_currency'} ?? (string) (config('filament-money-field.default_currency'));

        return $currency instanceof Currency ? $currency : new Currency($currency);
    }
}
