<?php

use Ekstremedia\NetatmoWeather\Models\NetatmoModule;
use Ekstremedia\NetatmoWeather\Models\NetatmoStation;
use Ekstremedia\NetatmoWeather\Models\NetatmoToken;
use Illuminate\Support\Facades\Cache;

use function Pest\Laravel\getJson;

beforeEach(function () {
    // Clear cache before each test
    Cache::flush();
});

it('returns 404 for non-existent station', function () {
    getJson(route('api.netatmo.show', ['uuid' => 'non-existent-uuid']))
        ->assertNotFound();
});

it('returns 403 when API is not enabled', function () {
    $station = NetatmoStation::create([
        'user_id' => 1,
        'station_name' => 'Test Station',
        'client_id' => 'test_client_id',
        'client_secret' => 'test_client_secret',
        'api_enabled' => false, // API disabled
    ]);

    getJson(route('api.netatmo.show', ['uuid' => $station->uuid]))
        ->assertStatus(403)
        ->assertJson([
            'error' => 'API access is not enabled for this station',
        ]);
});

it('returns 401 when API token is required but not provided', function () {
    $station = NetatmoStation::create([
        'user_id' => 1,
        'station_name' => 'Test Station',
        'client_id' => 'test_client_id',
        'client_secret' => 'test_client_secret',
        'api_enabled' => true,
        'api_token' => 'secret-token-123',
    ]);

    getJson(route('api.netatmo.show', ['uuid' => $station->uuid]))
        ->assertStatus(401)
        ->assertJson([
            'error' => 'Invalid or missing API token',
        ]);
});

it('returns 401 when invalid API token is provided', function () {
    $station = NetatmoStation::create([
        'user_id' => 1,
        'station_name' => 'Test Station',
        'client_id' => 'test_client_id',
        'client_secret' => 'test_client_secret',
        'api_enabled' => true,
        'api_token' => 'secret-token-123',
    ]);

    getJson(route('api.netatmo.show', ['uuid' => $station->uuid]), [
        'Authorization' => 'Bearer wrong-token',
    ])
        ->assertStatus(401)
        ->assertJson([
            'error' => 'Invalid or missing API token',
        ]);
});

it('returns 503 when station has no token', function () {
    $station = NetatmoStation::create([
        'user_id' => 1,
        'station_name' => 'Test Station',
        'client_id' => 'test_client_id',
        'client_secret' => 'test_client_secret',
        'api_enabled' => true,
    ]);

    getJson(route('api.netatmo.show', ['uuid' => $station->uuid]))
        ->assertStatus(503)
        ->assertJson([
            'error' => 'Station not authenticated with Netatmo',
        ]);
});

it('returns station data with valid API token', function () {
    $station = NetatmoStation::create([
        'user_id' => 1,
        'station_name' => 'Test Station',
        'client_id' => 'test_client_id',
        'client_secret' => 'test_client_secret',
        'device_id' => 'device_123',
        'api_enabled' => true,
        'api_token' => 'secret-token-123',
    ]);

    NetatmoToken::create([
        'netatmo_station_id' => $station->id,
        'access_token' => 'valid_token',
        'refresh_token' => 'refresh_token',
        'expires_at' => now()->addHour(),
    ]);

    $module = NetatmoModule::create([
        'netatmo_station_id' => $station->id,
        'module_id' => 'module_1',
        'module_name' => 'Indoor',
        'type' => 'NAMain',
        'data_type' => ['Temperature', 'Humidity'],
        'dashboard_data' => [
            'Temperature' => 22.5,
            'Humidity' => 45,
        ],
        'is_active' => true,
    ]);

    $response = getJson(route('api.netatmo.show', ['uuid' => $station->uuid]), [
        'Authorization' => 'Bearer secret-token-123',
    ])
        ->assertOk()
        ->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'device_id',
                'modules',
                'location',
                'last_updated',
                'active_modules_count',
            ],
        ]);

    $data = $response->json('data');
    expect($data['id'])->toBe($station->uuid);
    expect($data['name'])->toBe('Test Station');
    expect($data['active_modules_count'])->toBe(1);
});

