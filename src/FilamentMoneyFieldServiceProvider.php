<?php
namespace Pelmered\FilamentMoneyField;

use Spatie\LaravelPackageTools\Package;

class FilamentMoneyFieldServiceProvider extends \Spatie\LaravelPackageTools\PackageServiceProvider
{
    public static string $name = 'filament-money-field';

    public function configurePackage(Package $package): void
    {
        $package->name(static::$name)
                ->hasConfigFile();
    }

    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/filament-money-field.php' => config_path('filament-money-field.php'),
        ], 'config');
    }
}
