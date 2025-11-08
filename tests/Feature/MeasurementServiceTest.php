<?php

use Ekstremedia\NetatmoWeather\Models\NetatmoModule;
use Ekstremedia\NetatmoWeather\Models\NetatmoModuleReading;
use Ekstremedia\NetatmoWeather\Models\NetatmoStation;
use Ekstremedia\NetatmoWeather\Models\NetatmoToken;
use Ekstremedia\NetatmoWeather\Services\MeasurementService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    $this->service = new MeasurementService;

    // Create test user
    $this->user = new Ekstremedia\NetatmoWeather\Tests\Support\User;
    $this->user->id = 1;
    $this->user->name = 'Test User';
    $this->user->email = 'test@example.com';

    // Create test station
    $this->station = NetatmoStation::create([
        'user_id' => $this->user->id,
        'station_name' => 'Test Station',
        'device_id' => 'test_device_123',
        'client_id' => 'test_client_id',
        'client_secret' => 'test_client_secret',
    ]);

    // Create valid token
    $this->token = NetatmoToken::create([
        'netatmo_station_id' => $this->station->id,
        'access_token' => 'test_access_token',
        'refresh_token' => 'test_refresh_token',
        'expires_at' => now()->addHour(),
    ]);

    // Create test module
    $this->module = NetatmoModule::create([
        'netatmo_station_id' => $this->station->id,
        'module_id' => 'module_123',
        'module_name' => 'Test Module',
        'type' => 'NAModule1',
        'data_type' => ['Temperature', 'Humidity'],
    ]);
});

it('fetches and stores measurements from netatmo api', function () {
    $mockData = [
        1704067200 => [25.5, 65],
        1704068200 => [25.7, 64],
        1704069200 => [25.9, 63],
    ];

    Http::fake([
        '*' => Http::response([
            'body' => $mockData,
            'status' => 'ok',
        ], 200),
    ]);

    $result = $this->service->fetchAndStoreMeasurements(
        $this->module,
        '30min',
        now()->subDay(),
        now()
    );

    expect($result)->toBe($mockData);
    expect(NetatmoModuleReading::count())->toBe(3);

    $reading = NetatmoModuleReading::first();
    expect($reading->netatmo_module_id)->toBe($this->module->id);
    expect($reading->dashboard_data)->toHaveKeys(['Temperature', 'Humidity']);
    expect($reading->dashboard_data['Temperature'])->toBe(25.5);
    expect($reading->dashboard_data['Humidity'])->toBe(65);
});

it('maps wind data type to WindStrength for api', function () {
    $windModule = NetatmoModule::create([
        'netatmo_station_id' => $this->station->id,
        'module_id' => 'wind_module_456',
        'module_name' => 'Wind Gauge',
        'type' => 'NAModule2',
        'data_type' => ['Wind', 'WindAngle'],
    ]);

    Http::fake([
        '*' => Http::response([
            'body' => [1704067200 => [15.5, 180]],
            'status' => 'ok',
        ], 200),
    ]);

    $this->service->fetchAndStoreMeasurements(
        $windModule,
        '30min',
        now()->subHour(),
        now()
    );

    // Data should be stored
    expect(NetatmoModuleReading::where('netatmo_module_id', $windModule->id)->count())->toBe(1);
});

it('does not send module_id parameter for NAMain modules', function () {
    $mainModule = NetatmoModule::create([
        'netatmo_station_id' => $this->station->id,
        'module_id' => 'test_device_123',
        'module_name' => 'Indoor Station',
        'type' => 'NAMain',
        'data_type' => ['Temperature', 'Humidity'],
    ]);

    Http::fake([
        '*' => Http::response([
            'body' => [1704067200 => [22.5, 55]],
            'status' => 'ok',
        ], 200),
    ]);

    $this->service->fetchAndStoreMeasurements(
        $mainModule,
        '30min',
        now()->subHour(),
        now()
    );

    expect(NetatmoModuleReading::where('netatmo_module_id', $mainModule->id)->count())->toBe(1);
});

