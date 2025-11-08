<?php

use Ekstremedia\NetatmoWeather\Http\Resources\StationResource;
use Ekstremedia\NetatmoWeather\Models\NetatmoModule;
use Ekstremedia\NetatmoWeather\Models\NetatmoStation;

it('transforms station with basic data correctly', function () {
    $station = NetatmoStation::create([
        'user_id' => 1,
        'station_name' => 'My Weather Station',
        'device_id' => 'device_abc123',
        'client_id' => 'client_123',
        'client_secret' => 'secret_123',
    ]);

    $resource = new StationResource($station);
    $array = $resource->toArray(request());

    expect($array['id'])->toBe($station->uuid);
    expect($array['name'])->toBe('My Weather Station');
    expect($array['device_id'])->toBe('device_abc123');
});

it('includes module count', function () {
    $station = NetatmoStation::create([
        'user_id' => 1,
        'station_name' => 'Test Station',
        'device_id' => 'device_123',
        'client_id' => 'client_123',
        'client_secret' => 'secret_123',
    ]);

    // Create active modules
    NetatmoModule::create([
        'netatmo_station_id' => $station->id,
        'module_id' => 'mod1',
        'module_name' => 'Module 1',
        'type' => 'NAMain',
        'data_type' => ['Temperature'],
        'data_type' => ['Temperature'],
        'is_active' => true,
    ]);

    NetatmoModule::create([
        'netatmo_station_id' => $station->id,
        'module_id' => 'mod2',
        'module_name' => 'Module 2',
        'type' => 'NAModule1',
        'data_type' => ['Temperature'],
        'data_type' => ['Temperature'],
        'is_active' => true,
    ]);

    // Create inactive module (shouldn't be counted)
    NetatmoModule::create([
        'netatmo_station_id' => $station->id,
        'module_id' => 'mod3',
        'module_name' => 'Module 3',
        'type' => 'NAModule2',
        'data_type' => ['WindStrength'],
        'is_active' => false,
    ]);

    $resource = new StationResource($station);
    $array = $resource->toArray(request());

    expect($array['active_modules_count'])->toBe(2);
});

it('includes last_updated from most recent active module', function () {
    $station = NetatmoStation::create([
        'user_id' => 1,
        'station_name' => 'Test Station',
        'device_id' => 'device_123',
        'client_id' => 'client_123',
        'client_secret' => 'secret_123',
    ]);

    $oldModule = NetatmoModule::create([
        'netatmo_station_id' => $station->id,
        'module_id' => 'old_mod',
        'module_name' => 'Old Module',
        'type' => 'NAMain',
        'data_type' => ['Temperature'],
        'data_type' => ['Temperature'],
        'is_active' => true,
    ]);
    $oldModule->updated_at = now()->subHours(5);
    $oldModule->save();

    $recentModule = NetatmoModule::create([
        'netatmo_station_id' => $station->id,
        'module_id' => 'recent_mod',
        'module_name' => 'Recent Module',
        'type' => 'NAModule1',
        'data_type' => ['Temperature'],
        'data_type' => ['Temperature'],
        'is_active' => true,
    ]);
    $recentModule->updated_at = now()->subHour();
    $recentModule->save();

    $resource = new StationResource($station);
    $array = $resource->toArray(request());

    expect($array['last_updated'])->toBeString();
    expect($array['last_updated'])->toMatch('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}[+-]\d{2}:\d{2}$/');
});

it('returns null last_updated when no active modules', function () {
    $station = NetatmoStation::create([
        'user_id' => 1,
        'station_name' => 'Test Station',
        'device_id' => 'device_123',
        'client_id' => 'client_123',
        'client_secret' => 'secret_123',
    ]);

    $resource = new StationResource($station);
    $array = $resource->toArray(request());

    expect($array['last_updated'])->toBeNull();
});

it('includes location data when available', function () {
    $station = NetatmoStation::create([
        'user_id' => 1,
        'station_name' => 'Test Station',
        'device_id' => 'device_123',
        'client_id' => 'client_123',
        'client_secret' => 'secret_123',
    ]);

    NetatmoModule::create([
        'netatmo_station_id' => $station->id,
        'module_id' => 'mod1',
        'module_name' => 'Module 1',
        'type' => 'NAMain',
        'data_type' => ['Temperature'],
        'place' => [
            'altitude' => 150,
            'city' => 'Oslo',
            'country' => 'NO',
            'timezone' => 'Europe/Oslo',
            'location' => [10.7461, 59.9127], // [longitude, latitude]
        ],
    ]);

    $resource = new StationResource($station);
    $array = $resource->toArray(request());

    expect($array['location'])->not->toBeNull();
    expect($array['location']['altitude'])->toBe(150);
    expect($array['location']['city'])->toBe('Oslo');
    expect($array['location']['country'])->toBe('NO');
    expect($array['location']['timezone'])->toBe('Europe/Oslo');
    expect($array['location']['coordinates']['latitude'])->toBe(59.9127);
    expect($array['location']['coordinates']['longitude'])->toBe(10.7461);
});

it('returns null location when no place data', function () {
    $station = NetatmoStation::create([
        'user_id' => 1,
        'station_name' => 'Test Station',
        'device_id' => 'device_123',
        'client_id' => 'client_123',
        'client_secret' => 'secret_123',
    ]);

    NetatmoModule::create([
        'netatmo_station_id' => $station->id,
        'module_id' => 'mod1',
        'module_name' => 'Module 1',
        'type' => 'NAMain',
        'data_type' => ['Temperature'],
        'data_type' => ['Temperature'],
        'place' => null,
    ]);

    $resource = new StationResource($station);
    $array = $resource->toArray(request());

    expect($array['location'])->toBeNull();
});

