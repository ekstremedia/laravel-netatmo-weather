<?php

// src/routes/web.php


use Illuminate\Support\Facades\Route;

Route::prefix('netatmo')->middleware(['web', 'auth'])->group(function () {
    Route::get('/', [NetatmoWeatherController::class, 'index'])->name('memory.index');
});
