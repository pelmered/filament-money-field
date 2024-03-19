<?php
namespace Pelmered\FilamentMoneyField;

use Spatie\LaravelPackageTools\Package;

class FilamentMoneyFieldServiceProvider extends \Spatie\LaravelPackageTools\PackageServiceProvider
{
    /**
     * The name of the package.
     * 
     * @var string
     */
    public static string $name = 'filament-money-field';

    /**
     * The package's config key.
     * 
     * @var string
     */
    public function configurePackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasConfigFile();
    }

    /**
     * Bootstrap the application services.
     * 
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/filament-money-field.php' => config_path('filament-money-field.php'),
        ], 'config');
    }
}
