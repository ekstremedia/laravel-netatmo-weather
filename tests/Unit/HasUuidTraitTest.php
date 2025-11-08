<?php

use Ekstremedia\NetatmoWeather\Models\NetatmoStation;
use Illuminate\Support\Str;

it('generates UUID automatically on model creation', function () {
    $station = NetatmoStation::create([
        'user_id' => 1,
        'station_name' => 'Test Station',
        'device_id' => 'device_123',
        'client_id' => 'client_123',
        'client_secret' => 'secret_123',
    ]);

    expect($station->uuid)->not->toBeNull();
    expect($station->uuid)->toMatch('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i');
});

it('does not override existing UUID when set directly', function () {
    $customUuid = Str::uuid()->toString();

    $station = new NetatmoStation([
        'user_id' => 1,
        'station_name' => 'Test Station',
        'device_id' => 'device_123',
        'client_id' => 'client_123',
        'client_secret' => 'secret_123',
    ]);

    // Set UUID directly before saving
    $station->uuid = $customUuid;
    $station->save();

    expect($station->uuid)->toBe($customUuid);
});

it('generates unique UUIDs for different models', function () {
    $station1 = NetatmoStation::create([
        'user_id' => 1,
        'station_name' => 'Station 1',
        'device_id' => 'device_1',
        'client_id' => 'client_123',
        'client_secret' => 'secret_123',
    ]);

    $station2 = NetatmoStation::create([
        'user_id' => 1,
        'station_name' => 'Station 2',
        'device_id' => 'device_2',
        'client_id' => 'client_123',
        'client_secret' => 'secret_123',
    ]);

    expect($station1->uuid)->not->toBe($station2->uuid);
});