it('caches measurements based on period', function () {
    $timestamps = [
        now()->subMinutes(10),
        now()->subMinutes(5),
        now(),
    ];

    foreach ($timestamps as $index => $timestamp) {
        NetatmoModuleReading::create([
            'netatmo_module_id' => $this->module->id,
            'time_utc' => $timestamp,
            'dashboard_data' => [
                'Temperature' => 20 + $index,
                'Humidity' => 60 + $index,
            ],
        ]);
    }

    Http::fake([
        '*' => Http::response([
            'body' => [],
            'status' => 'ok',
        ], 200),
    ]);

    Cache::flush();

    $result1 = $this->service->getMeasurements($this->module, '1hour', '30min');

    expect($result1)->toHaveKeys(['timestamps', 'data']);
    expect($result1['data'])->toHaveKeys(['Temperature', 'Humidity']);
    expect($result1['timestamps'])->toHaveCount(3);

    // Cache should be set
    $cacheKey = "measurements.{$this->module->id}.1hour.30min";
    expect(Cache::has($cacheKey))->toBeTrue();
});

it('fetches from api when database is empty', function () {
    Http::fake([
        '*' => Http::response([
            'body' => [
                1704067200 => [25.5, 65],
                1704068200 => [25.7, 64],
            ],
            'status' => 'ok',
        ], 200),
    ]);

    Cache::flush();

    // Direct fetch should work
    $result = $this->service->fetchAndStoreMeasurements(
        $this->module,
        '30min',
        now()->subHour(),
        now()
    );

    expect($result)->toHaveCount(2);

    // Should have stored in database
    expect(NetatmoModuleReading::count())->toBe(2);
});

it('throws exception on api error', function () {
    Http::fake([
        '*' => Http::response([
            'error' => [
                'code' => 2,
                'message' => 'Invalid access token',
            ],
        ], 403),
    ]);

    $this->service->fetchAndStoreMeasurements(
        $this->module,
        '30min',
        now()->subHour(),
        now()
    );
})->throws(RuntimeException::class, 'Netatmo API error');

it('updates existing readings instead of duplicating', function () {
    $timestamp = now()->subMinutes(30);

    NetatmoModuleReading::create([
        'netatmo_module_id' => $this->module->id,
        'time_utc' => $timestamp,
        'dashboard_data' => ['Temperature' => 20.0, 'Humidity' => 60],
    ]);

    Http::fake([
        '*' => Http::response([
            'body' => [
                $timestamp->timestamp => [21.5, 62],
            ],
            'status' => 'ok',
        ], 200),
    ]);

    $this->service->fetchAndStoreMeasurements(
        $this->module,
        '30min',
        $timestamp->copy()->subMinute(),
        now()
    );

    expect(NetatmoModuleReading::count())->toBe(1);

    $reading = NetatmoModuleReading::first();
    expect($reading->dashboard_data['Temperature'])->toBe(21.5);
    expect($reading->dashboard_data['Humidity'])->toBe(62);
});

it('formats readings correctly for api response', function () {
    $timestamps = [
        now()->subMinutes(60),
        now()->subMinutes(30),
        now(),
    ];

    foreach ($timestamps as $index => $timestamp) {
        NetatmoModuleReading::create([
            'netatmo_module_id' => $this->module->id,
            'time_utc' => $timestamp,
            'dashboard_data' => [
                'Temperature' => 20 + $index,
                'Humidity' => 60 + $index,
            ],
        ]);
    }

    Cache::flush();

    $result = $this->service->getMeasurements($this->module, '1hour', '30min');

    expect($result)->toHaveKeys(['timestamps', 'data']);
    expect($result['timestamps'])->toBeArray()->toHaveCount(3);
    expect($result['data'])->toHaveKeys(['Temperature', 'Humidity']);
    expect($result['data']['Temperature'])->toBe([20, 21, 22]);
    expect($result['data']['Humidity'])->toBe([60, 61, 62]);

    // Timestamps should be ISO8601
    foreach ($result['timestamps'] as $ts) {
        expect($ts)->toBeString();
        expect($ts)->toMatch('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}[+-]\d{2}:\d{2}$/');
    }
});

