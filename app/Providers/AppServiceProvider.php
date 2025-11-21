<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\SpeedyIndexService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind the SpeedyIndexService into the service container
        $this->app->bind(SpeedyIndexService::class, function ($app) {
            return new SpeedyIndexService();
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
