<?php

use Ekstremedia\NetatmoWeather\Exceptions\InvalidApiResponseException;
use Ekstremedia\NetatmoWeather\Exceptions\TokenRefreshException;

describe('TokenRefreshException', function () {
    it('creates no refresh token exception', function () {
        $exception = TokenRefreshException::noRefreshToken();

        expect($exception)->toBeInstanceOf(TokenRefreshException::class)
            ->getMessage()->toBe('No refresh token available for authentication.');
    });

    it('creates station not found exception', function () {
        $exception = TokenRefreshException::stationNotFound();

        expect($exception)->toBeInstanceOf(TokenRefreshException::class)
            ->getMessage()->toBe('Associated weather station not found.');
    });

    it('creates api error exception', function () {
        $exception = TokenRefreshException::apiError('Connection timeout');

        expect($exception)->toBeInstanceOf(TokenRefreshException::class)
            ->getMessage()->toBe('Netatmo API error: Connection timeout');
    });
});

describe('InvalidApiResponseException', function () {
    it('creates no devices exception', function () {
        $exception = InvalidApiResponseException::noDevices();

        expect($exception)->toBeInstanceOf(InvalidApiResponseException::class)
            ->getMessage()->toBe('No devices found in Netatmo API response.');
    });

    it('creates missing required fields exception', function () {
        $exception = InvalidApiResponseException::missingRequiredFields('_id, module_name');

        expect($exception)->toBeInstanceOf(InvalidApiResponseException::class)
            ->getMessage()->toBe('Required fields missing from API response: _id, module_name');
    });
});
