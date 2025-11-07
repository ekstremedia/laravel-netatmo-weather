<?php

namespace Ekstremedia\NetatmoWeather\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NetatmoModule extends Model
{
    protected $table = 'netatmo_modules';

    protected $fillable = [
        'netatmo_station_id',
        'module_id',
        'module_name',
        'type',
        'battery_percent',
        'battery_vp',
        'firmware',
        'last_message',
        'last_seen',
        'wifi_status',
        'rf_status',
        'reachable',
        'last_status_store',
        'date_setup',
        'last_setup',
        'co2_calibrating',
        'home_id',
        'home_name',
        'user',
        'place',
        'data_type',
        'dashboard_data',
    ];

    protected $casts = [
        'data_type' => 'array',
        'user' => 'array',
        'place' => 'array',
        'dashboard_data' => 'array',
    ];

    public function netatmoStation(): BelongsTo
    {
        return $this->belongsTo(NetatmoStation::class, 'netatmo_station_id');
    }

    public function readings(): HasMany
    {
        return $this->hasMany(NetatmoModuleReading::class);
    }
}
