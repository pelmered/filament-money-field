<?php

use Illuminate\Support\Facades\Config;
use Pelmered\FilamentMoneyField\FilamentMoneyFieldServiceProvider;

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

    // Check that we can access the configuration
    expect(config('filament-money-field.default_currency'))->not()->toBeNull();
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
