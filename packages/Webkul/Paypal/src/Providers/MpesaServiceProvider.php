<?php

namespace Webkul\Paypal\Providers;

use Illuminate\Support\ServiceProvider;

class MpesaServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Load routes
        $this->loadRoutesFrom(__DIR__ . '/../Http/routes.php');

        // Load views
        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'mpesa');

        // Publish configuration
        $this->publishes([
            __DIR__ . '/../Config/paymentmethods.php' => config_path('paymentmethods.php'),
        ], 'config');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(EventServiceProvider::class);
        $this->mergeConfigFrom(__DIR__ . '/../Config/paymentmethods.php', 'paymentmethods');
    }
}