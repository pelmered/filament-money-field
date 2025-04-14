<?php

namespace Pelmered\FilamentMoneyField;

use Livewire\Livewire;
use Pelmered\FilamentMoneyField\Synthesizers\CurrencySynthesizer;
use Pelmered\FilamentMoneyField\Synthesizers\MoneySynthesizer;
use Pelmered\LaraPara\Currencies\CurrencyCollection;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentMoneyFieldServiceProvider extends PackageServiceProvider
{
    public static string $name = 'filament-money-field';

    public function configurePackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasConfigFile()
            ->hasTranslations();
    }

    public function boot(): void
    {
        parent::boot();

        Livewire::propertySynthesizer(CurrencySynthesizer::class);
        Livewire::propertySynthesizer(MoneySynthesizer::class);
    }

    public function register(): void
    {
        parent::register();

        $this->app->bind(CurrencyCollection::class, function (): CurrencyCollection {
            return new CurrencyCollection;
        });

    }
}