it('returns station data without API token when none is set', function () {
    $station = NetatmoStation::create([
        'user_id' => 1,
        'station_name' => 'Public API Station',
        'client_id' => 'test_client_id',
        'client_secret' => 'test_client_secret',
        'device_id' => 'device_123',
        'api_enabled' => true,
        'api_token' => null, // No token required
    ]);

    NetatmoToken::create([
        'netatmo_station_id' => $station->id,
        'access_token' => 'valid_token',
        'refresh_token' => 'refresh_token',
        'expires_at' => now()->addHour(),
    ]);

    NetatmoModule::create([
        'netatmo_station_id' => $station->id,
        'module_id' => 'module_1',
        'module_name' => 'Indoor',
        'type' => 'NAMain',
        'data_type' => ['Temperature'],
        'dashboard_data' => ['Temperature' => 22.5],
        'is_active' => true,
    ]);

    getJson(route('api.netatmo.show', ['uuid' => $station->uuid]))
        ->assertOk()
        ->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'modules',
            ],
        ]);
});

it('caches API responses for performance', function () {
    $station = NetatmoStation::create([
        'user_id' => 1,
        'station_name' => 'Test Station',
        'client_id' => 'test_client_id',
        'client_secret' => 'test_client_secret',
        'device_id' => 'device_123',
        'api_enabled' => true,
    ]);

    NetatmoToken::create([
        'netatmo_station_id' => $station->id,
        'access_token' => 'valid_token',
        'refresh_token' => 'refresh_token',
        'expires_at' => now()->addHour(),
    ]);

    NetatmoModule::create([
        'netatmo_station_id' => $station->id,
        'module_id' => 'module_1',
        'module_name' => 'Indoor',
        'type' => 'NAMain',
        'data_type' => ['Temperature'],
        'dashboard_data' => ['Temperature' => 22.5],
        'is_active' => true,
    ]);

    // First request - should cache
    getJson(route('api.netatmo.show', ['uuid' => $station->uuid]))
        ->assertOk();

    // Verify cache exists
    $cacheKey = "api.station.{$station->uuid}.data";
    expect(Cache::has($cacheKey))->toBeTrue();

    // Update module temperature
    $station->modules()->first()->update([
        'dashboard_data' => ['Temperature' => 25.0],
    ]);

    // Second request - should return cached data (still 22.5)
    $response = getJson(route('api.netatmo.show', ['uuid' => $station->uuid]))
        ->assertOk();

    // Cache should still have old temperature
    $cachedData = Cache::get($cacheKey);
    expect($cachedData->modules->first()->dashboard_data['Temperature'])->toBe(22.5);
});

it('returns measurements endpoint data', function () {
    $station = NetatmoStation::create([
        'user_id' => 1,
        'station_name' => 'Test Station',
        'client_id' => 'test_client_id',
        'client_secret' => 'test_client_secret',
        'api_enabled' => true,
    ]);

    NetatmoToken::create([
        'netatmo_station_id' => $station->id,
        'access_token' => 'valid_token',
        'refresh_token' => 'refresh_token',
        'expires_at' => now()->addHour(),
    ]);

    NetatmoModule::create([
        'netatmo_station_id' => $station->id,
        'module_id' => 'module_1',
        'module_name' => 'Indoor',
        'type' => 'NAMain',
        'data_type' => ['Temperature', 'Humidity'],
        'dashboard_data' => [
            'Temperature' => 22.5,
            'Humidity' => 45,
        ],
        'is_active' => true,
    ]);

    NetatmoModule::create([
        'netatmo_station_id' => $station->id,
        'module_id' => 'module_2',
        'module_name' => 'Outdoor',
        'type' => 'NAModule1',
        'data_type' => ['Temperature'],
        'dashboard_data' => [
            'Temperature' => 18.0,
        ],
        'is_active' => true,
    ]);

    $response = getJson(route('api.netatmo.measurements', ['uuid' => $station->uuid]))
        ->assertOk()
        ->assertJsonStructure([
            'station' => ['id', 'name'],
            'measurements',
            'cached_until',
        ]);

    $measurements = $response->json('measurements');
    expect($measurements)->toHaveKey('Indoor');
    expect($measurements)->toHaveKey('Outdoor');
    expect($measurements['Indoor']['data']['Temperature'])->toBe(22.5);
    expect($measurements['Outdoor']['data']['Temperature'])->toBe(18.0);
});

