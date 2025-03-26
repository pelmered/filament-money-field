<?php

use Illuminate\Support\Facades\Config;
use Pelmered\FilamentMoneyField\Currencies\Currency;
use Pelmered\FilamentMoneyField\Currencies\CurrencyRepository;

it('loads configured currencies', function () {
    config(['filament-money-field.currencies' => ['USD', 'EUR', 'SEK']]);

    $collection = (new CurrencyRepository)->getAvailableCurrencies();

    expect($collection->count())->toBe(3);
    expect($collection->get('USD'))->toBeInstanceOf(Currency::class);
    expect($collection->get('EUR'))->toBeInstanceOf(Currency::class);
    expect($collection->get('SEK'))->toBeInstanceOf(Currency::class);
})->with([
    'USD',
    'EUR',
    'SEK',
]);

it('can create a subset of configured currencies', function () {
    config(['filament-money-field.currencies' => ['USD', 'EUR', 'SEK']]);

    $collection = (new CurrencyRepository)->getAvailableCurrencies();

    expect($collection)->toHaveCount(3);
    expect($collection->get('USD'))->toBeInstanceOf(Currency::class);
    expect($collection->get('USD')->getCode())->toBe('USD');
});

it('loads all configured currencies', function () {
    config(['filament-money-field.currencies' => ['USD', 'EUR', 'SEK']]);

    $collection = (new CurrencyRepository)->getAvailableCurrencies();

    expect($collection->count())->toBe(3);
    expect($collection->get('USD'))->toBeInstanceOf(Currency::class);
    expect($collection->get('EUR'))->toBeInstanceOf(Currency::class);
    expect($collection->get('SEK'))->toBeInstanceOf(Currency::class);
})->with([
    'USD',
    'EUR',
    'SEK',
]);

it('can load ISO currencies', function () {
    Config::set('filament-money-field.currencies', []);
    Config::set('filament-money-field.available_currencies', []);
    Config::set('filament-money-field.load_iso_currencies', true);
    Config::set('filament-money-field.currency_provider', \Money\Currencies\ISOCurrencies::class);

    $collection = (new CurrencyRepository)->getAvailableCurrencies();

    expect($collection->count())->toBeGreaterThan(100)
        ->and($collection->get('USD'))->toBeInstanceOf(Currency::class)
        ->and($collection->get('EUR'))->toBeInstanceOf(Currency::class)
        ->and($collection->get('JPY'))->toBeInstanceOf(Currency::class);
});

it('can create a subset of ISO currencies', function () {
    $currencyList = [
        'USD',
        'EUR',
        'SEK',
    ];

    Config::set('filament-money-field.currencies', []);
    Config::set('filament-money-field.available_currencies', $currencyList);
    Config::set('filament-money-field.currency_provider', \Money\Currencies\ISOCurrencies::class);

    $collection = (new CurrencyRepository)->getAvailableCurrencies();

    expect($collection)->toHaveCount(3)
        ->and($collection->get('USD'))->toBeInstanceOf(Currency::class)
        ->and($collection->get('USD')->getCode())->toBe('USD')
        ->and($collection->get('EUR'))->toBeInstanceOf(Currency::class)
        ->and($collection->get('EUR')->getCode())->toBe('EUR')
        ->and($collection->get('SEK'))->toBeInstanceOf(Currency::class)
        ->and($collection->get('SEK')->getCode())->toBe('SEK');
});
