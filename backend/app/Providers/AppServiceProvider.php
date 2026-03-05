<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
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
        // Register API routes
        $this->loadRoutes();
    }

    protected function loadRoutes(): void
    {
        $router = $this->app['router'];
        $this->app->booting(function () use ($router) {
            require base_path('routes/api.php');
        });
    }
}
