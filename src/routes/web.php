<?php

// src/routes/web.php
use Ekstremedia\NetatmoWeather\Http\Controllers\NetatmoWeatherStationController;
use Illuminate\Support\Facades\Route;

//
//Route::prefix('netatmo')->middleware(['web', 'auth'])->group(function () {
//
//});
//Route::resource('netatmo', NetatmoWeatherStationController::class)->middleware(['web', 'auth']);
Route::resource('netatmo', NetatmoWeatherStationController::class)->parameters([
    'netatmo' => 'weatherStation',
])->middleware(['web', 'auth']);
