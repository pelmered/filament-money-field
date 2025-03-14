<?php
namespace Pelmered\FilamentMoneyField\Currencies;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Money\Currencies\ISOCurrencies;
use Money\Currency as MoneyCurrency;
use Pelmered\FilamentMoneyField\Exceptions\UnsupportedCurrency;
use PhpStaticAnalysis\Attributes\Param;
use PhpStaticAnalysis\Attributes\Type;
use RuntimeException;


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

        return match($config['type']) {
            'remember' => Cache::remember('filament_money_currencies', $config['ttl'], $callback),
            'flexible' => Cache::flexible('filament_money_currencies', $config['ttl'], $callback),
            'forever'  => Cache::forever('filament_money_currencies', $callback),
            default    => static::loadAvailableCurrencies(),
        };
    }

    private static function loadAvailableCurrencies(): CurrencyCollection
    {
        $currencyProvider    = Config::get('filament-money-field.currency_provider', ISOCurrencies::class);
        $availableCurrencies = Config::get('filament-money-field.available_currencies', []);

        $isoCurrencies = static::loadISOCurrencies();

        if (! $availableCurrencies) {
            $availableCurrencies = array_keys($isoCurrencies);
        }

        if (Config::get('filament-money-field.load_crypto_currencies', false)) {
            $availableCurrencies = array_merge(array_keys($isoCurrencies), static::loadCryptoCurrencies());
        }

        return new CurrencyCollection(Arr::mapWithKeys($availableCurrencies,function ($currencyCode) use ($isoCurrencies) {
            return [
                $currencyCode => new Currency(
                    strtoupper($currencyCode),
                    $isoCurrencies[$currencyCode]['currency'],
                    $isoCurrencies[$currencyCode]['minorUnit'],
                )
            ];
        }));
    }

    #[Type('array<string, array{alphabeticCode: string, currency: string, minorUnit: int, numericCode: int}>')]
    private static function loadISOCurrencies(): array
    {
        $file = base_path('vendor/moneyphp/money/resources/currency.php');

        if (is_file($file)) {
            return require $file;
        }

        throw new RuntimeException('Failed to load ISO currencies.');
    }

    #[Type('array<string, array{alphabeticCode: string, currency: string, minorUnit: int, numericCode: int}>')]
    private static function loadCryptoCurrencies(): array
    {
        $file = base_path('vendor/moneyphp/money/resources/binance.php');

        if (is_file($file)) {
            return require $file;
        }

        throw new RuntimeException('Failed to load crypto currencies.');
    }
}
