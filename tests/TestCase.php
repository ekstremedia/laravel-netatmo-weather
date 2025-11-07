<?php

namespace Ekstremedia\NetatmoWeather\Tests;

use Ekstremedia\NetatmoWeather\NetatmoWeatherServiceProvider;
use Illuminate\Database\Eloquent\Factories\Factory;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        // Enable foreign key constraints for SQLite
        if (config('database.default') === 'testing') {
            \Illuminate\Support\Facades\DB::statement('PRAGMA foreign_keys=ON;');
        }

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Ekstremedia\\NetatmoWeather\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    protected function getPackageProviders($app): array
    {
        return [
            NetatmoWeatherServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app): void
    {
        config()->set('database.default', 'testing');
        config()->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        // Set up auth config to use test user model
        config()->set('auth.providers.users.model', \Ekstremedia\NetatmoWeather\Tests\Support\User::class);

        // Create users table
        \Illuminate\Support\Facades\Schema::create('users', function ($table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
        });

        // Run package migrations
        $migration = include __DIR__.'/../database/migrations/2024_08_03_104551_netatmo_stations_table.php';
        $migration->up();

        $migration = include __DIR__.'/../database/migrations/2024_08_04_145532_netatmo_token_table.php';
        $migration->up();

        $migration = include __DIR__.'/../database/migrations/2024_08_18_224146_create_netatmo_station_modules_table.php';
        $migration->up();

        $migration = include __DIR__.'/../database/migrations/2024_08_18_224202_create_netatmo_weather_module_readings.php';
        $migration->up();

        $migration = include __DIR__.'/../database/migrations/2025_01_07_000001_add_is_public_to_netatmo_stations_table.php';
        $migration->up();

        $migration = include __DIR__.'/../database/migrations/2025_01_07_000002_fix_netatmo_modules_unique_constraint.php';
        $migration->up();

        $migration = include __DIR__.'/../database/migrations/2025_01_07_000003_add_device_id_to_netatmo_stations_table.php';
        $migration->up();

        $migration = include __DIR__.'/../database/migrations/2025_01_07_000004_add_is_active_to_netatmo_modules_table.php';
        $migration->up();
    }
}
