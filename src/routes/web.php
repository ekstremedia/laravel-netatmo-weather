<?php

// src/routes/web.php
use Ekstremedia\NetatmoWeather\Http\Controllers\NetatmoWeatherController;

use Illuminate\Support\Facades\Route;

Route::prefix('netatmo')->middleware(['web', 'auth'])->group(function () {

    Route::get('/', [NetatmoWeatherController::class, 'index'])->name('netatmo.index');

});
