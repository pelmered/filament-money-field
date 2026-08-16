<?php

declare(strict_types=1);

namespace Workbench\App\Providers;

use Composer\InstalledVersions;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Workbench\App\Http\Middleware\AutoLogin;

/**
 * The demo panel served by "composer serve:f4" and "composer serve:f5".
 *
 * Filament 4 and 5 expose an identical panel API, so this one provider renders
 * the same panel under both majors.
 */
class DemoPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('demo')
            ->path('admin')
            ->brandName(self::runningVersions())
            ->colors(['primary' => Color::Amber])
            ->discoverResources(
                in: __DIR__.'/../Filament/Resources',
                for: 'Workbench\\App\\Filament\\Resources',
            )
            ->pages([Dashboard::class])
            // AuthenticateSession is omitted: it expects a session established by
            // a real login, which AutoLogin deliberately skips.
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
                AutoLogin::class,
            ], isPersistent: true);
    }

    /**
     * Read from the autoloader that actually booted this request, so the panel
     * always states which environment you are looking at. Testbench assumes a
     * package's vendor directory is named "vendor", and when that assumption
     * bites the wrong environment gets served — visibly wrong beats silent.
     */
    private static function runningVersions(): string
    {
        $version = static fn (string $package): string => ltrim(
            InstalledVersions::getPrettyVersion($package) ?? '?',
            'v'
        );

        return 'Money Field · Filament '.$version('filament/filament')
            .' · Laravel '.$version('laravel/framework')
            .' · Livewire '.$version('livewire/livewire');
    }
}
