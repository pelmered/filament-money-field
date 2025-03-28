<?php

namespace Pelmered\FilamentMoneyField\Currencies;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Pelmered\FilamentMoneyField\Currencies\Providers\CryptoCurrenciesProvider;
use Pelmered\FilamentMoneyField\Currencies\Providers\ISOCurrenciesProvider;

class CurrencyRepository
{
    public static function isValid(Currency $currency): bool
    {
        return static::getAvailableCurrencies()->contains($currency);
    }

    public static function isValidCode(string $currencyCode): bool
    {
        return static::isValid(Currency::fromCode($currencyCode));
    }

    public static function getAvailableCurrencies(): CurrencyCollection
    {
        $config = Config::get('filament-money-field.currency_cache', [
            'type' => false,
            'ttl'  => 0,
        ]);

        $callback = function (): \Pelmered\FilamentMoneyField\Currencies\CurrencyCollection {
            return static::loadAvailableCurrencies();
        };

        return match ($config['type']) {
            'remember' => Cache::remember('filament_money_currencies', $config['ttl'], $callback),
            'flexible' => Cache::flexible('filament_money_currencies', $config['ttl'], $callback),
            'forever'  => Cache::forever('filament_money_currencies', $callback),
            default    => static::loadAvailableCurrencies(),
        };
    }

    /**
     * @throws BindingResolutionException
     */
    protected static function loadAvailableCurrencies(): CurrencyCollection
    {
        $currencyProvider    = Config::get('filament-money-field.currency_provider', ISOCurrenciesProvider::class);
        $availableCurrencies = Config::get('filament-money-field.available_currencies', []);

        $currencies = app()->make($currencyProvider)->loadCurrencies();

        if (Config::get('filament-money-field.load_crypto_currencies', false)) {
            $cryptoCurrencies = app()->make(CryptoCurrenciesProvider::class)->loadCurrencies();

            $currencies = array_merge(
                $currencies,
                $cryptoCurrencies
            );
        }

        if (! $availableCurrencies) {
            $availableCurrencies = array_keys($currencies);
        }

        return new CurrencyCollection(
            Arr::mapWithKeys($availableCurrencies,
                static function (string $currencyCode) use ($currencies) {
                    return [
                        $currencyCode => new Currency(
                            strtoupper($currencyCode),
                            $currencies[$currencyCode]['currency'] ?? '',
                            $currencies[$currencyCode]['minorUnit'],
                        ),
                    ];
                }));
    }
}
