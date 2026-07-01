<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Compatibilité MySQL / WampServer (utf8mb4, index max 1000 octets)
        Schema::defaultStringLength(191);

        \Illuminate\Pagination\Paginator::useBootstrapFive();
    }
}