it('returns specific module data', function () {
    $station = NetatmoStation::create([
        'user_id' => 1,
        'station_name' => 'Test Station',
        'client_id' => 'test_client_id',
        'client_secret' => 'test_client_secret',
        'api_enabled' => true,
    ]);

    NetatmoToken::create([
        'netatmo_station_id' => $station->id,
        'access_token' => 'valid_token',
        'refresh_token' => 'refresh_token',
        'expires_at' => now()->addHour(),
    ]);

    $module = NetatmoModule::create([
        'netatmo_station_id' => $station->id,
        'module_id' => 'module_1',
        'module_name' => 'Indoor',
        'type' => 'NAMain',
        'data_type' => ['Temperature', 'Humidity'],
        'dashboard_data' => [
            'Temperature' => 22.5,
            'Humidity' => 45,
        ],
        'battery_percent' => 85,
        'rf_status' => 50,
        'is_active' => true,
    ]);

    $response = getJson(route('api.netatmo.module', [
        'uuid' => $station->uuid,
        'moduleId' => 'module_1',
    ]))
        ->assertOk()
        ->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'type',
                'data_types',
                'measurements',
                'status' => [
                    'battery_percent',
                    'rf_status',
                    'reachable',
                    'last_seen',
                ],
                'firmware',
                'updated_at',
            ],
        ]);

    $data = $response->json('data');
    expect($data['id'])->toBe('module_1');
    expect($data['name'])->toBe('Indoor');
    expect($data['type'])->toBe('Indoor Module');
    expect($data['status']['battery_percent'])->toBe(85);
    expect($data['status']['rf_status'])->toBe('good');
});

it('returns 404 for non-existent module', function () {
    $station = NetatmoStation::create([
        'user_id' => 1,
        'station_name' => 'Test Station',
        'client_id' => 'test_client_id',
        'client_secret' => 'test_client_secret',
        'api_enabled' => true,
    ]);

    NetatmoToken::create([
        'netatmo_station_id' => $station->id,
        'access_token' => 'valid_token',
        'refresh_token' => 'refresh_token',
        'expires_at' => now()->addHour(),
    ]);

    getJson(route('api.netatmo.module', [
        'uuid' => $station->uuid,
        'moduleId' => 'non-existent',
    ]))
        ->assertNotFound();
});

it('does not return inactive modules in measurements', function () {
    $station = NetatmoStation::create([
        'user_id' => 1,
        'station_name' => 'Test Station',
        'client_id' => 'test_client_id',
        'client_secret' => 'test_client_secret',
        'api_enabled' => true,
    ]);

    NetatmoToken::create([
        'netatmo_station_id' => $station->id,
        'access_token' => 'valid_token',
        'refresh_token' => 'refresh_token',
        'expires_at' => now()->addHour(),
    ]);

    // Active module
    NetatmoModule::create([
        'netatmo_station_id' => $station->id,
        'module_id' => 'module_1',
        'module_name' => 'Indoor',
        'type' => 'NAMain',
        'data_type' => ['Temperature'],
        'dashboard_data' => ['Temperature' => 22.5],
        'is_active' => true,
    ]);

    // Inactive module
    NetatmoModule::create([
        'netatmo_station_id' => $station->id,
        'module_id' => 'module_2',
        'module_name' => 'Archived',
        'type' => 'NAModule1',
        'data_type' => ['Temperature'],
        'dashboard_data' => ['Temperature' => 18.0],
        'is_active' => false, // Inactive
    ]);

    $response = getJson(route('api.netatmo.measurements', ['uuid' => $station->uuid]))
        ->assertOk();

    $measurements = $response->json('measurements');
    expect($measurements)->toHaveKey('Indoor');
    expect($measurements)->not->toHaveKey('Archived');
});