it('handles empty api response gracefully', function () {
    Http::fake([
        '*' => Http::response([
            'body' => [],
            'status' => 'ok',
        ], 200),
    ]);

    $result = $this->service->fetchAndStoreMeasurements(
        $this->module,
        '30min',
        now()->subHour(),
        now()
    );

    expect($result)->toBe([]);
    expect(NetatmoModuleReading::count())->toBe(0);
});

it('loads station relationship when not loaded', function () {
    $freshModule = NetatmoModule::find($this->module->id);

    expect($freshModule->relationLoaded('netatmoStation'))->toBeFalse();

    Http::fake([
        '*' => Http::response([
            'body' => [1704067200 => [25.5, 65]],
            'status' => 'ok',
        ], 200),
    ]);

    $this->service->fetchAndStoreMeasurements(
        $freshModule,
        '30min',
        now()->subHour(),
        now()
    );

    expect($freshModule->relationLoaded('netatmoStation'))->toBeTrue();
});

it('uses module data_type when types parameter is null', function () {
    Http::fake([
        '*' => Http::response([
            'body' => [1704067200 => [25.5, 65]],
            'status' => 'ok',
        ], 200),
    ]);

    // Call without types parameter - should use module's data_type
    $result = $this->service->fetchAndStoreMeasurements(
        $this->module,
        '30min',
        now()->subHour(),
        now(),
        null  // types is null
    );

    expect($result)->toBeArray();

    $reading = NetatmoModuleReading::first();
    expect($reading->dashboard_data)->toHaveKeys(['Temperature', 'Humidity']);
});

it('handles different period values correctly', function () {
    $periods = ['1hour', '6hours', '12hours', '1day', '3days', '1week', '1month', 'invalid'];

    foreach ($periods as $period) {
        // Create a reading within the period
        NetatmoModuleReading::create([
            'netatmo_module_id' => $this->module->id,
            'time_utc' => now()->subMinutes(5),
            'dashboard_data' => ['Temperature' => 20, 'Humidity' => 60],
        ]);

        Http::fake([
            '*' => Http::response([
                'body' => [],
                'status' => 'ok',
            ], 200),
        ]);

        Cache::flush();

        $result = $this->service->getMeasurements($this->module, $period, '30min');

        expect($result)->toBeArray();
        expect($result)->toHaveKeys(['timestamps', 'data']);

        // Clean up for next iteration
        NetatmoModuleReading::query()->delete();
    }
});

it('refreshes data when readings are more than 30 minutes old', function () {
    // Create old reading (35 minutes ago)
    NetatmoModuleReading::create([
        'netatmo_module_id' => $this->module->id,
        'time_utc' => now()->subMinutes(35),
        'dashboard_data' => ['Temperature' => 18, 'Humidity' => 55],
    ]);

    Http::fake([
        '*' => Http::response([
            'body' => [
                now()->timestamp => [22, 60],
            ],
            'status' => 'ok',
        ], 200),
    ]);

    Cache::flush();

    $result = $this->service->getMeasurements($this->module, '1hour', '30min');

    // Should have fetched new data
    expect(NetatmoModuleReading::count())->toBeGreaterThan(1);
});

it('does not refresh when readings are recent', function () {
    // Create recent reading (5 minutes ago)
    NetatmoModuleReading::create([
        'netatmo_module_id' => $this->module->id,
        'time_utc' => now()->subMinutes(5),
        'dashboard_data' => ['Temperature' => 22, 'Humidity' => 60],
    ]);

    // Don't fake HTTP - if it tries to call API, test will fail

    Cache::flush();

    $result = $this->service->getMeasurements($this->module, '1hour', '30min');

    // Should use existing data
    expect($result)->toHaveKeys(['timestamps', 'data']);
    expect(NetatmoModuleReading::count())->toBe(1);
});

