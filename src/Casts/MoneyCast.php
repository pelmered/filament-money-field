<?php

namespace Pelmered\FilamentMoneyField\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Money\Currency as MoneyCurrency;
use Money\Money;
use Pelmered\FilamentMoneyField\Currencies\Currency;
use Pelmered\FilamentMoneyField\MoneyFormatter\MoneyFormatter;
use PhpStaticAnalysis\Attributes\Param;
use PhpStaticAnalysis\Attributes\Returns;

/**
 * @implements CastsAttributes<Money, Money>
 */
class MoneyCast implements CastsAttributes
{
    /**
     * Cast the given value.
     */
    #[Param(value: '?int')]
    #[Param(attributes: 'array<string, mixed>')]
    public function get(Model $model, string $key, mixed $value, array $attributes): ?Money
    {
        if ($value === null) {
            return null;
        }

        $currency = $this->getCurrencyFromModel($model, $key);

        $value = (int) (config('filament-money-field.store.format') === 'decimal'
            // ? $value * 10 ** $this->getDecimals($currency->getCode() ?? MoneyFormatter::getDefaultCurrency()->getCode())
            ? $value * 10 ** $this->getDecimals($currency->getCode())
            : $value);

        return new Money($value, $currency);
    }

    /**
     * Prepare the given value for storage.
     */
    #[Param(value: 'Money|string')]
    #[Param(attributes: 'array<string, mixed>')]
    #[Returns('array<string, int|float|string|null>')]
    public function set(Model $model, string $key, mixed $value, array $attributes): array
    {
        $amount   = $this->getAmount($model, $key, $value);
        $currency = $this->getCurrency($model, $key, $value);

        return [
            $key => config('filament-money-field.store.format') === 'decimal'
                ? $amount / 10 ** $this->getDecimals($currency)
                : $amount,
            $key.config('filament-money-field.currency_column_suffix', '_currency') => $currency,
        ];
    }

    #[Param(value: 'array{0?: int, 1?: string, amount?: int, currency?: string}|Money|int|string|null')]
    #[Returns('int|null')]
    protected function getAmount(Model $model, string $key, Money|array|int|string|null $value): ?int
    {
        $amount = match (true) {
            $value instanceof Money => $value->getAmount(),
            is_array($value)        => $value['amount'] ?? $value[0] ?? null,
            default                 => $value,
        };

        return $amount !== null ? (int) $amount : null;
    }

    #[Param(value: 'array{0?: int, 1?: string, amount?: int, currency?: string}|Money|int|string|null')]
    protected function getCurrency(Model $model, string $key, Money|array|int|string|null $value): string
    {
        return match (true) {
            $value instanceof Money => $value->getCurrency(),
            is_array($value)        => $value['currency'] ?? $value[1] ?? null,
            default                 => $this->getCurrencyFromModel($model, $key)->getCode(),
        } ?? MoneyFormatter::getDefaultCurrency()->getCode();
    }

    protected function getCurrencyFromModel(Model $model, string $name): MoneyCurrency
    {
        $currency = $model->{$name.config('filament-money-field.currency_column_suffix', '_currency')} ?? (string) (config('filament-money-field.default_currency'));

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
