<?php

namespace Pelmered\FilamentMoneyField\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Money\Currency;
use Money\Money;
use Spatie\LaravelData\Casts\Cast;

/**
 * @implements CastsAttributes<Currency, Currency>
 */
class CurrencyCast implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  ?non-empty-string  $value
     * @param  array<string, mixed>  $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): ?Currency
    {
        if ($value === null) {
            return null;
        }

        return new Currency($value);
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  Currency|string  $value
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): string
    {
        if ($value instanceof Currency) {
            return $value->getCode();
        }

        return $value;
    }
}
