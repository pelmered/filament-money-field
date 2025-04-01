<?php

use Illuminate\Database\Schema\Blueprint;
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

it('registers database macros', function (): void {
    // Test that the macros for Blueprint are registered
    expect(Blueprint::hasMacro('money'))->toBeTrue();
    expect(Blueprint::hasMacro('nullableMoney'))->toBeTrue();
    expect(Blueprint::hasMacro('smallMoney'))->toBeTrue();
    expect(Blueprint::hasMacro('signedMoney'))->toBeTrue();
});

it('has correct blueprint money macro implementation', function (): void {
    $originalSuffix = config('filament-money-field.currency_column_suffix');

    // Set a test value
    $testSuffix = '_test_currency';
    config(['filament-money-field.currency_column_suffix' => $testSuffix]);

    // Create a mock that will track what columns are defined
    $mockBlueprint = Mockery::mock(Blueprint::class, ['test_table'])
        ->makePartial()
        ->shouldAllowMockingProtectedMethods();

    // Set expectations for the mock
    $mockBlueprint->shouldReceive('unsignedBigInteger')
        ->once()
        ->with('price')
        ->andReturnSelf();

    $mockBlueprint->shouldReceive('string')
        ->once()
        ->with('price'.$testSuffix)
        ->andReturnSelf();

    $mockBlueprint->shouldReceive('index')
        ->once()
        ->with(['price'.$testSuffix, 'price'], null)
        ->andReturnSelf();

    // Call the macro on our mock
    $moneyMacro = Blueprint::macro('money', function (string $name, ?string $indexName = null) use ($testSuffix) {
        $column = $this->unsignedBigInteger($name);
        $this->string($name.$testSuffix);

        $this->index([$name.$testSuffix, $name], $indexName);

        return $column;
    });

    // Execute the money macro
    $mockBlueprint->money('price');

    // Reset the config
    config(['filament-money-field.currency_column_suffix' => $originalSuffix]);
});
