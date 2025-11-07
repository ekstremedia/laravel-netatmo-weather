<?php

namespace Ekstremedia\NetatmoWeather;

use Ekstremedia\NetatmoWeather\Models\NetatmoStation;
use Ekstremedia\NetatmoWeather\Policies\NetatmoStationPolicy;
use Ekstremedia\NetatmoWeather\Services\NetatmoService;
use Ekstremedia\NetatmoWeather\Services\TokenRefreshService;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class NetatmoWeatherServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap package services.
     */
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/routes/web.php');

        $this->publishes([
            __DIR__.'/assets/images' => public_path('netatmo-weather/images'),
        ], 'public');

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations/');

        $this->publishes([
            __DIR__.'/config/netatmo-weather.php' => config_path('netatmo-weather.php'),
        ], 'config');

        $this->loadViewsFrom(__DIR__.'/resources/views', 'netatmoweather');

        $this->loadTranslationsFrom(__DIR__.'/resources/lang', 'netatmoweather');

        $this->mergeConfigFrom(
            __DIR__.'/config/netatmo-weather.php', 'netatmo-weather'
        );

        // Register policies
        Gate::policy(NetatmoStation::class, NetatmoStationPolicy::class);

        // Register Blade directives with error handling
        Blade::directive('datetime', static function ($expression) {
            return "<?php
                try {
                    echo \Illuminate\Support\Carbon::createFromTimestamp({$expression})
                        ->setTimezone(config('app.timezone'))
                        ->format('Y-m-d H:i');
                } catch (\Exception \$e) {
                    echo 'Invalid date';
                }
            ?>";
        });

        Blade::directive('time', static function ($expression) {
            return "<?php
                try {
                    echo \Illuminate\Support\Carbon::createFromTimestamp({$expression})
                        ->setTimezone(config('app.timezone'))
                        ->format('H:i');
                } catch (\Exception \$e) {
                    echo '--:--';
                }
            ?>";
        });
    }

    /**
     * Register package services.
     */
    public function register(): void
    {
        // Register service as singleton
        $this->app->singleton(NetatmoService::class, function ($app) {
            return new NetatmoService;
        });

        $this->app->singleton(TokenRefreshService::class, function ($app) {
            return new TokenRefreshService;
        });
    }
}
