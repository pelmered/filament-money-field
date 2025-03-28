<?php

use Pelmered\FilamentMoneyField\Currencies\Currency;
use Pelmered\FilamentMoneyField\Exceptions\UnsupportedCurrency;

/**
 * @test
 */
it('can be created from currency code', function (string $code): void {
    config(['filament-money-field.currencies' => ['USD', 'EUR', 'SEK']]);

    $currency = Currency::fromCode($code);

    expect($currency)->toBeInstanceOf(Currency::class)
        ->and($currency->getCode())->toBe(strtoupper($code));
})->with(['USD', 'EUR', 'SEK']);

/**
 * @test
 */
it('throws exception for unsupported currency', function (): void {
    config(['filament-money-field.currencies' => ['USD', 'EUR', 'SEK']]);

    expect(fn (): \Pelmered\FilamentMoneyField\Currencies\Currency => Currency::fromCode('PHP'))->toThrow(UnsupportedCurrency::class);
    expect(fn (): \Pelmered\FilamentMoneyField\Currencies\Currency => Currency::fromCode('INR'))->toThrow(UnsupportedCurrency::class);
});

/**
 * @test
 */
it('handles different case inputs', function (): void {
    config(['filament-money-field.currencies' => ['USD']]);

    $currencyLower = Currency::fromCode('usd');
    $currencyUpper = Currency::fromCode('USD');
    $currencyMixed = Currency::fromCode('UsD');

    expect($currencyLower->getCode())->toBe('USD')
        ->and($currencyUpper->getCode())->toBe('USD')
        ->and($currencyMixed->getCode())->toBe('USD');
});

/**
 * @test
 */
it('maintains case consistency in toString', function (): void {
    config(['filament-money-field.currencies' => ['USD']]);

    $currency = Currency::fromCode('usd');

    expect((string) $currency)->toBe('USD');
});
