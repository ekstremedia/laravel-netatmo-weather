<?php

use Ekstremedia\NetatmoWeather\Models\NetatmoStation;
use Ekstremedia\NetatmoWeather\Models\NetatmoToken;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

it('encrypts attributes on set', function () {
    $station = NetatmoStation::create([
        'user_id' => 1,
        'station_name' => 'Test Station',
        'client_id' => 'test_client_id',
        'client_secret' => 'test_client_secret',
    ]);

    // Check that the value in the database is encrypted
    $rawValue = DB::table('netatmo_stations')
        ->where('id', $station->id)
        ->value('client_id');

    expect($rawValue)->not->toBe('test_client_id')
        ->and(Crypt::decryptString($rawValue))->toBe('test_client_id');
});

it('decrypts attributes on get', function () {
    $station = NetatmoStation::create([
        'user_id' => 1,
        'station_name' => 'Test Station',
        'client_id' => 'test_client_id',
        'client_secret' => 'test_client_secret',
    ]);

    expect($station->client_id)->toBe('test_client_id')
        ->and($station->client_secret)->toBe('test_client_secret');
});

it('does not encrypt null values', function () {
    $station = new NetatmoStation([
        'user_id' => 1,
        'station_name' => 'Test Station',
        'client_id' => 'test_client_id',
        'client_secret' => 'test_client_secret',
        'redirect_uri' => null,
    ]);

    // Verify that null values remain null and don't cause encryption errors
    expect($station->redirect_uri)->toBeNull();
});

it('returns null for corrupted encrypted data', function () {
    $station = NetatmoStation::create([
        'user_id' => 1,
        'station_name' => 'Test Station',
        'client_id' => 'test_client_id',
        'client_secret' => 'test_client_secret',
    ]);

    // Corrupt the encrypted data in the database
    DB::table('netatmo_stations')
        ->where('id', $station->id)
        ->update(['client_id' => 'corrupted_data']);

    // Refresh the model to get the corrupted data
    $station = NetatmoStation::find($station->id);

    expect($station->client_id)->toBeNull();
});

it('encrypts token attributes', function () {
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

    // Check database values are encrypted
    $rawAccessToken = DB::table('netatmo_tokens')
        ->where('id', $token->id)
        ->value('access_token');

    expect($rawAccessToken)->not->toBe('test_access_token')
        ->and(Crypt::decryptString($rawAccessToken))->toBe('test_access_token');
});

it('decrypts token attributes', function () {
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

    expect($token->access_token)->toBe('test_access_token')
        ->and($token->refresh_token)->toBe('test_refresh_token');
});
