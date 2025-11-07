<?php

namespace Ekstremedia\NetatmoWeather;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class NetatmoWeatherServiceProvider extends ServiceProvider
{
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

        Blade::directive('datetime', static function ($expression) {
            return "<?php echo \Illuminate\Support\Carbon::createFromTimestamp($expression)->setTimezone(config('app.timezone'))->format('Y-m-d H:i'); ?>";
        });

        Blade::directive('time', static function ($expression) {
            return "<?php echo \Illuminate\Support\Carbon::createFromTimestamp($expression)->setTimezone(config('app.timezone'))->format('H:i'); ?>";
        });
    }

    public function register(): void
    {
        // Register bindings, configurations, etc.
    }
}
