<?php

namespace Pelmered\FilamentMoneyField;

use Livewire\Livewire;
use Money\Currencies\ISOCurrencies;
use Pelmered\FilamentMoneyField\Casts\MoneySynthesizer;
use Illuminate\Database\Schema\Blueprint;
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

        Blueprint::macro('money', function (string $name, ?string $indexName = null) {
            $column = $this->unsignedBigInteger($name);
            $this->string("{$name}_currency");

            $this->index(["{$name}_currency", $name], $indexName);

            return $column;
        });

        Blueprint::macro('nullableMoney', function (string $name, ?string $indexName = null) {
            $column = $this->unsignedBigInteger($name)->nullable();
            $this->string("{$name}_currency")->nullable();

            $this->index(["{$name}_currency", $name], $indexName);

            return $column;
        });

        Blueprint::macro('smallMoney', function (string $name, ?string $indexName = null) {
            $column = $this->unsignedSmallInteger($name)->nullable();
            $this->string("{$name}_currency")->nullable();

            $this->index(["{$name}_currency", $name], $indexName);

            return $column;
        });

        Blueprint::macro('signedMoney', function (string $name, ?string $indexName = null) {
            $column = $this->bigInteger($name)->nullable();
            $this->string("{$name}_currency")->nullable();

            $this->index(["{$name}_currency", $name], $indexName);

            return $column;
        });
    }

    public function register(): void
    {
        $this->app->bind(CurrencyCollection::class, function () {


            return new CurrencyCollection();
        });

    }
}
