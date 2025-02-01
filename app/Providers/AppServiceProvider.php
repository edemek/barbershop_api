<?php

namespace App\Providers;

use App\Packages\OpeningHoursService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind the OpeningHoursService to the service container
        $this->app->singleton('opening_hours_service', function ($app) {
            return new OpeningHoursService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
