<?php

use Ekstremedia\NetatmoWeather\Exceptions\TokenRefreshException;
use Ekstremedia\NetatmoWeather\Models\NetatmoStation;
use Ekstremedia\NetatmoWeather\Models\NetatmoToken;
use Ekstremedia\NetatmoWeather\Services\TokenRefreshService;
use Illuminate\Support\Facades\Http;

it('refreshes token successfully', function () {
    $station = NetatmoStation::create([
        'user_id' => 1,
        'station_name' => 'Test Station',
        'client_id' => 'test_client_id',
        'client_secret' => 'test_client_secret',
    ]);

    $token = NetatmoToken::create([
        'netatmo_station_id' => $station->id,
        'access_token' => 'old_access_token',
        'refresh_token' => 'test_refresh_token',
        'expires_at' => now()->subHour(),
    ]);

    Http::fake([
        config('netatmo-weather.netatmo_token_url') => Http::response([
            'access_token' => 'new_access_token',
            'refresh_token' => 'new_refresh_token',
            'expires_in' => 10800,
        ], 200),
    ]);

    $service = app(TokenRefreshService::class);
    $service->refreshToken($token);

    $token->refresh();

    expect($token->access_token)->toBe('new_access_token')
        ->and($token->refresh_token)->toBe('new_refresh_token')
        ->and($token->expires_at)->toBeInstanceOf(\Illuminate\Support\Carbon::class)
        ->and($token->expires_at->isFuture())->toBeTrue();
});

it('throws exception when no refresh token', function () {
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
        'expires_at' => now()->subHour(),
    ]);

    // Manually set refresh_token to null to simulate missing token
    $token->refresh_token = null;

    $service = app(TokenRefreshService::class);

    expect(fn () => $service->refreshToken($token))
        ->toThrow(TokenRefreshException::class, 'No refresh token available');
});

it('throws exception when station not found', function () {
    $token = new NetatmoToken([
        'netatmo_station_id' => 999,
        'access_token' => 'test_access_token',
        'refresh_token' => 'test_refresh_token',
        'expires_at' => now()->subHour(),
    ]);

    $service = app(TokenRefreshService::class);

    expect(fn () => $service->refreshToken($token))
        ->toThrow(TokenRefreshException::class, 'station not found');
});

it('throws exception on api error', function () {
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
        'expires_at' => now()->subHour(),
    ]);

    Http::fake([
        config('netatmo-weather.netatmo_token_url') => Http::response([], 400),
    ]);

    $service = app(TokenRefreshService::class);

    expect(fn () => $service->refreshToken($token))
        ->toThrow(TokenRefreshException::class, 'Failed to refresh token');
});

it('can call refresh token via model method', function () {
    $station = NetatmoStation::create([
        'user_id' => 1,
        'station_name' => 'Test Station',
        'client_id' => 'test_client_id',
        'client_secret' => 'test_client_secret',
    ]);

    $token = NetatmoToken::create([
        'netatmo_station_id' => $station->id,
        'access_token' => 'old_access_token',
        'refresh_token' => 'test_refresh_token',
        'expires_at' => now()->subHour(),
    ]);

    Http::fake([
        config('netatmo-weather.netatmo_token_url') => Http::response([
            'access_token' => 'new_access_token',
            'refresh_token' => 'new_refresh_token',
            'expires_in' => 10800,
        ], 200),
    ]);

    $token->refreshToken();
    $token->refresh();

    expect($token->access_token)->toBe('new_access_token');
});
