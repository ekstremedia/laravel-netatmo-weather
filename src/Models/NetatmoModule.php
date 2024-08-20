<?php

namespace Ekstremedia\NetatmoWeather\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NetatmoModule extends Model
{
    protected $fillable = [
        'netatmo_station_id',
        'module_id',
        'module_name',
        'module_type',
        'data_type',
    ];

    protected $casts = [
        'data_type' => 'array', // This will cast data_type to an array
    ];

    public function weatherStation(): BelongsTo
    {
        return $this->belongsTo(NetatmoStation::class);
    }

    public function readings(): HasMany
    {
        return $this->hasMany(NetatmoModuleReading::class);
    }

    public function latestReading(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(NetatmoModuleReading::class)->latest('time_utc');
    }
}
