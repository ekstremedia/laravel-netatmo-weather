<?php

namespace Ekstremedia\NetatmoWeather\Traits;

use Ekstremedia\NetatmoWeather\Models\NetatmoToken;

trait HasNetatmoTokens
{
    public function netatmoTokens()
    {
        return $this->hasMany(NetatmoToken::class);
    }
}
