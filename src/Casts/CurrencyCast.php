<?php

namespace Pelmered\FilamentMoneyField\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Money\Money;
use Pelmered\FilamentMoneyField\Currencies\Currency;
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
    public function get(Model $model, string $key, mixed $value, array $attributes)//: Currency|\Money\Currency|null
    {
        if ($value === null) {
            return null;
        }

        $c = match(config('filament-money-field.currency_cast_to')) {
            \Money\Currency::class => new \Money\Currency($value),
            default => Currency::fromCode($value)
        };

        return $c->getCode();
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  Currency|string  $value
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof \Money\Currency) {
            return $value->getCode();
        }
        if ($value instanceof Currency) {
            return $value->getCode();
        }

        return (string) $value;
    }
}
