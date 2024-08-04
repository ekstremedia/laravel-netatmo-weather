<?php

namespace Ekstremedia\NetatmoWeather;

use Illuminate\Support\ServiceProvider;

class NetatmoWeatherServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/routes/web.php');

        $this->publishes([
            __DIR__ . '/path/to/assets' => public_path('vendor/netatmoweather'),
        ], 'public');

        if (config('memory.modules.vehicle')) {
        }
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations/');

        $this->publishes([
            __DIR__ . '/config/netatmo-weather.php' => config_path('netatmo-weather.php'),
        ], 'config');

        $this->loadViewsFrom(__DIR__ . '/resources/views', 'netatmoweather');

        $this->loadTranslationsFrom(__DIR__ . '/resources/lang', 'netatmoweather');

        $this->mergeConfigFrom(
            __DIR__ . '/config/netatmo-weather.php', 'netatmo-weather'
        );
    }

    public function register(): void
    {
        // Register bindings, configurations, etc.
    }
}
