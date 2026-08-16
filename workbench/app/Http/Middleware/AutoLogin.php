<?php

declare(strict_types=1);

namespace Workbench\App\Http\Middleware;

use Closure;
use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Signs the demo user in so the panel needs no credentials. The workbench exists
 * to eyeball money fields across Filament majors, not to exercise Filament's auth.
 */
class AutoLogin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            Auth::login(User::query()->firstOrFail());
        }

        return $next($request);
    }
}
