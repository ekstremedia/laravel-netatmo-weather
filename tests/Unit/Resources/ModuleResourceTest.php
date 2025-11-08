<?php

use Ekstremedia\NetatmoWeather\Http\Resources\ModuleResource;
use Ekstremedia\NetatmoWeather\Models\NetatmoModule;
use Ekstremedia\NetatmoWeather\Models\NetatmoStation;

beforeEach(function () {
    $this->station = NetatmoStation::create([
        'user_id' => 1,
        'station_name' => 'Test Station',
        'device_id' => 'device_123',
        'client_id' => 'client_123',
        'client_secret' => 'secret_123',
    ]);
});

it('transforms NAMain module correctly', function () {
    $module = NetatmoModule::create([
        'netatmo_station_id' => $this->station->id,
        'module_id' => 'main_123',
        'module_name' => 'Living Room',
        'type' => 'NAMain',
        'data_type' => ['Temperature', 'Humidity', 'CO2'],
        'dashboard_data' => [
            'Temperature' => 22.5,
            'Humidity' => 55,
            'CO2' => 800,
        ],
        'battery_percent' => null,
        'rf_status' => 50,
        'reachable' => true,
        'last_seen' => 1704067200,
        'firmware' => 180,
    ]);

    $resource = new ModuleResource($module);
    $array = $resource->toArray(request());

    expect($array['id'])->toBe('main_123');
    expect($array['name'])->toBe('Living Room');
    expect($array['type'])->toBe('Indoor Module');
    expect($array['data_types'])->toBe(['Temperature', 'Humidity', 'CO2']);
    expect($array['measurements'])->toBe([
        'Temperature' => 22.5,
        'Humidity' => 55,
        'CO2' => 800,
    ]);
    expect($array['status']['battery_percent'])->toBeNull();
    expect($array['status']['rf_status'])->toBe('good');
    expect($array['status']['reachable'])->toBeTrue();
    expect($array['firmware'])->toBe(180);
});

it('transforms NAModule1 correctly', function () {
    $module = NetatmoModule::create([
        'netatmo_station_id' => $this->station->id,
        'module_id' => 'outdoor_123',
        'module_name' => 'Garden',
        'type' => 'NAModule1',
        'data_type' => ['Temperature'],
        'data_type' => ['Temperature', 'Humidity'],
        'dashboard_data' => [
            'Temperature' => 15.5,
            'Humidity' => 75,
        ],
        'battery_percent' => 85,
        'rf_status' => 65,
        'reachable' => true,
        'last_seen' => 1704067200,
        'firmware' => 50,
    ]);

    $resource = new ModuleResource($module);
    $array = $resource->toArray(request());

    expect($array['type'])->toBe('Outdoor Module');
    expect($array['status']['battery_percent'])->toBe(85);
    expect($array['status']['rf_status'])->toBe('average');
});

it('transforms NAModule2 correctly', function () {
    $module = NetatmoModule::create([
        'netatmo_station_id' => $this->station->id,
        'module_id' => 'wind_123',
        'module_name' => 'Wind Gauge',
        'type' => 'NAModule2',
        'data_type' => ['WindStrength', 'WindAngle'],
        'dashboard_data' => [
            'WindStrength' => 12.5,
            'WindAngle' => 180,
        ],
        'battery_percent' => 45,
        'rf_status' => 95,
        'reachable' => false,
    ]);

    $resource = new ModuleResource($module);
    $array = $resource->toArray(request());

    expect($array['type'])->toBe('Wind Gauge');
    expect($array['status']['battery_percent'])->toBe(45);
    expect($array['status']['rf_status'])->toBe('weak');
    expect($array['status']['reachable'])->toBeFalse();
});

it('transforms NAModule3 correctly', function () {
    $module = NetatmoModule::create([
        'netatmo_station_id' => $this->station->id,
        'module_id' => 'rain_123',
        'module_name' => 'Rain Gauge',
        'type' => 'NAModule3',
        'data_type' => ['Rain'],
        'dashboard_data' => ['Rain' => 5.2],
    ]);

    $resource = new ModuleResource($module);
    $array = $resource->toArray(request());

    expect($array['type'])->toBe('Rain Gauge');
});

it('transforms NAModule4 correctly', function () {
    $module = NetatmoModule::create([
        'netatmo_station_id' => $this->station->id,
        'module_id' => 'indoor2_123',
        'module_name' => 'Bedroom',
        'type' => 'NAModule4',
        'data_type' => ['Temperature', 'Humidity', 'CO2'],
        'dashboard_data' => [
            'Temperature' => 20.0,
            'Humidity' => 60,
            'CO2' => 700,
        ],
    ]);

    $resource = new ModuleResource($module);
    $array = $resource->toArray(request());

    expect($array['type'])->toBe('Additional Indoor Module');
});

