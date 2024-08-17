<?php

// src/routes/web.php
use Ekstremedia\NetatmoWeather\Http\Controllers\NetatmoWeatherStationAuthController;
use Ekstremedia\NetatmoWeather\Http\Controllers\NetatmoWeatherStationController;
use Illuminate\Support\Facades\Route;


Route::middleware(['web', 'auth'])->group(function () {
    Route::resource('netatmo', NetatmoWeatherStationController::class)->parameters([
        'netatmo' => 'weatherStation',
    ]);
    Route::get('/netatmo/authenticate/{weatherstation}', [NetatmoWeatherStationAuthController::class, 'authenticate'])
        ->name('netatmo.authenticate');
    Route::get('/netatmo/callback/{weatherstation}', [NetatmoWeatherStationAuthController::class, 'handleCallback'])->name('netatmo.callback');

});
