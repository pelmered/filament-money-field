<?php

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Mockery as M;
use Pelmered\FilamentMoneyField\Currencies\Currency;
use Pelmered\FilamentMoneyField\Currencies\CurrencyCollection;
use Pelmered\FilamentMoneyField\Currencies\CurrencyRepository;
use Pelmered\FilamentMoneyField\Currencies\Providers\ISOCurrenciesProvider;

beforeEach(function (): void {
    // Clear cache between tests
    Cache::flush();
    Cache::shouldReceive('remember')->andReturnUsing(fn ($key, $ttl, $callback) => $callback());
    Cache::shouldReceive('flexible')->andReturnUsing(fn ($key, $ttl, $callback) => $callback());
    Cache::shouldReceive('forever')->andReturnUsing(fn ($key, $callback) => $callback());
});

it('checks if a currency is valid', function (): void {
    // Configure available currencies
    Config::set('filament-money-field.available_currencies', ['USD']);

    // Create test currencies
    $validCurrency   = new Currency('USD', 'US Dollar', 2);
    $invalidCurrency = new Currency('EUR', 'Euro', 2);

    // Test with a valid currency
    expect(CurrencyRepository::isValid($validCurrency))->toBeTrue();

    // Test with an invalid currency
    expect(CurrencyRepository::isValid($invalidCurrency))->toBeFalse();
});

it('checks if a currency code is valid', function (): void {
    Config::set('filament-money-field.available_currencies', ['USD']);
    expect(CurrencyRepository::isValidCode('USD'))->toBeTrue();
});

it('checks if a currency code is invalid', function (): void {
    Config::set('filament-money-field.available_currencies', ['USD']);
    expect(CurrencyRepository::isValidCode('EUR'))->toBeFalse();
});

it('loads available currencies without caching', function (): void {
    // Configure to not use cache
    Config::set('filament-money-field.currency_cache.type', false);
    Config::set('filament-money-field.currency_provider', ISOCurrenciesProvider::class);
    Config::set('filament-money-field.available_currencies', ['USD', 'EUR']);

    // Mock the ISOCurrenciesProvider
    $mockProvider = M::mock(ISOCurrenciesProvider::class);
    $mockProvider->shouldReceive('loadCurrencies')
        ->andReturn([
            'USD' => ['alphabeticCode' => 'USD', 'currency' => 'US Dollar', 'minorUnit' => 2, 'numericCode' => 840],
            'EUR' => ['alphabeticCode' => 'EUR', 'currency' => 'Euro', 'minorUnit' => 2, 'numericCode' => 978],
        ]);
    app()->instance(ISOCurrenciesProvider::class, $mockProvider);

    $currencies = CurrencyRepository::getAvailableCurrencies();

    expect($currencies)->toBeInstanceOf(CurrencyCollection::class)
        ->and($currencies->count())->toBe(2)
        ->and($currencies->has('USD'))->toBeTrue()
        ->and($currencies->has('EUR'))->toBeTrue()
        ->and($currencies->get('USD')->name)->toBe('US Dollar')
        ->and($currencies->get('EUR')->name)->toBe('Euro');
});

it('caches currencies with remember', function (): void {
    // Configure to use remember cache
    Config::set('filament-money-field.currency_cache.type', 'remember');
    Config::set('filament-money-field.currency_cache.ttl', 60);
    Config::set('filament-money-field.currency_provider', ISOCurrenciesProvider::class);
    Config::set('filament-money-field.available_currencies', ['USD', 'EUR']);

    // Mock the ISOCurrenciesProvider
    $mockProvider = M::mock(ISOCurrenciesProvider::class);
    $mockProvider->shouldReceive('loadCurrencies')
        ->andReturn([
            'USD' => ['alphabeticCode' => 'USD', 'currency' => 'US Dollar', 'minorUnit' => 2, 'numericCode' => 840],
            'EUR' => ['alphabeticCode' => 'EUR', 'currency' => 'Euro', 'minorUnit' => 2, 'numericCode' => 978],
        ]);
    app()->instance(ISOCurrenciesProvider::class, $mockProvider);

    $currencies = CurrencyRepository::getAvailableCurrencies();

    expect($currencies)->toBeInstanceOf(CurrencyCollection::class)
        ->and($currencies->count())->toBe(2);
});

