<?php

namespace Ekstremedia\NetatmoWeather\Traits;

use Ekstremedia\NetatmoWeather\Models\NetatmoWeatherToken;

trait HasNetatmoTokens
{
    public function netatmoTokens()
    {
        return $this->hasMany(NetatmoWeatherToken::class);
    }
}
