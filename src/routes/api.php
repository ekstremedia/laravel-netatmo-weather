<?php

// src/routes/api.php
use Ekstremedia\NetatmoWeather\Http\Controllers\Api\WeatherStationApiController;
use Illuminate\Support\Facades\Route;

// API routes for weather station data
// These endpoints return JSON responses for programmatic access
// Access control is per-station via api_enabled and api_token fields

Route::get('/stations/{uuid}', [WeatherStationApiController::class, 'show'])
    ->name('api.netatmo.show');

Route::get('/stations/{uuid}/modules/{moduleId}', [WeatherStationApiController::class, 'module'])
    ->name('api.netatmo.module');

Route::get('/stations/{uuid}/measurements', [WeatherStationApiController::class, 'measurements'])
    ->name('api.netatmo.measurements');

Route::get('/stations/{uuid}/modules/{moduleId}/measurements', [WeatherStationApiController::class, 'moduleMeasurements'])
    ->name('api.netatmo.module.measurements');

Route::get('/stations/{uuid}/modules/{moduleId}/measurements/debug', [WeatherStationApiController::class, 'debugModuleMeasurements'])
    ->name('api.netatmo.module.measurements.debug');
