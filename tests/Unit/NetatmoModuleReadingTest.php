<?php

use Ekstremedia\NetatmoWeather\Models\NetatmoModule;
use Ekstremedia\NetatmoWeather\Models\NetatmoModuleReading;
use Ekstremedia\NetatmoWeather\Models\NetatmoStation;

it('can create a module reading', function () {
    $station = NetatmoStation::create([
        'user_id' => 1,
        'station_name' => 'Test Station',
        'client_id' => 'test_client_id',
        'client_secret' => 'test_client_secret',
    ]);

    $module = NetatmoModule::create([
        'netatmo_station_id' => $station->id,
        'module_id' => 'test_module_id',
        'module_name' => 'Test Module',
        'type' => 'NAModule1',
        'data_type' => ['Temperature', 'Humidity'],
    ]);

    $reading = NetatmoModuleReading::create([
        'netatmo_module_id' => $module->id,
        'time_utc' => now()->timestamp,
        'dashboard_data' => [
            'Temperature' => 22.5,
            'Humidity' => 65,
        ],
    ]);

    expect($reading->id)->not->toBeNull()
        ->and($reading->netatmo_module_id)->toBe($module->id)
        ->and($reading->time_utc)->toBeInt()
        ->and($reading->dashboard_data)->toBeArray();
});

it('belongs to a module', function () {
    $station = NetatmoStation::create([
        'user_id' => 1,
        'station_name' => 'Test Station',
        'client_id' => 'test_client_id',
        'client_secret' => 'test_client_secret',
    ]);

    $module = NetatmoModule::create([
        'netatmo_station_id' => $station->id,
        'module_id' => 'test_module_id',
        'module_name' => 'Test Module',
        'type' => 'NAModule1',
        'data_type' => ['Temperature', 'Humidity'],
    ]);

    $reading = NetatmoModuleReading::create([
        'netatmo_module_id' => $module->id,
        'time_utc' => now()->timestamp,
        'dashboard_data' => ['Temperature' => 22.5],
    ]);

    expect($reading->module)
        ->toBeInstanceOf(NetatmoModule::class)
        ->id->toBe($module->id);
});

it('casts dashboard_data to array', function () {
    $station = NetatmoStation::create([
        'user_id' => 1,
        'station_name' => 'Test Station',
        'client_id' => 'test_client_id',
        'client_secret' => 'test_client_secret',
    ]);

    $module = NetatmoModule::create([
        'netatmo_station_id' => $station->id,
        'module_id' => 'test_module_id',
        'module_name' => 'Test Module',
        'type' => 'NAModule1',
        'data_type' => ['Temperature', 'Humidity'],
    ]);

    $dashboardData = [
        'Temperature' => 22.5,
        'Humidity' => 65,
        'CO2' => 450,
    ];

    $reading = NetatmoModuleReading::create([
        'netatmo_module_id' => $module->id,
        'time_utc' => now()->timestamp,
        'dashboard_data' => $dashboardData,
    ]);

    $reading->refresh();

    expect($reading->dashboard_data)->toBeArray()
        ->and($reading->dashboard_data['Temperature'])->toBe(22.5)
        ->and($reading->dashboard_data['Humidity'])->toBe(65)
        ->and($reading->dashboard_data['CO2'])->toBe(450);
});

it('can store multiple readings for same module', function () {
    $station = NetatmoStation::create([
        'user_id' => 1,
        'station_name' => 'Test Station',
        'client_id' => 'test_client_id',
        'client_secret' => 'test_client_secret',
    ]);

    $module = NetatmoModule::create([
        'netatmo_station_id' => $station->id,
        'module_id' => 'test_module_id',
        'module_name' => 'Test Module',
        'type' => 'NAModule1',
        'data_type' => ['Temperature', 'Humidity'],
    ]);

    $reading1 = NetatmoModuleReading::create([
        'netatmo_module_id' => $module->id,
        'time_utc' => now()->subHour()->timestamp,
        'dashboard_data' => ['Temperature' => 20.0],
    ]);

    $reading2 = NetatmoModuleReading::create([
        'netatmo_module_id' => $module->id,
        'time_utc' => now()->timestamp,
        'dashboard_data' => ['Temperature' => 22.5],
    ]);

    expect(NetatmoModuleReading::where('netatmo_module_id', $module->id)->count())->toBe(2);
});
