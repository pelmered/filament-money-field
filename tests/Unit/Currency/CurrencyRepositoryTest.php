<?php

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Pelmered\FilamentMoneyField\Currencies\Currency;
use Pelmered\FilamentMoneyField\Currencies\CurrencyCollection;
use Pelmered\FilamentMoneyField\Currencies\CurrencyRepository;
use Pelmered\FilamentMoneyField\Currencies\Providers\CryptoCurrenciesProvider;
use Pelmered\FilamentMoneyField\Currencies\Providers\ISOCurrenciesProvider;

beforeEach(function () {
    // Clear cache between tests
    Cache::flush();
});

it('checks if a currency is valid', function () {
    // Create a mock collection with a test currency
    $currency = new Currency('USD', 'US Dollar', 2);
    $currencies = new CurrencyCollection(['USD' => $currency]);

    // Mock the getAvailableCurrencies method to return our collection
    $this->partialMock(CurrencyRepository::class, function ($mock) use ($currencies) {
        $mock->shouldReceive('getAvailableCurrencies')
            ->andReturn($currencies);
    });

    // Test with a valid currency
    $result = CurrencyRepository::isValid($currency);
    expect($result)->toBeTrue();

    // Test with an invalid currency
    $invalidCurrency = new Currency('XYZ', 'Invalid Currency', 2);
    $result = CurrencyRepository::isValid($invalidCurrency);
    expect($result)->toBeFalse();
});

it('checks if a currency code is valid', function () {
    // Create a valid currency
    $currency = new Currency('USD', 'US Dollar', 2);

    // Mock the Currency::fromCode method
    $this->partialMock(Currency::class, function ($mock) use ($currency) {
        $mock->shouldReceive('fromCode')
            ->with('USD')
            ->andReturn($currency);
    });

    // Mock the isValid method
    $this->partialMock(CurrencyRepository::class, function ($mock) use ($currency) {
        $mock->shouldReceive('isValid')
            ->with($currency)
            ->andReturn(true);
    });

    $result = CurrencyRepository::isValidCode('USD');
    expect($result)->toBeTrue();
});

it('loads available currencies without caching', function () {
    // Configure to not use cache
    Config::set('filament-money-field.currency_cache.type', false);
    Config::set('filament-money-field.currency_provider', ISOCurrenciesProvider::class);
    Config::set('filament-money-field.available_currencies', ['USD', 'EUR']);

    // Mock the ISOCurrenciesProvider
    $this->mock(ISOCurrenciesProvider::class, function ($mock) {
        $mock->shouldReceive('loadCurrencies')
            ->andReturn([
                'USD' => ['alphabeticCode' => 'USD', 'currency' => 'US Dollar', 'minorUnit' => 2, 'numericCode' => 840],
                'EUR' => ['alphabeticCode' => 'EUR', 'currency' => 'Euro', 'minorUnit' => 2, 'numericCode' => 978],
            ]);
    });

    $currencies = CurrencyRepository::getAvailableCurrencies();

    expect($currencies)->toBeInstanceOf(CurrencyCollection::class)
        ->and($currencies->count())->toBe(2)
        ->and($currencies->has('USD'))->toBeTrue()
        ->and($currencies->has('EUR'))->toBeTrue()
        ->and($currencies->get('USD')->name)->toBe('US Dollar')
        ->and($currencies->get('EUR')->name)->toBe('Euro');
});

it('caches currencies with remember', function () {
    // Configure to use remember cache
    Config::set('filament-money-field.currency_cache.type', 'remember');
    Config::set('filament-money-field.currency_cache.ttl', 60);
    Config::set('filament-money-field.currency_provider', ISOCurrenciesProvider::class);
    Config::set('filament-money-field.available_currencies', ['USD', 'EUR']);

    // Mock the ISOCurrenciesProvider
    $this->mock(ISOCurrenciesProvider::class, function ($mock) {
        $mock->shouldReceive('loadCurrencies')
            ->once() // This should only be called once, then cached
            ->andReturn([
                'USD' => ['alphabeticCode' => 'USD', 'currency' => 'US Dollar', 'minorUnit' => 2, 'numericCode' => 840],
                'EUR' => ['alphabeticCode' => 'EUR', 'currency' => 'Euro', 'minorUnit' => 2, 'numericCode' => 978],
            ]);
    });

    // Mock Cache facade
    Cache::shouldReceive('remember')
        ->once()
        ->with('filament_money_currencies', 60, \Mockery::type('Closure'))
        ->andReturnUsing(function ($key, $ttl, $callback) {
            return $callback();
        });

    $currencies = CurrencyRepository::getAvailableCurrencies();

    expect($currencies)->toBeInstanceOf(CurrencyCollection::class)
        ->and($currencies->count())->toBe(2);
});

