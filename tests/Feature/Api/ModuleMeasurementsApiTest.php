<?php

use Ekstremedia\NetatmoWeather\Models\NetatmoModule;
use Ekstremedia\NetatmoWeather\Models\NetatmoStation;
use Ekstremedia\NetatmoWeather\Models\NetatmoToken;
use Ekstremedia\NetatmoWeather\Tests\Support\User;
use Illuminate\Support\Facades\Http;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\getJson;

it('allows owner to access module measurements without API token', function () {
    $user = new User;
    $user->id = 1;
    $user->email = 'test@example.com';

    $station = NetatmoStation::create([
        'user_id' => 1,
        'station_name' => 'Test Station',
        'client_id' => 'test_client_id',
        'client_secret' => 'test_client_secret',
        'device_id' => 'device_123',
        'api_enabled' => false, // API disabled
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
        'is_active' => true,
    ]);

    // Mock Netatmo API response
    Http::fake([
        config('netatmo-weather.netatmo_api_url').'*' => Http::response([
            'body' => [[1234567890, 22.5, 45]],
            'status' => 'ok',
        ]),
    ]);

    actingAs($user)->getJson(route('api.netatmo.module.measurements', [
        'uuid' => $station->uuid,
        'moduleId' => 'module_1',
    ]))
        ->assertOk()
        ->assertJsonStructure([
            'module' => ['id', 'name', 'type'],
            'period',
            'scale',
            'measurements',
        ]);
});

it('requires API auth for non-owner on module measurements', function () {
    $station = NetatmoStation::create([
        'user_id' => 1,
        'station_name' => 'Test Station',
        'client_id' => 'test_client_id',
        'client_secret' => 'test_client_secret',
        'device_id' => 'device_123',
        'api_enabled' => false, // API disabled
    ]);

    $module = NetatmoModule::create([
        'netatmo_station_id' => $station->id,
        'module_id' => 'module_1',
        'module_name' => 'Indoor',
        'type' => 'NAMain',
        'data_type' => ['Temperature'],
        'is_active' => true,
    ]);

    // Not authenticated as owner
    getJson(route('api.netatmo.module.measurements', [
        'uuid' => $station->uuid,
        'moduleId' => 'module_1',
    ]))
        ->assertStatus(403)
        ->assertJson([
            'error' => 'API access is not enabled for this station',
        ]);
});

it('allows non-owner with valid API token on module measurements', function () {
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
        'is_active' => true,
    ]);

    Http::fake([
        config('netatmo-weather.netatmo_api_url').'*' => Http::response([
            'body' => [[1234567890, 22.5, 45]],
            'status' => 'ok',
        ]),
    ]);

    getJson(route('api.netatmo.module.measurements', [
        'uuid' => $station->uuid,
        'moduleId' => 'module_1',
    ]), [
        'Authorization' => 'Bearer secret-token-123',
    ])
        ->assertOk();
});

it('validates period parameter on module measurements', function () {
    $user = new User;
    $user->id = 1;

    $station = NetatmoStation::create([
        'user_id' => 1,
        'station_name' => 'Test Station',
        'client_id' => 'test_client_id',
        'client_secret' => 'test_client_secret',
        'device_id' => 'device_123',
    ]);

    $module = NetatmoModule::create([
        'netatmo_station_id' => $station->id,
        'module_id' => 'module_1',
        'module_name' => 'Indoor',
        'type' => 'NAMain',
        'data_type' => ['Temperature'],
        'is_active' => true,
    ]);

    actingAs($user)->getJson(route('api.netatmo.module.measurements', [
        'uuid' => $station->uuid,
        'moduleId' => 'module_1',
        'period' => 'invalid',
    ]))
        ->assertStatus(400)
        ->assertJsonFragment([
            'error' => 'Invalid period. Valid options: 1hour, 6hours, 12hours, 1day, 3days, 1week, 1month',
        ]);
});

it('validates scale parameter on module measurements', function () {
    $user = new User;
    $user->id = 1;

    $station = NetatmoStation::create([
        'user_id' => 1,
        'station_name' => 'Test Station',
        'client_id' => 'test_client_id',
        'client_secret' => 'test_client_secret',
        'device_id' => 'device_123',
    ]);

    $module = NetatmoModule::create([
        'netatmo_station_id' => $station->id,
        'module_id' => 'module_1',
        'module_name' => 'Indoor',
        'type' => 'NAMain',
        'data_type' => ['Temperature'],
        'is_active' => true,
    ]);

    actingAs($user)->getJson(route('api.netatmo.module.measurements', [
        'uuid' => $station->uuid,
        'moduleId' => 'module_1',
        'scale' => 'invalid',
    ]))
        ->assertStatus(400)
        ->assertJsonFragment([
            'error' => 'Invalid scale. Valid options: max, 30min, 1hour, 3hours, 1day, 1week, 1month',
        ]);
});