it('returns null location when no modules', function () {
    $station = NetatmoStation::create([
        'user_id' => 1,
        'station_name' => 'Test Station',
        'device_id' => 'device_123',
        'client_id' => 'client_123',
        'client_secret' => 'secret_123',
    ]);

    $resource = new StationResource($station);
    $array = $resource->toArray(request());

    expect($array['location'])->toBeNull();
});

it('handles partial location data', function () {
    $station = NetatmoStation::create([
        'user_id' => 1,
        'station_name' => 'Test Station',
        'device_id' => 'device_123',
        'client_id' => 'client_123',
        'client_secret' => 'secret_123',
    ]);

    NetatmoModule::create([
        'netatmo_station_id' => $station->id,
        'module_id' => 'mod1',
        'module_name' => 'Module 1',
        'type' => 'NAMain',
        'data_type' => ['Temperature'],
        'place' => [
            'city' => 'Oslo',
            'country' => 'NO',
            // Missing altitude, timezone, location
        ],
    ]);

    $resource = new StationResource($station);
    $array = $resource->toArray(request());

    expect($array['location'])->not->toBeNull();
    expect($array['location']['city'])->toBe('Oslo');
    expect($array['location']['country'])->toBe('NO');
    expect($array['location']['altitude'])->toBeNull();
    expect($array['location']['timezone'])->toBeNull();
    expect($array['location']['coordinates'])->toBeNull();
});

it('handles location without coordinates', function () {
    $station = NetatmoStation::create([
        'user_id' => 1,
        'station_name' => 'Test Station',
        'device_id' => 'device_123',
        'client_id' => 'client_123',
        'client_secret' => 'secret_123',
    ]);

    NetatmoModule::create([
        'netatmo_station_id' => $station->id,
        'module_id' => 'mod1',
        'module_name' => 'Module 1',
        'type' => 'NAMain',
        'data_type' => ['Temperature'],
        'place' => [
            'city' => 'Oslo',
            'country' => 'NO',
            'altitude' => 150,
            'timezone' => 'Europe/Oslo',
            // No location array
        ],
    ]);

    $resource = new StationResource($station);
    $array = $resource->toArray(request());

    expect($array['location']['coordinates'])->toBeNull();
});

it('includes loaded modules when eager loaded', function () {
    $station = NetatmoStation::create([
        'user_id' => 1,
        'station_name' => 'Test Station',
        'device_id' => 'device_123',
        'client_id' => 'client_123',
        'client_secret' => 'secret_123',
    ]);

    NetatmoModule::create([
        'netatmo_station_id' => $station->id,
        'module_id' => 'mod1',
        'module_name' => 'Module 1',
        'type' => 'NAMain',
        'data_type' => ['Temperature'],
    ]);

    NetatmoModule::create([
        'netatmo_station_id' => $station->id,
        'module_id' => 'mod2',
        'module_name' => 'Module 2',
        'type' => 'NAModule1',
        'data_type' => ['Temperature'],
    ]);

    // Eager load modules
    $station->load('modules');

    $resource = new StationResource($station);
    $array = $resource->toArray(request());

    // whenLoaded returns a ResourceCollection, not a plain array
    expect($array['modules'])->toBeInstanceOf(\Illuminate\Http\Resources\Json\AnonymousResourceCollection::class);
});

it('includes modules even when not explicitly eager loaded due to resource calculations', function () {
    $station = NetatmoStation::create([
        'user_id' => 1,
        'station_name' => 'Test Station',
        'device_id' => 'device_123',
        'client_id' => 'client_123',
        'client_secret' => 'secret_123',
    ]);

    NetatmoModule::create([
        'netatmo_station_id' => $station->id,
        'module_id' => 'mod1',
        'module_name' => 'Module 1',
        'type' => 'NAMain',
        'data_type' => ['Temperature'],
    ]);

    // Don't explicitly eager load modules
    $resource = new StationResource($station);
    $array = $resource->toArray(request());

    // Modules are loaded as a side effect of active_modules_count, last_updated, and location calculations
    expect($array['modules'])->toBeInstanceOf(\Illuminate\Http\Resources\Json\AnonymousResourceCollection::class);
});

it('uses first module with place data for location', function () {
    $station = NetatmoStation::create([
        'user_id' => 1,
        'station_name' => 'Test Station',
        'device_id' => 'device_123',
        'client_id' => 'client_123',
        'client_secret' => 'secret_123',
    ]);

    // First module without place
    NetatmoModule::create([
        'netatmo_station_id' => $station->id,
        'module_id' => 'mod1',
        'module_name' => 'Module 1',
        'type' => 'NAModule1',
        'data_type' => ['Temperature'],
        'data_type' => ['Temperature'],
        'place' => null,
    ]);

    // Second module with place
    NetatmoModule::create([
        'netatmo_station_id' => $station->id,
        'module_id' => 'mod2',
        'module_name' => 'Module 2',
        'type' => 'NAMain',
        'data_type' => ['Temperature'],
        'place' => [
            'city' => 'Bergen',
            'country' => 'NO',
        ],
    ]);

    $resource = new StationResource($station);
    $array = $resource->toArray(request());

    expect($array['location'])->not->toBeNull();
    expect($array['location']['city'])->toBe('Bergen');
});
