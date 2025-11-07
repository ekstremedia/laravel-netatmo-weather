<?php

use Ekstremedia\NetatmoWeather\Models\NetatmoStation;
use Ekstremedia\NetatmoWeather\Policies\NetatmoStationPolicy;

beforeEach(function () {
    $this->policy = new NetatmoStationPolicy;
    $this->user = createUser();
    $this->otherUser = createUser();
});

it('allows owner to view station', function () {
    $station = NetatmoStation::create([
        'user_id' => $this->user->id,
        'station_name' => 'Test Station',
        'client_id' => 'test_client_id',
        'client_secret' => 'test_client_secret',
    ]);

    expect($this->policy->view($this->user, $station))->toBeTrue();
});

it('denies non-owner to view station', function () {
    $station = NetatmoStation::create([
        'user_id' => $this->user->id,
        'station_name' => 'Test Station',
        'client_id' => 'test_client_id',
        'client_secret' => 'test_client_secret',
    ]);

    expect($this->policy->view($this->otherUser, $station))->toBeFalse();
});

it('allows owner to update station', function () {
    $station = NetatmoStation::create([
        'user_id' => $this->user->id,
        'station_name' => 'Test Station',
        'client_id' => 'test_client_id',
        'client_secret' => 'test_client_secret',
    ]);

    expect($this->policy->update($this->user, $station))->toBeTrue();
});

it('denies non-owner to update station', function () {
    $station = NetatmoStation::create([
        'user_id' => $this->user->id,
        'station_name' => 'Test Station',
        'client_id' => 'test_client_id',
        'client_secret' => 'test_client_secret',
    ]);

    expect($this->policy->update($this->otherUser, $station))->toBeFalse();
});

it('allows owner to delete station', function () {
    $station = NetatmoStation::create([
        'user_id' => $this->user->id,
        'station_name' => 'Test Station',
        'client_id' => 'test_client_id',
        'client_secret' => 'test_client_secret',
    ]);

    expect($this->policy->delete($this->user, $station))->toBeTrue();
});

it('denies non-owner to delete station', function () {
    $station = NetatmoStation::create([
        'user_id' => $this->user->id,
        'station_name' => 'Test Station',
        'client_id' => 'test_client_id',
        'client_secret' => 'test_client_secret',
    ]);

    expect($this->policy->delete($this->otherUser, $station))->toBeFalse();
});

it('allows owner to authenticate station', function () {
    $station = NetatmoStation::create([
        'user_id' => $this->user->id,
        'station_name' => 'Test Station',
        'client_id' => 'test_client_id',
        'client_secret' => 'test_client_secret',
    ]);

    expect($this->policy->authenticate($this->user, $station))->toBeTrue();
});

it('denies non-owner to authenticate station', function () {
    $station = NetatmoStation::create([
        'user_id' => $this->user->id,
        'station_name' => 'Test Station',
        'client_id' => 'test_client_id',
        'client_secret' => 'test_client_secret',
    ]);

    expect($this->policy->authenticate($this->otherUser, $station))->toBeFalse();
});
