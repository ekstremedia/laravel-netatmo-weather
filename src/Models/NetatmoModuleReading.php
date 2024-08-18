<?php

namespace Ekstremedia\NetatmoWeather\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NetatmoModuleReading extends Model
{
    protected $fillable = [
        'netatmo_module_id',
        'time_utc',
        'dashboard_data',
    ];

    protected $casts = [
        'dashboard_data' => 'array', // This will cast dashboard_data to an array
    ];

    public function weatherStationModule(): BelongsTo
    {
        return $this->belongsTo(NetatmoModule::class);
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(NetatmoModule::class, 'netatmo_module_id');
    }
}
