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
    public static function isValid(Currency $currency)
    {
        return static::getAvailableCurrencies()->contains($currency);
    }

    public static function isValidCode(string $currencyCode)
    {
        return static::isValid(Currency::fromCode($currencyCode));
    }

    public static function getAvailableCurrencies(): CurrencyCollection
    {
        $config = Config::get('filament-money-field.currency_cache', [
            'type' => false,
            'ttl'  => 0,
        ]);

        $callback = function () {
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
    private static function loadAvailableCurrencies(): CurrencyCollection
    {
        $currencyProvider    = Config::get('filament-money-field.currency_provider', ISOCurrenciesProvider::class);
        $availableCurrencies = Config::get('filament-money-field.available_currencies', []);

        $currencies = app()->make($currencyProvider)->loadCurrencies();

        if (! $availableCurrencies) {
            $availableCurrencies = array_keys($currencies);
        }

        if (Config::get('filament-money-field.load_crypto_currencies', false)) {
            $availableCurrencies = array_merge(
                array_keys($currencies),
                app()->make(CryptoCurrenciesProvider::class)->loadCurrencies()
            );
        }

        return new CurrencyCollection(Arr::mapWithKeys($availableCurrencies, function ($currencyCode) use ($currencies) {
            return [
                $currencyCode => new Currency(
                    strtoupper($currencyCode),
                    $currencies[$currencyCode]['currency'],
                    $currencies[$currencyCode]['minorUnit'],
                ),
            ];
        }));
    }
}
