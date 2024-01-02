<?php
namespace Pelmered\FilamentMoneyField;

use Spatie\LaravelPackageTools\Package;

class FilamentMoneyFieldServiceProvider extends \Spatie\LaravelPackageTools\PackageServiceProvider
{
    public static string $name = 'filament-money-field';

    public static string $viewNamespace = 'filament-money-field';

    public function configurePackage(Package $package): void
    {
        $package->name(static::$name);
    }
}
