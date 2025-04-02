<?php

namespace Pelmered\FilamentMoneyField;

use Illuminate\Database\Schema\Blueprint;
use Livewire\Livewire;
use Pelmered\FilamentMoneyField\Commands\CacheCommand;
use Pelmered\FilamentMoneyField\Commands\ClearCacheCommand;
use Pelmered\FilamentMoneyField\Currencies\CurrencyCollection;
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

        // Requires Laravel 11.27.1
        // See: https://github.com/laravel/framework/pull/52928
        /** @phpstan-ignore function.alreadyNarrowedType  */
        if (method_exists($this, 'optimizes')) {
            $this->optimizes(
                optimize: CacheCommand::class,
                clear: ClearCacheCommand::class,
            );
        }

        $this->commands([
            CacheCommand::class,
            ClearCacheCommand::class,
        ]);

        Livewire::propertySynthesizer(CurrencySynthesizer::class);
        Livewire::propertySynthesizer(MoneySynthesizer::class);



        Blueprint::macro('money', function (string $name, ?string $indexName = null) {
            $currencySuffix = config('filament-money-field.currency_column_suffix');

            $column = config('filament-money-field.store.format') === 'decimal'
                ? $this->decimal($name, 12, 3)
                : $this->unsignedBigInteger($name);

            $this->string($name.$currencySuffix, 6);

            $this->index([$name.$currencySuffix, $name], $indexName);

            return $column;
        });

        Blueprint::macro('nullableMoney', function (string $name, ?string $indexName = null) {
            $currencySuffix = config('filament-money-field.currency_column_suffix');

            $column = config('filament-money-field.store.format') === 'decimal'
                ? $this->decimal($name, 12, 3)->nullable()
                : $this->unsignedBigInteger($name)->nullable();

            $this->string($name.$currencySuffix, 6)->nullable();

            $this->index([$name.$currencySuffix, $name], $indexName);

            return $column;
        });

        Blueprint::macro('smallMoney', function (string $name, ?string $indexName = null) {
            $currencySuffix = config('filament-money-field.currency_column_suffix');

            $column = config('filament-money-field.store.format') === 'decimal'
                ? $this->decimal($name, 6, 3)->nullable()
                : $this->unsignedSmallInteger($name)->nullable();
            $this->string($name.$currencySuffix, 6)->nullable();

            $this->index([$name.$currencySuffix, $name], $indexName);

            return $column;
        });

        Blueprint::macro('signedMoney', function (string $name, ?string $indexName = null) {
            $currencySuffix = config('filament-money-field.currency_column_suffix');

            $column = config('filament-money-field.store.format') === 'decimal'
                ? $this->decimal($name, 12, 3)->nullable()
                : $this->bigInteger($name)->nullable();
            $this->string($name.$currencySuffix, 6)->nullable();

            $this->index([$name.$currencySuffix, $name], $indexName);

            return $column;
        });
    }

    public function register(): void
    {
        $this->app->bind(CurrencyCollection::class, function (): CurrencyCollection {
            return new CurrencyCollection;
        });

    }
}
