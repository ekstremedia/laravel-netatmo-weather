<?php

use Ekstremedia\NetatmoWeather\Models\NetatmoModule;
use Ekstremedia\NetatmoWeather\Models\NetatmoStation;
use Ekstremedia\NetatmoWeather\Models\NetatmoToken;
use Illuminate\Foundation\Auth\User as Authenticatable;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\delete;
use function Pest\Laravel\get;
use function Pest\Laravel\patch;
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

it('can toggle public access for a station', function () {
    $station = NetatmoStation::create([
        'user_id' => 1,
        'station_name' => 'Test Station',
        'client_id' => 'test_client_id',
        'client_secret' => 'test_client_secret',
        'is_public' => false,
    ]);

    post(route('netatmo.toggle-public', $station->uuid))
        ->assertOk()
        ->assertJson([
            'success' => true,
            'is_public' => true,
        ]);

    assertDatabaseHas('netatmo_stations', [
        'id' => $station->id,
        'is_public' => true,
    ]);
});

it('can store a station with public access enabled', function () {
    $data = [
        'station_name' => 'Public Station',
        'client_id' => 'client_id',
        'client_secret' => 'client_secret',
        'is_public' => true,
    ];

    post(route('netatmo.store'), $data)
        ->assertRedirect();

    assertDatabaseHas('netatmo_stations', [
        'station_name' => 'Public Station',
        'is_public' => true,
    ]);
});

it('can update station public access via form', function () {
    $station = NetatmoStation::create([
        'user_id' => 1,
        'station_name' => 'Test Station',
        'client_id' => 'test_client_id',
        'client_secret' => 'test_client_secret',
        'is_public' => false,
    ]);

    $data = [
        'station_name' => 'Test Station',
        'client_id' => 'test_client_id',
        'client_secret' => 'test_client_secret',
        'is_public' => true,
    ];

    put(route('netatmo.update', $station->uuid), $data)
        ->assertRedirect();

    assertDatabaseHas('netatmo_stations', [
        'id' => $station->id,
        'is_public' => true,
    ]);
});

it('returns 404 for non-public station on public route', function () {
    $station = NetatmoStation::create([
        'user_id' => 1,
        'station_name' => 'Private Station',
        'client_id' => 'test_client_id',
        'client_secret' => 'test_client_secret',
        'is_public' => false,
    ]);

    // Test public route without authentication by creating a fresh request
    $this->app['auth']->forgetGuards();

    get(route('netatmo.public', $station->uuid))
        ->assertNotFound();
});

it('returns 503 for public station without valid token', function () {
    $station = NetatmoStation::create([
        'user_id' => 1,
        'station_name' => 'Public Station',
        'client_id' => 'test_client_id',
        'client_secret' => 'test_client_secret',
        'is_public' => true,
    ]);

    // Test public route without authentication
    $this->app['auth']->forgetGuards();

    get(route('netatmo.public', $station->uuid))
        ->assertStatus(503);
});

// Module Management Tests

it('can reactivate an archived module', function () {
    $station = NetatmoStation::create([
        'user_id' => 1,
        'station_name' => 'Test Station',
        'client_id' => 'test_client_id',
        'client_secret' => 'test_client_secret',
    ]);

    $module = NetatmoModule::create([
        'netatmo_station_id' => $station->id,
        'module_id' => '02:00:00:58:71:60',
        'module_name' => 'Outdoor Module',
        'type' => 'NAModule1',
        'data_type' => ['Temperature', 'Humidity'],
        'is_active' => false, // Archived
    ]);

    patch(route('netatmo.modules.activate', [$station->uuid, $module->id]))
        ->assertRedirect(route('netatmo.show', $station->uuid))
        ->assertSessionHas('success');

    assertDatabaseHas('netatmo_modules', [
        'id' => $module->id,
        'is_active' => true,
    ]);
});

it('can delete an archived module', function () {
    $station = NetatmoStation::create([
        'user_id' => 1,
        'station_name' => 'Test Station',
        'client_id' => 'test_client_id',
        'client_secret' => 'test_client_secret',
    ]);

    $module = NetatmoModule::create([
        'netatmo_station_id' => $station->id,
        'module_id' => '02:00:00:58:71:60',
        'module_name' => 'Old Module',
        'type' => 'NAModule1',
        'data_type' => ['Temperature', 'Humidity'],
        'is_active' => false, // Archived
    ]);

    delete(route('netatmo.modules.destroy', [$station->uuid, $module->id]))
        ->assertRedirect(route('netatmo.show', $station->uuid))
        ->assertSessionHas('success');

    assertDatabaseMissing('netatmo_modules', [
        'id' => $module->id,
    ]);
});

it('cannot delete active modules', function () {
    $station = NetatmoStation::create([
        'user_id' => 1,
        'station_name' => 'Test Station',
        'client_id' => 'test_client_id',
        'client_secret' => 'test_client_secret',
    ]);

    $module = NetatmoModule::create([
        'netatmo_station_id' => $station->id,
        'module_id' => '02:00:00:58:71:60',
        'module_name' => 'Active Module',
        'type' => 'NAModule1',
        'data_type' => ['Temperature', 'Humidity'],
        'is_active' => true, // Active
    ]);

    delete(route('netatmo.modules.destroy', [$station->uuid, $module->id]))
        ->assertRedirect(route('netatmo.show', $station->uuid))
        ->assertSessionHas('error');

    assertDatabaseHas('netatmo_modules', [
        'id' => $module->id,
    ]);
});

it('returns 404 when activating module from wrong station', function () {
    $station1 = NetatmoStation::create([
        'user_id' => 1,
        'station_name' => 'Station 1',
        'client_id' => 'test_client_id',
        'client_secret' => 'test_client_secret',
    ]);

    $station2 = NetatmoStation::create([
        'user_id' => 1,
        'station_name' => 'Station 2',
        'client_id' => 'test_client_id',
        'client_secret' => 'test_client_secret',
    ]);

    $module = NetatmoModule::create([
        'netatmo_station_id' => $station2->id, // Belongs to station2
        'module_id' => '02:00:00:58:71:60',
        'module_name' => 'Module',
        'type' => 'NAModule1',
        'data_type' => ['Temperature'],
        'is_active' => false,
    ]);

    // Try to activate via station1 (should fail)
    patch(route('netatmo.modules.activate', [$station1->uuid, $module->id]))
        ->assertNotFound();
});

it('returns 404 when deleting module from wrong station', function () {
    $station1 = NetatmoStation::create([
        'user_id' => 1,
        'station_name' => 'Station 1',
        'client_id' => 'test_client_id',
        'client_secret' => 'test_client_secret',
    ]);

    $station2 = NetatmoStation::create([
        'user_id' => 1,
        'station_name' => 'Station 2',
        'client_id' => 'test_client_id',
        'client_secret' => 'test_client_secret',
    ]);

    $module = NetatmoModule::create([
        'netatmo_station_id' => $station2->id, // Belongs to station2
        'module_id' => '02:00:00:58:71:60',
        'module_name' => 'Module',
        'type' => 'NAModule1',
        'data_type' => ['Temperature'],
        'is_active' => false,
    ]);

    // Try to delete via station1 (should fail)
    delete(route('netatmo.modules.destroy', [$station1->uuid, $module->id]))
        ->assertNotFound();
});
