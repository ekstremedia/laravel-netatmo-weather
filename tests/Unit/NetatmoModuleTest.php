<?php

use Ekstremedia\NetatmoWeather\Models\NetatmoModule;
use Ekstremedia\NetatmoWeather\Models\NetatmoStation;

use function Pest\Laravel\assertDatabaseHas;

it('can create a module', function () {
    $station = NetatmoStation::create([
        'user_id' => 1,
        'station_name' => 'Test Station',
        'client_id' => 'test_client_id',
        'client_secret' => 'test_client_secret',
    ]);

    $module = NetatmoModule::create([
        'netatmo_station_id' => $station->id,
        'module_id' => 'test_module_id',
        'module_name' => 'Indoor Module',
        'type' => 'NAMain',
        'data_type' => ['Temperature', 'Humidity', 'CO2', 'Noise', 'Pressure'],
        'battery_percent' => 80,
        'firmware' => '178',
        'reachable' => true,
        'dashboard_data' => [
            'Temperature' => 22.5,
            'Humidity' => 45,
            'CO2' => 500,
        ],
    ]);

    assertDatabaseHas('netatmo_modules', [
        'module_id' => 'test_module_id',
        'module_name' => 'Indoor Module',
        'type' => 'NAMain',
    ]);
});

it('belongs to a station', function () {
    $station = NetatmoStation::create([
        'user_id' => 1,
        'station_name' => 'Test Station',
        'client_id' => 'test_client_id',
        'client_secret' => 'test_client_secret',
    ]);

    $module = NetatmoModule::create([
        'netatmo_station_id' => $station->id,
        'module_id' => 'test_module_id',
        'module_name' => 'Indoor Module',
        'type' => 'NAMain',
        'data_type' => ['Temperature', 'Humidity'],
    ]);

    expect($module->netatmoStation)
        ->toBeInstanceOf(NetatmoStation::class)
        ->id->toBe($station->id);
});

it('casts json fields correctly', function () {
    $station = NetatmoStation::create([
        'user_id' => 1,
        'station_name' => 'Test Station',
        'client_id' => 'test_client_id',
        'client_secret' => 'test_client_secret',
    ]);

    $module = NetatmoModule::create([
        'netatmo_station_id' => $station->id,
        'module_id' => 'test_module_id',
        'module_name' => 'Indoor Module',
        'type' => 'NAMain',
        'data_type' => ['Temperature', 'Humidity'],
        'dashboard_data' => [
            'Temperature' => 22.5,
            'Humidity' => 45,
        ],
        'user' => ['admin' => true],
        'place' => ['city' => 'Oslo'],
    ]);

    expect($module->data_type)->toBeArray();
    expect($module->dashboard_data)->toBeArray();
    expect($module->user)->toBeArray();
    expect($module->place)->toBeArray();
});

it('stores different module types', function () {
    $station = NetatmoStation::create([
        'user_id' => 1,
        'station_name' => 'Test Station',
        'client_id' => 'test_client_id',
        'client_secret' => 'test_client_secret',
    ]);

    $types = [
        'NAMain' => 'Indoor',
        'NAModule1' => 'Outdoor',
        'NAModule2' => 'Wind',
        'NAModule3' => 'Rain',
        'NAModule4' => 'Additional Indoor',
    ];

    foreach ($types as $type => $name) {
        NetatmoModule::create([
            'netatmo_station_id' => $station->id,
            'module_id' => 'module_'.$type,
            'module_name' => $name.' Module',
            'type' => $type,
            'data_type' => ['Temperature'],
        ]);
    }

    expect($station->modules)->toHaveCount(5);
    expect($station->modules->firstWhere('type', 'NAMain')->type)->toBe('NAMain');
    expect($station->modules->firstWhere('type', 'NAModule3')->type)->toBe('NAModule3');
});
