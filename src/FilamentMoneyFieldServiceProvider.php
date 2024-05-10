<?php
namespace Pelmered\FilamentMoneyField;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentMoneyFieldServiceProvider extends PackageServiceProvider
{
    public static string $name = 'filament-money-field';

    public function configurePackage(Package $package): void
    {
        $package->name(static::$name)
                ->hasConfigFile();
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/filament-money-field.php' => config_path('filament-money-field.php'),
        ], 'config');
    }
}
