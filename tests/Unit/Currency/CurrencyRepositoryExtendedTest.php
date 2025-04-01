<?php

use Pelmered\FilamentMoneyField\Currencies\Currency;
use Pelmered\FilamentMoneyField\Currencies\CurrencyCollection;
use Pelmered\FilamentMoneyField\Currencies\CurrencyRepository;

it('gets available currencies', function (): void {
    $currencies = CurrencyRepository::getAvailableCurrencies();

    expect($currencies)->toBeInstanceOf(CurrencyCollection::class)
        ->and($currencies->count())->toBeGreaterThan(1);
});

it('finds a currency by code', function (): void {
    $usd = CurrencyRepository::getAvailableCurrencies()->get('USD');

    expect($usd)->toBeInstanceOf(Currency::class)
        ->and($usd->getCode())->toBe('USD')
        ->and($usd->name)->toBe('US Dollar');
});

it('returns null for non-existent currency code', function (): void {
    $nonExistent = CurrencyRepository::getAvailableCurrencies()->get('XYZ');

    expect($nonExistent)->toBeNull();
});

it('checks if a currency exists', function (): void {
    $currencies = CurrencyRepository::getAvailableCurrencies();

    expect($currencies->has('USD'))->toBeTrue()
        ->and($currencies->has('XYZ'))->toBeFalse();
});

it('gets currency codes', function (): void {
    $currencies = CurrencyRepository::getAvailableCurrencies();
    $codes      = $currencies->pluck('code')->toArray();

    expect($codes)->toBeArray()
        ->and($codes)->toContain('USD', 'EUR');
});

it('respects currency inclusion configuration', function (): void {
    // Save original config
    $originalConfig = config('filament-money-field.available_currencies');

    // Configure to include only USD and EUR
    config(['filament-money-field.available_currencies' => ['USD', 'EUR']]);

    // Reset the static cache in CurrencyRepository
    CurrencyRepository::clearCache();

    $currencies = CurrencyRepository::getAvailableCurrencies();
    $codes      = $currencies->pluck('code')->toArray();

    expect($codes)->toHaveCount(2)
        ->and($codes)->toContain('USD', 'EUR');

    // Reset config
    config(['filament-money-field.available_currencies' => $originalConfig]);
    CurrencyRepository::clearCache();
});

it('respects currency exclusion configuration', function (): void {
    // Save original config
    $originalAvailable = config('filament-money-field.available_currencies', []);
    $originalExcluded  = config('filament-money-field.excluded_currencies', []);

    // Configure to exclude USD and EUR
    config(['filament-money-field.available_currencies' => []]);
    config(['filament-money-field.excluded_currencies' => ['USD', 'EUR']]);

    // Reset the static cache in CurrencyRepository
    CurrencyRepository::clearCache();

    $currencies = CurrencyRepository::getAvailableCurrencies();
    $codes      = $currencies->pluck('code')->toArray();

    expect($codes)->not->toContain('USD', 'EUR');

    // Reset config
    config(['filament-money-field.available_currencies' => $originalAvailable]);
    config(['filament-money-field.excluded_currencies' => $originalExcluded]);
    CurrencyRepository::clearCache();
});
