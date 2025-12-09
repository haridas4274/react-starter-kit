<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use MyVendorName\MyPackageName\Provider\MyProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->register(MyProvider::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
