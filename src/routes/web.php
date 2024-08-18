<?php

// src/routes/web.php
use Ekstremedia\NetatmoWeather\Http\Controllers\NetatmoStationAuthController;
use Ekstremedia\NetatmoWeather\Http\Controllers\NetatmoStationController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth'])->group(function () {
    Route::resource('netatmo', NetatmoStationController::class)->parameters([
        'netatmo' => 'weatherStation',
    ]);
    Route::get('/netatmo/authenticate/{weatherstation}', [NetatmoStationAuthController::class, 'authenticate'])
        ->name('netatmo.authenticate');
    Route::get('/netatmo/callback/{weatherstation}', [NetatmoStationAuthController::class, 'handleCallback'])->name('netatmo.callback');

});
