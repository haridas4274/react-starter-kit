<?php

use App\Http\Middleware\EnsureTenantUser;
use App\Http\Middleware\HandleAppearance;
use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\SetSessionDomainFromRequestHost;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\InitializeTenancyBySubdomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            // Central routes — only on central domains
            foreach (config('tenancy.central_domains') as $domain) {
                Route::middleware(['web', 'auth'])
                    ->domain($domain)
                    ->prefix('')
                    ->name('central.')
                    ->group(base_path('routes/central.php'));
            }

            // Tenant routes — on tenant domains, with tenancy initialization
            Route::middleware([
                'web',
                InitializeTenancyByDomain::class,
                PreventAccessFromCentralDomains::class,
                'auth',
                EnsureTenantUser::class,
            ])
                ->prefix('')
                ->name('tenant.')
                ->group(base_path('routes/tenant.php'));
        },
        
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->encryptCookies(except: ['appearance', 'sidebar_state']);

        $middleware->group('universal', [
            InitializeTenancyByDomain::class,
            InitializeTenancyBySubdomain::class,
        ]);
        $middleware->web(prepend: [
            SetSessionDomainFromRequestHost::class,
        ], append: [
            HandleAppearance::class,
            HandleInertiaRequests::class,
            AddLinkHeadersForPreloadedAssets::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
