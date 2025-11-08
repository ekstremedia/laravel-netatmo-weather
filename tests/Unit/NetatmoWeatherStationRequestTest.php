<?php

use Ekstremedia\NetatmoWeather\Http\Requests\NetatmoWeatherStationRequest;
use Illuminate\Support\Facades\Validator;

it('validates required fields', function () {
    $request = new NetatmoWeatherStationRequest();
    $validator = Validator::make([], $request->rules());

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->has('station_name'))->toBeTrue();
    expect($validator->errors()->has('client_id'))->toBeTrue();
    expect($validator->errors()->has('client_secret'))->toBeTrue();
});

it('passes validation with valid data', function () {
    $request = new NetatmoWeatherStationRequest();
    $data = [
        'station_name' => 'Test Station',
        'client_id' => 'test_client_id',
        'client_secret' => 'test_client_secret',
    ];

    $validator = Validator::make($data, $request->rules());

    expect($validator->passes())->toBeTrue();
});

it('validates redirect_uri must be a URL', function () {
    $request = new NetatmoWeatherStationRequest();
    $data = [
        'station_name' => 'Test Station',
        'client_id' => 'test_client_id',
        'client_secret' => 'test_client_secret',
        'redirect_uri' => 'not-a-url',
    ];

    $validator = Validator::make($data, $request->rules());

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->has('redirect_uri'))->toBeTrue();
});

it('validates webhook_uri must be a URL', function () {
    $request = new NetatmoWeatherStationRequest();
    $data = [
        'station_name' => 'Test Station',
        'client_id' => 'test_client_id',
        'client_secret' => 'test_client_secret',
        'webhook_uri' => 'not-a-url',
    ];

    $validator = Validator::make($data, $request->rules());

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->has('webhook_uri'))->toBeTrue();
});

it('accepts valid URLs for redirect_uri and webhook_uri', function () {
    $request = new NetatmoWeatherStationRequest();
    $data = [
        'station_name' => 'Test Station',
        'client_id' => 'test_client_id',
        'client_secret' => 'test_client_secret',
        'redirect_uri' => 'https://example.com/callback',
        'webhook_uri' => 'https://example.com/webhook',
    ];

    $validator = Validator::make($data, $request->rules());

    expect($validator->passes())->toBeTrue();
});

it('validates is_public must be boolean', function () {
    $request = new NetatmoWeatherStationRequest();
    $data = [
        'station_name' => 'Test Station',
        'client_id' => 'test_client_id',
        'client_secret' => 'test_client_secret',
        'is_public' => 'not-a-boolean',
    ];

    $validator = Validator::make($data, $request->rules());

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->has('is_public'))->toBeTrue();
});

it('validates api_enabled must be boolean', function () {
    $request = new NetatmoWeatherStationRequest();
    $data = [
        'station_name' => 'Test Station',
        'client_id' => 'test_client_id',
        'client_secret' => 'test_client_secret',
        'api_enabled' => 'not-a-boolean',
    ];

    $validator = Validator::make($data, $request->rules());

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->has('api_enabled'))->toBeTrue();
});

it('validates api_token minimum length', function () {
    $request = new NetatmoWeatherStationRequest();
    $data = [
        'station_name' => 'Test Station',
        'client_id' => 'test_client_id',
        'client_secret' => 'test_client_secret',
        'api_token' => 'short',
    ];

    $validator = Validator::make($data, $request->rules());

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->has('api_token'))->toBeTrue();
});

it('validates api_token maximum length', function () {
    $request = new NetatmoWeatherStationRequest();
    $data = [
        'station_name' => 'Test Station',
        'client_id' => 'test_client_id',
        'client_secret' => 'test_client_secret',
        'api_token' => str_repeat('a', 256),
    ];

    $validator = Validator::make($data, $request->rules());

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->has('api_token'))->toBeTrue();
});

it('accepts api_token with valid length', function () {
    $request = new NetatmoWeatherStationRequest();
    $data = [
        'station_name' => 'Test Station',
        'client_id' => 'test_client_id',
        'client_secret' => 'test_client_secret',
        'api_token' => str_repeat('a', 32), // Exactly 32 chars
    ];

    $validator = Validator::make($data, $request->rules());

    expect($validator->passes())->toBeTrue();
});

it('validates station_name maximum length', function () {
    $request = new NetatmoWeatherStationRequest();
    $data = [
        'station_name' => str_repeat('a', 256),
        'client_id' => 'test_client_id',
        'client_secret' => 'test_client_secret',
    ];

    $validator = Validator::make($data, $request->rules());

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->has('station_name'))->toBeTrue();
});

it('validates client_id maximum length', function () {
    $request = new NetatmoWeatherStationRequest();
    $data = [
        'station_name' => 'Test Station',
        'client_id' => str_repeat('a', 256),
        'client_secret' => 'test_client_secret',
    ];

    $validator = Validator::make($data, $request->rules());

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->has('client_id'))->toBeTrue();
});

it('validates client_secret maximum length', function () {
    $request = new NetatmoWeatherStationRequest();
    $data = [
        'station_name' => 'Test Station',
        'client_id' => 'test_client_id',
        'client_secret' => str_repeat('a', 256),
    ];

    $validator = Validator::make($data, $request->rules());

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->has('client_secret'))->toBeTrue();
});

it('accepts boolean values for is_public', function () {
    $request = new NetatmoWeatherStationRequest();

    foreach ([true, false, 1, 0, '1', '0'] as $value) {
        $data = [
            'station_name' => 'Test Station',
            'client_id' => 'test_client_id',
            'client_secret' => 'test_client_secret',
            'is_public' => $value,
        ];

        $validator = Validator::make($data, $request->rules());
        expect($validator->passes())->toBeTrue();
    }
});

it('accepts boolean values for api_enabled', function () {
    $request = new NetatmoWeatherStationRequest();

    foreach ([true, false, 1, 0, '1', '0'] as $value) {
        $data = [
            'station_name' => 'Test Station',
            'client_id' => 'test_client_id',
            'client_secret' => 'test_client_secret',
            'api_enabled' => $value,
        ];

        $validator = Validator::make($data, $request->rules());
        expect($validator->passes())->toBeTrue();
    }
});
