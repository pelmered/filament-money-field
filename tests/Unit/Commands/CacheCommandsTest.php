<?php

// Let's skip the actual cache checks in these tests
// and focus on ensuring the commands run successfully

use Illuminate\Support\Facades\Cache;
use Pelmered\FilamentMoneyField\Currencies\CurrencyCollection;
use Pelmered\FilamentMoneyField\Currencies\CurrencyRepository;

test('cache command runs successfully', function (): void {

    config(['filament-money-field.currency_cache.type' => 'remember']);
    config(['filament-money-field.currency_cache.ttl' => '500']);

    CurrencyRepository::clearCache();

    expect(Cache::has('filament_money_currencies'))->toBeFalse();

    test()->artisan('money:cache')
        ->assertExitCode(0);

    expect(Cache::has('filament_money_currencies'))->toBeTrue();

    $currencies  = Cache::get('filament_money_currencies');
    $currencies2 = CurrencyRepository::getAvailableCurrencies();

    expect($currencies->count())->toBe($currencies2->count());
    expect($currencies)->toBeInstanceOf(CurrencyCollection::class);
});

test('clear cache command runs successfully', function (): void {

    config(['filament-money-field.currency_cache.type' => 'remember']);
    config(['filament-money-field.currency_cache.ttl' => '500']);

    $currencies = CurrencyRepository::getAvailableCurrencies();

    expect(Cache::has('filament_money_currencies'))->toBeTrue();

    test()->artisan('money:clear')
        ->expectsOutput('Currencies cache cleared.')
        ->assertExitCode(0);

    expect(Cache::has('filament_money_currencies'))->toBeFalse();
});

/*
test('optimize command also adds currencies to cache', function () {
    test()->artisan('optimize')
         ->assertExitCode(0);
});
*/
