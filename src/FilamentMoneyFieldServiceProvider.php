<?php

namespace Pelmered\FilamentMoneyField;

use Livewire\Livewire;
use Pelmered\FilamentMoneyField\Synthesizers\CurrencySynthesizer;
use Pelmered\FilamentMoneyField\Synthesizers\MoneySynthesizer;
use Pelmered\LaraPara\Commands\CacheCommand;
use Pelmered\LaraPara\Commands\ClearCacheCommand;
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

        $this->forwardConfigToLaraPara();

        Livewire::propertySynthesizer(CurrencySynthesizer::class);
        Livewire::propertySynthesizer(MoneySynthesizer::class);
    }

    /**
     * LaraPara owns the currency configuration, but this package's config file is
     * the one its users publish, so anything set there wins. A null means "inherit
     * LaraPara's own default", which leaves a published config/larapara.php in
     * charge of the keys this file does not mirror.
     *
     * Every larapara.* key is read lazily — Blueprint macros, casts and the
     * formatter all read it per call — so boot() is early enough for all of them.
     */
    protected function forwardConfigToLaraPara(): void
    {
        $overrides = [
            'default_currency'       => config('filament-money-field.default_currency'),
            'intl_currency_symbol'   => config('filament-money-field.intl_currency_symbol'),
            'currency_column_suffix' => config('filament-money-field.currency_column_suffix'),
            'available_currencies'   => config('filament-money-field.available_currencies'),
            'store.format'           => config('filament-money-field.store.format'),
        ];

        foreach ($overrides as $key => $value) {
            if (! is_null($value)) {
                config(['larapara.'.$key => $value]);
            }
        }
    }

    public function register(): void
    {
        parent::register();

        $this->app->bind(CurrencyCollection::class, function (): CurrencyCollection {
            return new CurrencyCollection;
        });

    }
}