it('handles measurement service exceptions on module measurements', function () {
    $user = new User;
    $user->id = 1;

    $station = NetatmoStation::create([
        'user_id' => 1,
        'station_name' => 'Test Station',
        'client_id' => 'test_client_id',
        'client_secret' => 'test_client_secret',
        'device_id' => 'device_123',
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
        'data_type' => ['Temperature'],
        'is_active' => true,
    ]);

    // Mock API failure
    Http::fake([
        config('netatmo-weather.netatmo_api_url').'*' => Http::response([
            'error' => 'API error',
        ], 500),
    ]);

    actingAs($user)->getJson(route('api.netatmo.module.measurements', [
        'uuid' => $station->uuid,
        'moduleId' => 'module_1',
    ]))
        ->assertStatus(500)
        ->assertJsonStructure([
            'error',
            'message',
            'debug' => [
                'module_id',
                'module_type',
                'data_types',
                'station_device_id',
            ],
        ]);
});

it('only allows station owner to access debug endpoint', function () {
    $station = NetatmoStation::create([
        'user_id' => 1,
        'station_name' => 'Test Station',
        'client_id' => 'test_client_id',
        'client_secret' => 'test_client_secret',
        'device_id' => 'device_123',
    ]);

    $module = NetatmoModule::create([
        'netatmo_station_id' => $station->id,
        'module_id' => 'module_1',
        'module_name' => 'Indoor',
        'type' => 'NAMain',
        'data_type' => ['Temperature'],
        'is_active' => true,
    ]);

    // Not authenticated
    getJson(route('api.netatmo.module.measurements.debug', [
        'uuid' => $station->uuid,
        'moduleId' => 'module_1',
    ]))
        ->assertStatus(403)
        ->assertJson([
            'error' => 'Debug endpoint only available to station owner',
        ]);
});

it('allows owner to access debug endpoint', function () {
    $user = new User;
    $user->id = 1;

    $station = NetatmoStation::create([
        'user_id' => 1,
        'station_name' => 'Test Station',
        'client_id' => 'test_client_id',
        'client_secret' => 'test_client_secret',
        'device_id' => 'device_123',
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
        'is_active' => true,
    ]);

    actingAs($user)->getJson(route('api.netatmo.module.measurements.debug', [
        'uuid' => $station->uuid,
        'moduleId' => 'module_1',
    ]))
        ->assertOk()
        ->assertJsonStructure([
            'debug_info' => [
                'station' => ['id', 'uuid', 'name', 'device_id'],
                'module' => ['db_id', 'module_id', 'name', 'type', 'data_types'],
                'token' => ['exists', 'valid', 'expires_at'],
                'api_request' => ['url', 'method', 'params', 'date_range_human'],
                'database_readings' => ['count', 'latest'],
            ],
        ]);
});

it('debug endpoint handles different period parameters', function () {
    $user = new User;
    $user->id = 1;

    $station = NetatmoStation::create([
        'user_id' => 1,
        'station_name' => 'Test Station',
        'client_id' => 'test_client_id',
        'client_secret' => 'test_client_secret',
        'device_id' => 'device_123',
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
        'data_type' => ['Temperature'],
        'is_active' => true,
    ]);

    foreach (['1hour', '6hours', '12hours', '1day', '3days', '1week', '1month'] as $period) {
        actingAs($user)->getJson(route('api.netatmo.module.measurements.debug', [
            'uuid' => $station->uuid,
            'moduleId' => 'module_1',
            'period' => $period,
        ]))
            ->assertOk()
            ->assertJsonPath('debug_info.api_request.params.scale', '30min');
    }
});

it('debug endpoint excludes module_id parameter for NAMain modules', function () {
    $user = new User;
    $user->id = 1;

    $station = NetatmoStation::create([
        'user_id' => 1,
        'station_name' => 'Test Station',
        'client_id' => 'test_client_id',
        'client_secret' => 'test_client_secret',
        'device_id' => 'device_123',
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
        'data_type' => ['Temperature'],
        'is_active' => true,
    ]);

    $response = actingAs($user)->getJson(route('api.netatmo.module.measurements.debug', [
        'uuid' => $station->uuid,
        'moduleId' => 'module_1',
    ]))
        ->assertOk();

    $params = $response->json('debug_info.api_request.params');
    expect($params)->not->toHaveKey('module_id');
});

it('debug endpoint includes module_id parameter for non-NAMain modules', function () {
    $user = new User;
    $user->id = 1;

    $station = NetatmoStation::create([
        'user_id' => 1,
        'station_name' => 'Test Station',
        'client_id' => 'test_client_id',
        'client_secret' => 'test_client_secret',
        'device_id' => 'device_123',
    ]);

    NetatmoToken::create([
        'netatmo_station_id' => $station->id,
        'access_token' => 'valid_token',
        'refresh_token' => 'refresh_token',
        'expires_at' => now()->addHour(),
    ]);

    $module = NetatmoModule::create([
        'netatmo_station_id' => $station->id,
        'module_id' => 'module_outdoor',
        'module_name' => 'Outdoor',
        'type' => 'NAModule1',
        'data_type' => ['Temperature'],
        'is_active' => true,
    ]);

    $response = actingAs($user)->getJson(route('api.netatmo.module.measurements.debug', [
        'uuid' => $station->uuid,
        'moduleId' => 'module_outdoor',
    ]))
        ->assertOk();

    $params = $response->json('debug_info.api_request.params');
    expect($params)->toHaveKey('module_id');
    expect($params['module_id'])->toBe('module_outdoor');
});
