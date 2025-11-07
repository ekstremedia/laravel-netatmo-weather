<?php

// src/routes/web.php
use Ekstremedia\NetatmoWeather\Http\Controllers\NetatmoStationAuthController;
use Ekstremedia\NetatmoWeather\Http\Controllers\NetatmoStationController;
use Illuminate\Support\Facades\Route;

// Public routes (no authentication required)
Route::middleware(['web'])->group(function () {
    Route::get('/netatmo/public/{weatherStation}', [NetatmoStationController::class, 'publicShow'])
        ->name('netatmo.public');
});

// Protected routes (authentication required)
Route::middleware(['web', 'auth'])->group(function () {
    Route::resource('netatmo', NetatmoStationController::class)->parameters([
        'netatmo' => 'weatherStation',
    ]);
    Route::post('/netatmo/{weatherStation}/toggle-public', [NetatmoStationController::class, 'togglePublic'])
        ->name('netatmo.toggle-public');
    Route::get('/netatmo/{weatherStation}/select-device', [NetatmoStationController::class, 'selectDevice'])
        ->name('netatmo.select-device');
    Route::post('/netatmo/{weatherStation}/set-device', [NetatmoStationController::class, 'setDevice'])
        ->name('netatmo.set-device');
    Route::delete('/netatmo/{weatherStation}/modules/{module}', [NetatmoStationController::class, 'destroyModule'])
        ->name('netatmo.modules.destroy');
    Route::get('/netatmo/authenticate/{weatherStation}', [NetatmoStationAuthController::class, 'authenticate'])
        ->name('netatmo.authenticate');
    Route::get('/netatmo/callback/{weatherStation}', [NetatmoStationAuthController::class, 'handleCallback'])->name('netatmo.callback');

});
