<?php

use Illuminate\Support\Facades\Config;
use Pelmered\FilamentMoneyField\FilamentMoneyFieldServiceProvider;
use Pelmered\FilamentMoneyField\Tests\TestCase;

// Tests for FilamentMoneyFieldServiceProvider that avoid using reflection

it('loads service provider correctly', function (): void {
    // Test that the service provider can be instantiated without errors
    $serviceProvider = new FilamentMoneyFieldServiceProvider(app());
    expect($serviceProvider)->toBeInstanceOf(FilamentMoneyFieldServiceProvider::class);
});

it('registers config file correctly', function (): void {
    // Verify that the config file exists in the package
    $configSourcePath = realpath(__DIR__.'/../../config');
    $hasConfigFile    = file_exists($configSourcePath.'/filament-money-field.php');

    expect($hasConfigFile)->toBeTrue();

    // Check that we can access the configuration. Most keys are unset by default,
    // since they inherit either the app locale or LaraPara's own value.
    expect(config('filament-money-field'))->toBeArray()
        ->toHaveKeys(['default_locale', 'decimal_digits', 'default_currency']);
});

it('merges config', function (): void {
    $originalDefaultCurrency = config('filament-money-field.default_currency');

    // Change config value
    config(['filament-money-field.default_currency' => 'EUR']);

    // Check if config was changed
    expect(config('filament-money-field.default_currency'))->toEqual('EUR');

    // Reset to original value
    config(['filament-money-field.default_currency' => $originalDefaultCurrency]);
});

function forwardConfig(array $config): void
{
    config($config);

    TestCase::callMethod(
        new FilamentMoneyFieldServiceProvider(app()),
        'forwardConfigToLaraPara',
        []
    );
}

it("forwards its own config over LaraPara's", function (string $key, string $laraParaKey, mixed $value): void {
    forwardConfig(['filament-money-field.'.$key => $value]);

    expect(config('larapara.'.$laraParaKey))->toBe($value);
})->with([
    'default currency'       => ['default_currency', 'default_currency', 'SEK'],
    'intl currency symbol'   => ['intl_currency_symbol', 'intl_currency_symbol', true],
    'currency column suffix' => ['currency_column_suffix', 'currency_column_suffix', '_iso'],
    'available currencies'   => ['available_currencies', 'available_currencies', ['USD', 'SEK']],
    'store format'           => ['store.format', 'store.format', 'decimal'],
    'store decimal scale'    => ['store.decimal_scale', 'store.decimal_scale', 8],
    'parse strict'           => ['parse.strict', 'parse.strict', true],
]);

it("leaves LaraPara's own config alone when nothing is set here", function (mixed $unset): void {
    forwardConfig([
        'larapara.default_currency'             => 'EUR',
        'filament-money-field.default_currency' => $unset,
    ]);

    expect(config('larapara.default_currency'))->toBe('EUR');
})->with([
    // A MONEY_* variable named in .env with nothing after the = reads as an empty string,
    // not null, and that is the most natural way to write "I am not setting this".
    'null'         => [null],
    'empty string' => [''],
    'only spaces'  => ['   '],
]);

it('keeps the sibling keys of a nested group it overrides', function (): void {
    $ttl = config('larapara.currency_cache.ttl');

    forwardConfig(['filament-money-field.store.format' => 'decimal']);

    expect(config('larapara.store.format'))->toBe('decimal')
        ->and(config('larapara.currency_cache.ttl'))->toBe($ttl);
});
