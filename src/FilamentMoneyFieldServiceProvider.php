<?php

namespace Pelmered\FilamentMoneyField;

use Livewire\Livewire;
use Pelmered\LaraPara\Commands\CacheCommand;
use Pelmered\LaraPara\Commands\ClearCacheCommand;
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
            ->hasConfigFile()
            ->hasTranslations()
            ->hasCommands([
                CacheCommand::class,
                ClearCacheCommand::class,
            ]);
    }

    public function boot(): void
    {
        parent::boot();

        // Requires Laravel 11.27.1
        // See: https://github.com/laravel/framework/pull/52928
        /** @phpstan-ignore function.alreadyNarrowedType  */
        if (method_exists($this, 'optimizes')) {
            $this->optimizes(
                optimize: CacheCommand::class,
                clear: ClearCacheCommand::class,
            );
        }

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
