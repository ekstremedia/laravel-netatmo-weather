<?php

use Ekstremedia\NetatmoWeather\Models\NetatmoStation;
use Ekstremedia\NetatmoWeather\Models\NetatmoToken;
use Ekstremedia\NetatmoWeather\Services\NetatmoService;
use Illuminate\Support\Facades\Http;

use function Pest\Laravel\assertDatabaseHas;

it('fetches station data from api', function () {
    $station = NetatmoStation::create([
        'user_id' => 1,
        'station_name' => 'Test Station',
        'client_id' => 'test_client_id',
        'client_secret' => 'test_client_secret',
    ]);

    NetatmoToken::create([
        'netatmo_station_id' => $station->id,
        'access_token' => 'valid_access_token',
        'refresh_token' => 'valid_refresh_token',
        'expires_at' => now()->addHour(),
    ]);

    // Mock the HTTP response
    Http::fake([
        '*/getstationsdata' => Http::response([
            'body' => [
                'devices' => [
                    [
                        '_id' => 'device_1',
                        'type' => 'NAMain',
                        'module_name' => 'Indoor',
                        'data_type' => ['Temperature', 'Humidity'],
                        'dashboard_data' => [
                            'Temperature' => 22.5,
                            'Humidity' => 45,
                        ],
                        'modules' => [
                            [
                                '_id' => 'module_1',
                                'type' => 'NAModule1',
                                'module_name' => 'Outdoor',
                                'data_type' => ['Temperature', 'Humidity'],
                                'dashboard_data' => [
                                    'Temperature' => 15.5,
                                    'Humidity' => 65,
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'status' => 'ok',
        ], 200),
    ]);

    $service = new NetatmoService;
    $data = $service->getStationData($station);

    expect($data)->toBeArray()
        ->toHaveKey('body')
        ->and($data['body'])->toHaveKey('devices');
});

it('stores module data correctly', function () {
    $station = NetatmoStation::create([
        'user_id' => 1,
        'station_name' => 'Test Station',
        'client_id' => 'test_client_id',
        'client_secret' => 'test_client_secret',
    ]);

    $apiData = [
        'devices' => [
            [
                '_id' => 'device_1',
                'type' => 'NAMain',
                'module_name' => 'Indoor',
                'data_type' => ['Temperature', 'Humidity'],
                'dashboard_data' => [
                    'Temperature' => 22.5,
                    'Humidity' => 45,
                ],
                'firmware' => '178',
                'reachable' => true,
                'modules' => [],
            ],
        ],
    ];

    $service = new NetatmoService;
    $service->storeStationData($station, $apiData);

    expect($station->fresh()->modules)->toHaveCount(1);

    assertDatabaseHas('netatmo_modules', [
        'module_id' => 'device_1',
        'module_name' => 'Indoor',
        'type' => 'NAMain',
    ]);
});

it('returns cached data when recent', function () {
    $station = NetatmoStation::create([
        'user_id' => 1,
        'station_name' => 'Test Station',
        'client_id' => 'test_client_id',
        'client_secret' => 'test_client_secret',
    ]);

    NetatmoToken::create([
        'netatmo_station_id' => $station->id,
        'access_token' => 'valid_access_token',
        'refresh_token' => 'valid_refresh_token',
        'expires_at' => now()->addHour(),
    ]);

    // Create a recent module (less than 10 minutes old)
    $station->modules()->create([
        'module_id' => 'module_1',
        'module_name' => 'Indoor',
        'type' => 'NAMain',
        'data_type' => ['Temperature'],
        'updated_at' => now()->subMinutes(5), // 5 minutes ago
    ]);

    // HTTP should not be called
    Http::fake();

    $service = new NetatmoService;
    $data = $service->getStationData($station);

    // Verify no HTTP call was made
    Http::assertNothingSent();

    // Verify data was returned from cache
    expect($data)->toBeArray()
        ->toHaveKey('devices');
});

it('updates existing modules instead of duplicating', function () {
    $station = NetatmoStation::create([
        'user_id' => 1,
        'station_name' => 'Test Station',
        'client_id' => 'test_client_id',
        'client_secret' => 'test_client_secret',
    ]);

    // Create initial module
    $station->modules()->create([
        'module_id' => 'device_1',
        'module_name' => 'Old Name',
        'type' => 'NAMain',
        'data_type' => ['Temperature'],
    ]);

    // Updated data from API
    $apiData = [
        'devices' => [
            [
                '_id' => 'device_1',
                'type' => 'NAMain',
                'module_name' => 'New Name',
                'data_type' => ['Temperature', 'Humidity'],
                'dashboard_data' => [
                    'Temperature' => 23.0,
                ],
                'modules' => [],
            ],
        ],
    ];

    $service = new NetatmoService;
    $service->storeStationData($station, $apiData);

    // Should still have only 1 module, but with updated data
    expect($station->fresh()->modules)->toHaveCount(1);

    assertDatabaseHas('netatmo_modules', [
        'module_id' => 'device_1',
        'module_name' => 'New Name',
    ]);
});
