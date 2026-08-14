<?php

declare(strict_types=1);

namespace Pelmered\FilamentMoneyField\Tests;

use Filament\Actions\ActionsServiceProvider;
use Filament\Infolists\InfolistsServiceProvider;
use Filament\Notifications\NotificationsServiceProvider;
use Filament\Schemas\SchemasServiceProvider;
use Filament\Support\Livewire\Partials\DataStoreOverride;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Routing\Router;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Livewire\Livewire;
use Livewire\Mechanisms\DataStore;
use Pelmered\FilamentMoneyField\Tests\Support\Components\BrowserFormComponent;

use function Orchestra\Testbench\artisan;

/**
 * Browser tests are served by the Pest browser plugin's in-process HTTP server,
 * which dispatches through this Testbench application's HTTP kernel. That means
 * routes, Livewire components and views must be registered on the test app, and
 * Filament's compiled assets must exist under public_path().
 */
class BrowserTestCase extends TestCase
{
    private static bool $assetsPublished = false;

    /**
     * The parent test case registers only what the Livewire-level tests need.
     * Rendering in a real browser additionally needs the packages that publish
     * the Alpine components the markup references (filamentSchema,
     * filamentSchemaComponent) and that back the currency switcher modal.
     */
    protected function getPackageProviders($app): array
    {
        return [
            ...parent::getPackageProviders($app),
            SchemasServiceProvider::class,
            ActionsServiceProvider::class,
            InfolistsServiceProvider::class,
            NotificationsServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        parent::defineEnvironment($app);

        $app['config']->set('app.key', 'base64:'.base64_encode(random_bytes(32)));
        $app['config']->set('session.driver', 'array');
        $app['view']->addNamespace('money-tests', __DIR__.'/Support/views');
    }

    protected function defineRoutes($router): void
    {
        assert($router instanceof Router);

        // Testbench's HTTP kernel registers no middleware groups, so "web" is empty.
        // Livewire needs the session and the shared error bag to render.
        $router->middleware([
            AddQueuedCookiesToResponse::class,
            StartSession::class,
            ShareErrorsFromSession::class,
        ])->group(function (Router $router): void {
            $router->get('/money-form', fn () => view('money-tests::browser-page'));
        });
    }

    protected function setUp(): void
    {
        parent::setUp();

        // Filament's SupportServiceProvider bind()s DataStore -> DataStoreOverride
        // (not singleton), and registers after LivewireServiceProvider here, so it
        // replaces Livewire's shared instance. DataStore keeps its state in a
        // per-instance WeakMap, so a non-shared binding hands out an empty store on
        // every store() call and Livewire's error bag comes back null while mounting.
        // Keep Filament's subclass, but share it.
        app()->instance(DataStore::class, new DataStoreOverride);

        Livewire::component('browser-form', BrowserFormComponent::class);

        // @filamentScripts/@filamentStyles resolve to public_path() URLs, which the
        // plugin's server returns straight from disk, so they have to be published.
        // Publish once per run rather than per test: which assets exist depends on
        // the registered providers, so an existence check on one file is not enough.
        if (! self::$assetsPublished) {
            artisan($this, 'filament:assets');
            self::$assetsPublished = true;
        }
    }
}
