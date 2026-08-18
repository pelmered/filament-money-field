<?php

namespace Pelmered\FilamentMoneyField\Tests;

use BladeUI\Heroicons\BladeHeroiconsServiceProvider;
use BladeUI\Icons\BladeIconsServiceProvider;
use Filament\FilamentServiceProvider;
use Filament\Forms\FormsServiceProvider;
use Filament\Support\SupportServiceProvider;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Foundation\Application;
use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\Attributes\WithMigration;
use Pelmered\FilamentMoneyField\FilamentMoneyFieldServiceProvider;
use Pelmered\LaraPara\LaraParaServiceProvider;

use function Orchestra\Testbench\artisan;

// #[WithMigration('laravel', 'cache', 'queue')]

#[WithMigration]
class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            // Filament service providers
            BladeHeroiconsServiceProvider::class,
            BladeIconsServiceProvider::class,
            FilamentServiceProvider::class,
            FormsServiceProvider::class,
            LivewireServiceProvider::class,
            SupportServiceProvider::class,

            // This package service provider
            LaraParaServiceProvider::class,
            FilamentMoneyFieldServiceProvider::class,
        ];
    }

    /**
     * Define environment setup.
     *
     * @param  Application  $app
     * @return void
     */
    protected function defineEnvironment($app)
    {
        // Setup default database to use sqlite :memory:
        tap($app['config'], function (Repository $config): void {
            $config->set('database.default', 'testbench');
            $config->set('database.connections.testbench', [
                'driver'   => 'sqlite',
                'database' => ':memory:',
                'prefix'   => '',
            ]);
            $config->set('cache.default', 'file');

            // currency_cache is not mirrored in this package's config, so it has to be set on larapara.
            $config->set('larapara.currency_cache.type', false);

            // The expectations throughout the suite are written for en_US. The package itself
            // no longer defaults to a locale, so pin it here like a real app would.
            $config->set('filament-money-field.default_locale', 'en_US');
            $config->set('filament-money-field.available_currencies', ['USD', 'EUR', 'SEK']);

            // Setup queue database connections.
            /*
            $config([
                'queue.batching.database' => 'testbench',
                'queue.failed.database' => 'testbench',
            ]);
            */
        });
    }

    /**
     * Define database migrations.
     *
     * @return void
     */
    protected function defineDatabaseMigrations()
    {
        $this->loadMigrationsFrom(__DIR__.'/Support/Database/Migrations');

        artisan($this, 'migrate', ['--database' => 'testbench']);

        $this->beforeApplicationDestroyed(
            fn (): int => artisan($this, 'migrate:rollback', ['--database' => 'testbench'])
        );
    }

    public static function callMethod($obj, $name, array $args): mixed
    {
        $class = new \ReflectionClass($obj);

        return $class->getMethod($name)->invokeArgs($obj, $args);
    }

    public static function getProperty($object, $property): mixed
    {
        $reflectedClass = new \ReflectionClass($object);
        $reflection     = $reflectedClass->getProperty($property);

        return $reflection->getValue($object);
    }
}