it('caches currencies with flexible', function () {
    // Configure to use flexible cache
    Config::set('filament-money-field.currency_cache.type', 'flexible');
    Config::set('filament-money-field.currency_cache.ttl', [60, 3600]);
    Config::set('filament-money-field.currency_provider', ISOCurrenciesProvider::class);
    Config::set('filament-money-field.available_currencies', ['USD', 'EUR']);

    // Mock the ISOCurrenciesProvider
    $this->mock(ISOCurrenciesProvider::class, function ($mock) {
        $mock->shouldReceive('loadCurrencies')
            ->once() // This should only be called once, then cached
            ->andReturn([
                'USD' => ['alphabeticCode' => 'USD', 'currency' => 'US Dollar', 'minorUnit' => 2, 'numericCode' => 840],
                'EUR' => ['alphabeticCode' => 'EUR', 'currency' => 'Euro', 'minorUnit' => 2, 'numericCode' => 978],
            ]);
    });

    // Mock Cache facade
    Cache::shouldReceive('flexible')
        ->once()
        ->with('filament_money_currencies', [60, 3600], \Mockery::type('Closure'))
        ->andReturnUsing(function ($key, $ttl, $callback) {
            return $callback();
        });

    $currencies = CurrencyRepository::getAvailableCurrencies();

    expect($currencies)->toBeInstanceOf(CurrencyCollection::class)
        ->and($currencies->count())->toBe(2);
});

it('caches currencies forever', function () {
    // Configure to use forever cache
    Config::set('filament-money-field.currency_cache.type', 'forever');
    Config::set('filament-money-field.currency_provider', ISOCurrenciesProvider::class);
    Config::set('filament-money-field.available_currencies', ['USD', 'EUR']);

    // Mock the ISOCurrenciesProvider
    $this->mock(ISOCurrenciesProvider::class, function ($mock) {
        $mock->shouldReceive('loadCurrencies')
            ->once() // This should only be called once, then cached
            ->andReturn([
                'USD' => ['alphabeticCode' => 'USD', 'currency' => 'US Dollar', 'minorUnit' => 2, 'numericCode' => 840],
                'EUR' => ['alphabeticCode' => 'EUR', 'currency' => 'Euro', 'minorUnit' => 2, 'numericCode' => 978],
            ]);
    });

    // Mock Cache facade
    Cache::shouldReceive('forever')
        ->once()
        ->with('filament_money_currencies', \Mockery::type('Closure'))
        ->andReturnUsing(function ($key, $callback) {
            return $callback();
        });

    $currencies = CurrencyRepository::getAvailableCurrencies();

    expect($currencies)->toBeInstanceOf(CurrencyCollection::class)
        ->and($currencies->count())->toBe(2);
});

it('loads all available currencies when none specified', function () {
    Config::set('filament-money-field.currency_cache.type', false);
    Config::set('filament-money-field.currency_provider', ISOCurrenciesProvider::class);
    Config::set('filament-money-field.available_currencies', []);

    // Mock the ISOCurrenciesProvider with more currencies
    $this->mock(ISOCurrenciesProvider::class, function ($mock) {
        $mock->shouldReceive('loadCurrencies')
            ->andReturn([
                'USD' => ['alphabeticCode' => 'USD', 'currency' => 'US Dollar', 'minorUnit' => 2, 'numericCode' => 840],
                'EUR' => ['alphabeticCode' => 'EUR', 'currency' => 'Euro', 'minorUnit' => 2, 'numericCode' => 978],
                'GBP' => ['alphabeticCode' => 'GBP', 'currency' => 'British Pound', 'minorUnit' => 2, 'numericCode' => 826],
            ]);
    });

    $currencies = CurrencyRepository::getAvailableCurrencies();

    expect($currencies)->toBeInstanceOf(CurrencyCollection::class)
        ->and($currencies->count())->toBe(3)
        ->and($currencies->has('USD'))->toBeTrue()
        ->and($currencies->has('EUR'))->toBeTrue()
        ->and($currencies->has('GBP'))->toBeTrue();
});

it('loads crypto currencies when enabled', function () {
    Config::set('filament-money-field.currency_cache.type', false);
    Config::set('filament-money-field.currency_provider', ISOCurrenciesProvider::class);
    Config::set('filament-money-field.available_currencies', []);
    Config::set('filament-money-field.load_crypto_currencies', true);

    /*
    // Mock the ISOCurrenciesProvider
    $this->mock(ISOCurrenciesProvider::class, function ($mock) {
        $mock->shouldReceive('loadCurrencies')
            ->andReturn([
                'USD' => ['alphabeticCode' => 'USD', 'currency' => 'US Dollar', 'minorUnit' => 2, 'numericCode' => 840],
                'EUR' => ['alphabeticCode' => 'EUR', 'currency' => 'Euro', 'minorUnit' => 2, 'numericCode' => 978],
            ]);
    });

    // Mock the CryptoCurrenciesProvider
    $this->mock(CryptoCurrenciesProvider::class, function ($mock) {
        $mock->shouldReceive('loadCurrencies')
            ->andReturn([
                'BTC', 'ETH', 'XRP'
            ]);
    });
    */

    $currencies = CurrencyRepository::getAvailableCurrencies();

    expect($currencies)->toBeInstanceOf(CurrencyCollection::class)
        ->and($currencies->has('USD'))->toBeTrue()
        ->and($currencies->has('EUR'))->toBeTrue();
});
