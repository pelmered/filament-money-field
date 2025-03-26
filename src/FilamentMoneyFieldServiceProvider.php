<?php

namespace Pelmered\FilamentMoneyField;

use Illuminate\Database\Schema\Blueprint;
use Livewire\Livewire;
use Pelmered\FilamentMoneyField\Casts\MoneySynthesizer;
use Pelmered\FilamentMoneyField\Commands\CacheCommand;
use Pelmered\FilamentMoneyField\Commands\ClearCacheCommand;
use Pelmered\FilamentMoneyField\Currencies\CurrencyCollection;
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

        $this->optimizes(
            optimize: CacheCommand::class,
            clear: ClearCacheCommand::class,
        );

        Livewire::propertySynthesizer(MoneySynthesizer::class);

        $currencySuffix = config('filament-money-field.currency_column_suffix');

        Blueprint::macro('money', function (string $name, ?string $indexName = null) use ($currencySuffix) {
            $column = $this->unsignedBigInteger($name);
            $this->string($name.$currencySuffix);

            $this->index([$name.$currencySuffix, $name], $indexName);

            return $column;
        });

        Blueprint::macro('nullableMoney', function (string $name, ?string $indexName = null) use ($currencySuffix) {
            $column = $this->unsignedBigInteger($name)->nullable();
            $this->string($name.$currencySuffix)->nullable();

            $this->index([$name.$currencySuffix, $name], $indexName);

            return $column;
        });

        Blueprint::macro('smallMoney', function (string $name, ?string $indexName = null) use ($currencySuffix) {
            $column = $this->unsignedSmallInteger($name)->nullable();
            $this->string($name.$currencySuffix)->nullable();

            $this->index([$name.$currencySuffix, $name], $indexName);

            return $column;
        });

        Blueprint::macro('signedMoney', function (string $name, ?string $indexName = null) use ($currencySuffix) {
            $column = $this->bigInteger($name)->nullable();
            $this->string($name.$currencySuffix)->nullable();

            $this->index([$name.$currencySuffix, $name], $indexName);

            return $column;
        });
    }

    public function register(): void
    {
        $this->app->bind(CurrencyCollection::class, function () {
            return new CurrencyCollection;
        });

    }
}
