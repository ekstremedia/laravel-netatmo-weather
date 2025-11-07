<?php

use Ekstremedia\NetatmoWeather\Models\NetatmoStation;
use Ekstremedia\NetatmoWeather\Models\NetatmoToken;

it('can check if token is valid', function () {
    $station = NetatmoStation::create([
        'user_id' => 1,
        'station_name' => 'Test Station',
        'client_id' => 'test_client_id',
        'client_secret' => 'test_client_secret',
    ]);

    $validToken = NetatmoToken::create([
        'netatmo_station_id' => $station->id,
        'access_token' => 'test_access_token',
        'refresh_token' => 'test_refresh_token',
        'expires_at' => now()->addHour(),
    ]);

    expect($validToken->hasValidToken())->toBeTrue();
});

it('detects expired tokens', function () {
    $station = NetatmoStation::create([
        'user_id' => 1,
        'station_name' => 'Test Station',
        'client_id' => 'test_client_id',
        'client_secret' => 'test_client_secret',
    ]);

    $expiredToken = NetatmoToken::create([
        'netatmo_station_id' => $station->id,
        'access_token' => 'test_access_token',
        'refresh_token' => 'test_refresh_token',
        'expires_at' => now()->subHour(),
    ]);

    expect($expiredToken->hasValidToken())->toBeFalse();
});

it('belongs to a station', function () {
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

    expect($token->netatmoStation)
        ->toBeInstanceOf(NetatmoStation::class)
        ->id->toBe($station->id);
});

it('casts expires_at to datetime', function () {
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

    expect($token->expires_at)->toBeInstanceOf(\Illuminate\Support\Carbon::class);
});
