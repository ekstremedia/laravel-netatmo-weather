<?php

use Ekstremedia\NetatmoWeather\Models\NetatmoStation;
use Ekstremedia\NetatmoWeather\Models\NetatmoToken;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    $this->user = createUser();
    $this->actingAs($this->user);
});

it('can initiate authentication', function () {
    $station = NetatmoStation::create([
        'user_id' => $this->user->id,
        'station_name' => 'Test Station',
        'client_id' => 'test_client_id',
        'client_secret' => 'test_client_secret',
        'redirect_uri' => 'https://example.com/callback',
    ]);

    $response = $this->get(route('netatmo.authenticate', $station));

    $response->assertRedirect();
    $response->assertRedirectContains(config('netatmo-weather.netatmo_auth_url'));

    // Verify state was stored in session
    $this->assertTrue(session()->has('netatmo_oauth_state_'.$station->id));
});

it('prevents authentication for unauthorized user', function () {
    $otherUser = createUser();
    $station = NetatmoStation::create([
        'user_id' => $otherUser->id,
        'station_name' => 'Test Station',
        'client_id' => 'test_client_id',
        'client_secret' => 'test_client_secret',
        'redirect_uri' => 'https://example.com/callback',
    ]);

    $response = $this->get(route('netatmo.authenticate', $station));

    $response->assertForbidden();
});

it('handles callback successfully', function () {
    $station = NetatmoStation::create([
        'user_id' => $this->user->id,
        'station_name' => 'Test Station',
        'client_id' => 'test_client_id',
        'client_secret' => 'test_client_secret',
        'redirect_uri' => 'https://example.com/callback',
    ]);

    // Set state in session
    $state = 'test_state_token';
    session()->put('netatmo_oauth_state_'.$station->id, $state);

    Http::fake([
        config('netatmo-weather.netatmo_token_url') => Http::response([
            'access_token' => 'test_access_token',
            'refresh_token' => 'test_refresh_token',
            'expires_in' => 10800,
        ], 200),
    ]);

    $response = $this->get(route('netatmo.callback', [
        'weatherstation' => $station,
        'code' => 'test_authorization_code',
        'state' => $state,
    ]));

    $response->assertRedirect(route('netatmo.index'));
    $response->assertSessionHas('success');

    // Verify token was created
    $station->refresh();
    expect($station->token)->not->toBeNull()
        ->and($station->token->access_token)->toBe('test_access_token')
        ->and($station->token->refresh_token)->toBe('test_refresh_token');
});

it('rejects callback with invalid state', function () {
    $station = NetatmoStation::create([
        'user_id' => $this->user->id,
        'station_name' => 'Test Station',
        'client_id' => 'test_client_id',
        'client_secret' => 'test_client_secret',
        'redirect_uri' => 'https://example.com/callback',
    ]);

    session()->put('netatmo_oauth_state_'.$station->id, 'correct_state');

    $response = $this->get(route('netatmo.callback', [
        'weatherstation' => $station,
        'code' => 'test_authorization_code',
        'state' => 'wrong_state',
    ]));

    $response->assertRedirect(route('netatmo.index'));
    $response->assertSessionHas('error', 'Invalid authentication state. Please try again.');
});

it('rejects callback with missing state', function () {
    $station = NetatmoStation::create([
        'user_id' => $this->user->id,
        'station_name' => 'Test Station',
        'client_id' => 'test_client_id',
        'client_secret' => 'test_client_secret',
        'redirect_uri' => 'https://example.com/callback',
    ]);

    $response = $this->get(route('netatmo.callback', [
        'weatherstation' => $station,
        'code' => 'test_authorization_code',
        'state' => 'any_state',
    ]));

    $response->assertRedirect(route('netatmo.index'));
    $response->assertSessionHas('error');
});

it('handles token api error gracefully', function () {
    $station = NetatmoStation::create([
        'user_id' => $this->user->id,
        'station_name' => 'Test Station',
        'client_id' => 'test_client_id',
        'client_secret' => 'test_client_secret',
        'redirect_uri' => 'https://example.com/callback',
    ]);

    $state = 'test_state_token';
    session()->put('netatmo_oauth_state_'.$station->id, $state);

    Http::fake([
        config('netatmo-weather.netatmo_token_url') => Http::response([], 400),
    ]);

    $response = $this->get(route('netatmo.callback', [
        'weatherstation' => $station,
        'code' => 'test_authorization_code',
        'state' => $state,
    ]));

    $response->assertRedirect(route('netatmo.index'));
    $response->assertSessionHas('error');
});

it('prevents callback for unauthorized user', function () {
    $otherUser = createUser();
    $station = NetatmoStation::create([
        'user_id' => $otherUser->id,
        'station_name' => 'Test Station',
        'client_id' => 'test_client_id',
        'client_secret' => 'test_client_secret',
        'redirect_uri' => 'https://example.com/callback',
    ]);

    $response = $this->get(route('netatmo.callback', [
        'weatherstation' => $station,
        'code' => 'test_code',
        'state' => 'test_state',
    ]));

    $response->assertForbidden();
});

it('updates existing token instead of creating duplicate', function () {
    $station = NetatmoStation::create([
        'user_id' => $this->user->id,
        'station_name' => 'Test Station',
        'client_id' => 'test_client_id',
        'client_secret' => 'test_client_secret',
        'redirect_uri' => 'https://example.com/callback',
    ]);

    // Create existing token
    NetatmoToken::create([
        'netatmo_station_id' => $station->id,
        'access_token' => 'old_access_token',
        'refresh_token' => 'old_refresh_token',
        'expires_at' => now()->subHour(),
    ]);

    $state = 'test_state_token';
    session()->put('netatmo_oauth_state_'.$station->id, $state);

    Http::fake([
        config('netatmo-weather.netatmo_token_url') => Http::response([
            'access_token' => 'new_access_token',
            'refresh_token' => 'new_refresh_token',
            'expires_in' => 10800,
        ], 200),
    ]);

    $response = $this->get(route('netatmo.callback', [
        'weatherstation' => $station,
        'code' => 'test_authorization_code',
        'state' => $state,
    ]));

    $response->assertRedirect(route('netatmo.index'));

    // Verify only one token exists
    expect(NetatmoToken::where('netatmo_station_id', $station->id)->count())->toBe(1);

    $station->refresh();
    expect($station->token->access_token)->toBe('new_access_token');
});
