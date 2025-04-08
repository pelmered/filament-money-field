<?php

namespace Pelmered\FilamentMoneyField;

use Illuminate\Database\Schema\Blueprint;
use Livewire\Livewire;
use Pelmered\FilamentMoneyField\Commands\CacheCommand;
use Pelmered\FilamentMoneyField\Commands\ClearCacheCommand;
use Pelmered\LaraPara\Currencies\CurrencyCollection;
use Pelmered\FilamentMoneyField\Synthesizers\CurrencySynthesizer;
use Pelmered\FilamentMoneyField\Synthesizers\MoneySynthesizer;
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
            __DIR__.'/../config/filament-money-field.php' => config_path('filament-money-field.php'),
        ], 'filament-money-field');
        $this->mergeConfigFrom(
            __DIR__.'/../config/filament-money-field.php', 'filament-money-field'
        );

        Livewire::propertySynthesizer(CurrencySynthesizer::class);
        Livewire::propertySynthesizer(MoneySynthesizer::class);
    }

    public function register(): void
    {
        $this->app->bind(CurrencyCollection::class, function (): CurrencyCollection {
            return new CurrencyCollection;
        });

    }
}