it('handles unknown module types', function () {
    $module = NetatmoModule::create([
        'netatmo_station_id' => $this->station->id,
        'module_id' => 'unknown_123',
        'module_name' => 'Unknown Device',
        'type' => 'NAModuleX',
        'data_type' => ['Temperature'],
    ]);

    $resource = new ModuleResource($module);
    $array = $resource->toArray(request());

    expect($array['type'])->toBe('NAModuleX');
});

it('handles rf_status boundaries correctly', function () {
    // Good signal (< 60)
    $module1 = NetatmoModule::create([
        'netatmo_station_id' => $this->station->id,
        'module_id' => 'mod1',
        'module_name' => 'Module 1',
        'type' => 'NAModule1',
        'data_type' => ['Temperature'],
        'data_type' => ['Temperature'],
        'rf_status' => 59,
    ]);

    $resource1 = new ModuleResource($module1);
    expect($resource1->toArray(request())['status']['rf_status'])->toBe('good');

    // Average signal (60-89)
    $module2 = NetatmoModule::create([
        'netatmo_station_id' => $this->station->id,
        'module_id' => 'mod2',
        'module_name' => 'Module 2',
        'type' => 'NAModule1',
        'data_type' => ['Temperature'],
        'rf_status' => 75,
    ]);

    $resource2 = new ModuleResource($module2);
    expect($resource2->toArray(request())['status']['rf_status'])->toBe('average');

    // Weak signal (>= 90)
    $module3 = NetatmoModule::create([
        'netatmo_station_id' => $this->station->id,
        'module_id' => 'mod3',
        'module_name' => 'Module 3',
        'type' => 'NAModule1',
        'data_type' => ['Temperature'],
        'rf_status' => 90,
    ]);

    $resource3 = new ModuleResource($module3);
    expect($resource3->toArray(request())['status']['rf_status'])->toBe('weak');
});

it('handles null rf_status', function () {
    $module = NetatmoModule::create([
        'netatmo_station_id' => $this->station->id,
        'module_id' => 'module_123',
        'module_name' => 'Test Module',
        'type' => 'NAModule1',
        'data_type' => ['Temperature'],
        'data_type' => ['Temperature'],
        'rf_status' => null,
    ]);

    $resource = new ModuleResource($module);
    $array = $resource->toArray(request());

    expect($array['status']['rf_status'])->toBeNull();
});

it('handles null last_seen', function () {
    $module = NetatmoModule::create([
        'netatmo_station_id' => $this->station->id,
        'module_id' => 'module_123',
        'module_name' => 'Test Module',
        'type' => 'NAModule1',
        'data_type' => ['Temperature'],
        'data_type' => ['Temperature'],
        'last_seen' => null,
    ]);

    $resource = new ModuleResource($module);
    $array = $resource->toArray(request());

    expect($array['status']['last_seen'])->toBeNull();
});

it('formats timestamp last_seen correctly', function () {
    $module = NetatmoModule::create([
        'netatmo_station_id' => $this->station->id,
        'module_id' => 'module_123',
        'module_name' => 'Test Module',
        'type' => 'NAModule1',
        'data_type' => ['Temperature'],
        'data_type' => ['Temperature'],
        'last_seen' => 1704067200,
    ]);

    $resource = new ModuleResource($module);
    $array = $resource->toArray(request());

    expect($array['status']['last_seen'])->toBe('2024-01-01 00:00:00');
});

it('includes updated_at as ISO8601', function () {
    $module = NetatmoModule::create([
        'netatmo_station_id' => $this->station->id,
        'module_id' => 'module_123',
        'module_name' => 'Test Module',
        'type' => 'NAModule1',
        'data_type' => ['Temperature'],
        'data_type' => ['Temperature'],
    ]);

    $module->updated_at = now();
    $module->save();

    $resource = new ModuleResource($module);
    $array = $resource->toArray(request());

    expect($array['updated_at'])->toBeString();
    expect($array['updated_at'])->toMatch('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}[+-]\d{2}:\d{2}$/');
});

it('casts battery_percent to integer', function () {
    $module = NetatmoModule::create([
        'netatmo_station_id' => $this->station->id,
        'module_id' => 'module_123',
        'module_name' => 'Test Module',
        'type' => 'NAModule1',
        'data_type' => ['Temperature'],
        'data_type' => ['Temperature'],
        'battery_percent' => '75',
    ]);

    $resource = new ModuleResource($module);
    $array = $resource->toArray(request());

    expect($array['status']['battery_percent'])->toBe(75);
    expect($array['status']['battery_percent'])->toBeInt();
});
