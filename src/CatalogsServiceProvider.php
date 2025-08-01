<?php

namespace Gam\LaravelSatCatalogs;

use Gam\LaravelSatCatalogs\Console\UpdateCatalogs;
use Illuminate\Support\ServiceProvider;

class CatalogsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                build_path([__DIR__, '..', 'config', 'catalogs.php']) => config_path('catalogs.php'),
            ], 'config');

            // Registering package commands.
            $this->commands([
                UpdateCatalogs::class,
            ]);
        }
    }

    /**
     * Register the application services.
     */
    public function register(): void
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(build_path([__DIR__, '..', 'config', 'catalogs.php']), 'catalogs');

        // Register the main class to use with the facade
        $this->app->singleton('catalog', fn (): Catalog => new Catalog());
    }
}