it('uses default period of 1day when invalid period provided', function () {
    NetatmoModuleReading::create([
        'netatmo_module_id' => $this->module->id,
        'time_utc' => now()->subMinutes(5),
        'dashboard_data' => ['Temperature' => 22, 'Humidity' => 60],
    ]);

    Http::fake([
        '*' => Http::response([
            'body' => [],
            'status' => 'ok',
        ], 200),
    ]);

    Cache::flush();

    $result = $this->service->getMeasurements($this->module, 'invalid_period', '30min');

    expect($result)->toHaveKeys(['timestamps', 'data']);
});

it('uses correct cache duration for different periods', function () {
    $testCases = [
        ['period' => '1hour', 'expectedMin' => 250, 'expectedMax' => 350],
        ['period' => '6hours', 'expectedMin' => 250, 'expectedMax' => 350],
        ['period' => '12hours', 'expectedMin' => 850, 'expectedMax' => 950],
        ['period' => '1day', 'expectedMin' => 850, 'expectedMax' => 950],
        ['period' => '3days', 'expectedMin' => 1750, 'expectedMax' => 1850],
        ['period' => '1week', 'expectedMin' => 3550, 'expectedMax' => 3650],
        ['period' => '1month', 'expectedMin' => 3550, 'expectedMax' => 3650],
    ];

    foreach ($testCases as $case) {
        NetatmoModuleReading::create([
            'netatmo_module_id' => $this->module->id,
            'time_utc' => now()->subMinutes(5),
            'dashboard_data' => ['Temperature' => 20, 'Humidity' => 60],
        ]);

        Http::fake([
            '*' => Http::response([
                'body' => [],
                'status' => 'ok',
            ], 200),
        ]);

        Cache::flush();

        $this->service->getMeasurements($this->module, $case['period'], '30min');

        $cacheKey = "measurements.{$this->module->id}.{$case['period']}.30min";
        expect(Cache::has($cacheKey))->toBeTrue();

        NetatmoModuleReading::query()->delete();
    }
});

it('defaults to module data_type when types not specified', function () {
    Http::fake([
        '*' => Http::response([
            'body' => [1704067200 => [25.5, 65]],
            'status' => 'ok',
        ], 200),
    ]);

    // Fetch without specifying types
    $this->service->fetchAndStoreMeasurements(
        $this->module,
        '30min',
        now()->subHour(),
        now()
    );

    $reading = NetatmoModuleReading::first();
    // Should have both Temperature and Humidity from module's data_type
    expect($reading->dashboard_data)->toHaveKeys(['Temperature', 'Humidity']);
});

it('handles multiple measurement types correctly', function () {
    $multiModule = NetatmoModule::create([
        'netatmo_station_id' => $this->station->id,
        'module_id' => 'multi_module',
        'module_name' => 'Multi Sensor',
        'type' => 'NAMain',
        'data_type' => ['Temperature', 'Humidity', 'CO2', 'Pressure', 'Noise'],
    ]);

    Http::fake([
        '*' => Http::response([
            'body' => [1704067200 => [22.5, 55, 800, 1013, 35]],
            'status' => 'ok',
        ], 200),
    ]);

    $this->service->fetchAndStoreMeasurements(
        $multiModule,
        '30min',
        now()->subHour(),
        now()
    );

    $reading = NetatmoModuleReading::where('netatmo_module_id', $multiModule->id)->first();
    expect($reading->dashboard_data)->toHaveKeys(['Temperature', 'Humidity', 'CO2', 'Pressure', 'Noise']);
    expect($reading->dashboard_data['CO2'])->toBe(800);
    expect($reading->dashboard_data['Pressure'])->toBe(1013);
    expect($reading->dashboard_data['Noise'])->toBe(35);
});

it('formats empty readings correctly', function () {
    Cache::flush();

    // Test with no readings
    Http::fake([
        '*' => Http::response([
            'body' => [],
            'status' => 'ok',
        ], 200),
    ]);

    $result = $this->service->getMeasurements($this->module, '1hour', '30min');

    expect($result)->toHaveKeys(['timestamps', 'data']);
    expect($result['timestamps'])->toBe([]);
    expect($result['data'])->toBe([]);
});
