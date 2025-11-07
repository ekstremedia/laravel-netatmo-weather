<?php

use Ekstremedia\NetatmoWeather\Models\NetatmoStation;
use Ekstremedia\NetatmoWeather\Models\NetatmoToken;
use Illuminate\Foundation\Auth\User as Authenticatable;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\delete;
use function Pest\Laravel\get;
use function Pest\Laravel\post;
use function Pest\Laravel\put;

beforeEach(function () {
    // Create a simple test user that implements Authenticatable
    $user = new class extends Authenticatable
    {
        protected $fillable = ['id', 'name', 'email'];

        public function getAuthIdentifier()
        {
            return 1;
        }
    };

    $user->id = 1;

    actingAs($user);
});

it('can display index page', function () {
    $station = NetatmoStation::create([
        'user_id' => 1,
        'station_name' => 'Test Station',
        'client_id' => 'test_client_id',
        'client_secret' => 'test_client_secret',
    ]);

    get(route('netatmo.index'))
        ->assertOk()
        ->assertViewIs('netatmoweather::netatmo.index')
        ->assertViewHas('weatherStations')
        ->assertSee('Test Station');
});

it('can display create form', function () {
    get(route('netatmo.create'))
        ->assertOk()
        ->assertViewIs('netatmoweather::netatmo.form')
        ->assertViewHas('fields');
});

it('can store a new station', function () {
    $data = [
        'station_name' => 'New Station',
        'client_id' => 'new_client_id',
        'client_secret' => 'new_client_secret',
    ];

    post(route('netatmo.store'), $data)
        ->assertRedirect();

    assertDatabaseHas('netatmo_stations', [
        'station_name' => 'New Station',
        'user_id' => 1,
    ]);
});

it('validates required fields on store', function () {
    post(route('netatmo.store'), [])
        ->assertSessionHasErrors(['station_name', 'client_id', 'client_secret']);
});

it('can display edit form', function () {
    $station = NetatmoStation::create([
        'user_id' => 1,
        'station_name' => 'Test Station',
        'client_id' => 'test_client_id',
        'client_secret' => 'test_client_secret',
    ]);

    get(route('netatmo.edit', $station->uuid))
        ->assertOk()
        ->assertViewIs('netatmoweather::netatmo.form')
        ->assertViewHas('weatherStation', $station);
});

it('can update a station', function () {
    $station = NetatmoStation::create([
        'user_id' => 1,
        'station_name' => 'Test Station',
        'client_id' => 'test_client_id',
        'client_secret' => 'test_client_secret',
    ]);

    $data = [
        'station_name' => 'Updated Station',
        'client_id' => 'updated_client_id',
        'client_secret' => 'updated_client_secret',
    ];

    put(route('netatmo.update', $station->uuid), $data)
        ->assertRedirect(route('netatmo.index'));

    assertDatabaseHas('netatmo_stations', [
        'station_name' => 'Updated Station',
    ]);
});

it('can delete a station', function () {
    $station = NetatmoStation::create([
        'user_id' => 1,
        'station_name' => 'Test Station',
        'client_id' => 'test_client_id',
        'client_secret' => 'test_client_secret',
    ]);

    delete(route('netatmo.destroy', $station->uuid))
        ->assertRedirect(route('netatmo.index'));

    assertDatabaseMissing('netatmo_stations', [
        'id' => $station->id,
    ]);
});

it('redirects to authenticate when showing station without token', function () {
    $station = NetatmoStation::create([
        'user_id' => 1,
        'station_name' => 'Test Station',
        'client_id' => 'test_client_id',
        'client_secret' => 'test_client_secret',
    ]);

    get(route('netatmo.show', $station->uuid))
        ->assertRedirect(route('netatmo.authenticate', $station->uuid))
        ->assertSessionHas('error');
});

it('redirects to authenticate when showing station with invalid token', function () {
    $station = NetatmoStation::create([
        'user_id' => 1,
        'station_name' => 'Test Station',
        'client_id' => 'test_client_id',
        'client_secret' => 'test_client_secret',
    ]);

    // Create expired token
    NetatmoToken::create([
        'netatmo_station_id' => $station->id,
        'access_token' => 'expired_token',
        'refresh_token' => 'refresh_token',
        'expires_at' => now()->subHour(),
    ]);

    get(route('netatmo.show', $station->uuid))
        ->assertRedirect(route('netatmo.authenticate', $station->uuid));
});
