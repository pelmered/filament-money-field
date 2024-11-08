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
    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if ($value instanceof Money) {
            //$model->{$key.'_currency'} = $value->getCurrency();
            return $value->getAmount();
        }

        return $value;
    }

    public function getCurrencyFromModel(Model $model, string $name): Currency
    {
        $currency = $model->{$name.'_currency'} ?? (string) (config('filament-money-field.default_currency'));

        return $currency instanceof Currency ? $currency : new Currency($currency);
    }
}