it('caches currencies with flexible', function (): void {
    // Configure to use flexible cache
    Config::set('filament-money-field.currency_cache.type', 'flexible');
    Config::set('filament-money-field.currency_cache.ttl', [60, 3600]);
    Config::set('filament-money-field.currency_provider', ISOCurrenciesProvider::class);
    Config::set('filament-money-field.available_currencies', ['USD', 'EUR']);

    // Mock the ISOCurrenciesProvider
    $mockProvider = M::mock(ISOCurrenciesProvider::class);
    $mockProvider->shouldReceive('loadCurrencies')
        ->andReturn([
            'USD' => ['alphabeticCode' => 'USD', 'currency' => 'US Dollar', 'minorUnit' => 2, 'numericCode' => 840],
            'EUR' => ['alphabeticCode' => 'EUR', 'currency' => 'Euro', 'minorUnit' => 2, 'numericCode' => 978],
        ]);
    app()->instance(ISOCurrenciesProvider::class, $mockProvider);

    $currencies = CurrencyRepository::getAvailableCurrencies();

    expect($currencies)->toBeInstanceOf(CurrencyCollection::class)
        ->and($currencies->count())->toBe(2);
});

it('caches currencies forever', function (): void {
    // Configure to use forever cache
    Config::set('filament-money-field.currency_cache.type', 'forever');
    Config::set('filament-money-field.currency_provider', ISOCurrenciesProvider::class);
    Config::set('filament-money-field.available_currencies', ['USD', 'EUR']);

    // Mock the ISOCurrenciesProvider
    $mockProvider = M::mock(ISOCurrenciesProvider::class);
    $mockProvider->shouldReceive('loadCurrencies')
        ->andReturn([
            'USD' => ['alphabeticCode' => 'USD', 'currency' => 'US Dollar', 'minorUnit' => 2, 'numericCode' => 840],
            'EUR' => ['alphabeticCode' => 'EUR', 'currency' => 'Euro', 'minorUnit' => 2, 'numericCode' => 978],
        ]);
    app()->instance(ISOCurrenciesProvider::class, $mockProvider);

    $currencies = CurrencyRepository::getAvailableCurrencies();

    expect($currencies)->toBeInstanceOf(CurrencyCollection::class)
        ->and($currencies->count())->toBe(2);
});

it('loads all available currencies when none specified', function (): void {
    Config::set('filament-money-field.currency_cache.type', false);
    Config::set('filament-money-field.currency_provider', ISOCurrenciesProvider::class);
    Config::set('filament-money-field.available_currencies', []);

    // Mock the ISOCurrenciesProvider with more currencies
    $mockProvider = M::mock(ISOCurrenciesProvider::class);
    $mockProvider->shouldReceive('loadCurrencies')
        ->andReturn([
            'USD' => ['alphabeticCode' => 'USD', 'currency' => 'US Dollar', 'minorUnit' => 2, 'numericCode' => 840],
            'EUR' => ['alphabeticCode' => 'EUR', 'currency' => 'Euro', 'minorUnit' => 2, 'numericCode' => 978],
            'GBP' => ['alphabeticCode' => 'GBP', 'currency' => 'British Pound', 'minorUnit' => 2, 'numericCode' => 826],
        ]);
    app()->instance(ISOCurrenciesProvider::class, $mockProvider);

    $currencies = CurrencyRepository::getAvailableCurrencies();

    expect($currencies)->toBeInstanceOf(CurrencyCollection::class)
        ->and($currencies->count())->toBe(3)
        ->and($currencies->has('USD'))->toBeTrue()
        ->and($currencies->has('EUR'))->toBeTrue()
        ->and($currencies->has('GBP'))->toBeTrue();
});

it('loads crypto currencies when enabled', function (): void {
    Config::set('filament-money-field.currency_cache.type', false);
    Config::set('filament-money-field.currency_provider', ISOCurrenciesProvider::class);
    Config::set('filament-money-field.available_currencies', []);
    Config::set('filament-money-field.load_crypto_currencies', true);

    // Using regular provider to test actual functionality
    $currencies = CurrencyRepository::getAvailableCurrencies();

    expect($currencies)->toBeInstanceOf(CurrencyCollection::class)
        ->and($currencies->has('USD'))->toBeTrue()
        ->and($currencies->has('EUR'))->toBeTrue();
});
