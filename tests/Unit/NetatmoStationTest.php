<?php

use Ekstremedia\NetatmoWeather\Models\NetatmoModule;
use Ekstremedia\NetatmoWeather\Models\NetatmoStation;
use Ekstremedia\NetatmoWeather\Models\NetatmoToken;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;

it('can create a netatmo station', function () {
    $station = NetatmoStation::create([
        'user_id' => 1,
        'station_name' => 'Test Station',
        'client_id' => 'test_client_id',
        'client_secret' => 'test_client_secret',
    ]);

    assertDatabaseHas('netatmo_stations', [
        'station_name' => 'Test Station',
        'user_id' => 1,
    ]);

    expect($station->uuid)->not->toBeNull();
});

it('encrypts sensitive fields', function () {
    $station = NetatmoStation::create([
        'user_id' => 1,
        'station_name' => 'Test Station',
        'client_id' => 'test_client_id',
        'client_secret' => 'test_client_secret',
    ]);

    // The encrypted value should not match the plain text
    expect($station->getRawOriginal('client_id'))->not->toBe('test_client_id');
    expect($station->getRawOriginal('client_secret'))->not->toBe('test_client_secret');

    // But the decrypted value should match
    expect($station->client_id)->toBe('test_client_id');
    expect($station->client_secret)->toBe('test_client_secret');
});

it('uses uuid as route key', function () {
    $station = NetatmoStation::create([
        'user_id' => 1,
        'station_name' => 'Test Station',
        'client_id' => 'test_client_id',
        'client_secret' => 'test_client_secret',
    ]);

    expect($station->getRouteKeyName())->toBe('uuid');
});

it('has one token', function () {
    $station = NetatmoStation::create([
        'user_id' => 1,
        'station_name' => 'Test Station',
        'client_id' => 'test_client_id',
        'client_secret' => 'test_client_secret',
    ]);

    $token = NetatmoToken::create([
        'netatmo_station_id' => $station->id,
        'access_token' => 'test_access_token',
        'refresh_token' => 'test_refresh_token',
        'expires_at' => now()->addHour(),
    ]);

    expect($station->token)
        ->toBeInstanceOf(NetatmoToken::class)
        ->id->toBe($token->id);
});

it('has many modules', function () {
    $station = NetatmoStation::create([
        'user_id' => 1,
        'station_name' => 'Test Station',
        'client_id' => 'test_client_id',
        'client_secret' => 'test_client_secret',
    ]);

    NetatmoModule::create([
        'netatmo_station_id' => $station->id,
        'module_id' => 'module_1',
        'module_name' => 'Indoor Module',
        'type' => 'NAMain',
        'data_type' => ['Temperature', 'Humidity'],
    ]);

    NetatmoModule::create([
        'netatmo_station_id' => $station->id,
        'module_id' => 'module_2',
        'module_name' => 'Outdoor Module',
        'type' => 'NAModule1',
        'data_type' => ['Temperature', 'Humidity'],
    ]);

    expect($station->modules)->toHaveCount(2);
});

it('cascades delete to token and modules', function () {
    $station = NetatmoStation::create([
        'user_id' => 1,
        'station_name' => 'Test Station',
        'client_id' => 'test_client_id',
        'client_secret' => 'test_client_secret',
    ]);

    $token = NetatmoToken::create([
        'netatmo_station_id' => $station->id,
        'access_token' => 'test_access_token',
        'refresh_token' => 'test_refresh_token',
        'expires_at' => now()->addHour(),
    ]);

    $module = NetatmoModule::create([
        'netatmo_station_id' => $station->id,
        'module_id' => 'module_1',
        'module_name' => 'Indoor Module',
        'type' => 'NAMain',
        'data_type' => ['Temperature', 'Humidity'],
    ]);

    $station->delete();

    assertDatabaseMissing('netatmo_stations', ['id' => $station->id]);
    assertDatabaseMissing('netatmo_tokens', ['id' => $token->id]);
    assertDatabaseMissing('netatmo_modules', ['id' => $module->id]);
});

it('defaults is_public to false', function () {
    $station = NetatmoStation::create([
        'user_id' => 1,
        'station_name' => 'Test Station',
        'client_id' => 'test_client_id',
        'client_secret' => 'test_client_secret',
    ]);

    $station->refresh();

    expect($station->is_public)->toBeFalse();
});

it('can create a public station', function () {
    $station = NetatmoStation::create([
        'user_id' => 1,
        'station_name' => 'Public Station',
        'client_id' => 'test_client_id',
        'client_secret' => 'test_client_secret',
        'is_public' => true,
    ]);

    expect($station->is_public)->toBeTrue();

    assertDatabaseHas('netatmo_stations', [
        'id' => $station->id,
        'is_public' => true,
    ]);
});

it('casts is_public to boolean', function () {
    $station = NetatmoStation::create([
        'user_id' => 1,
        'station_name' => 'Test Station',
        'client_id' => 'test_client_id',
        'client_secret' => 'test_client_secret',
        'is_public' => 1, // integer
    ]);

    expect($station->is_public)->toBeBool();
    expect($station->is_public)->toBeTrue();
});
