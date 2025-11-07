<?php

namespace Ekstremedia\NetatmoWeather\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NetatmoModuleReading extends Model
{
    protected $table = 'netatmo_module_readings';

    protected $fillable = [
        'netatmo_module_id',
        'time_utc',
        'dashboard_data',
    ];

    protected $casts = [
        'dashboard_data' => 'array',
    ];

    /**
     * Get the module that owns this reading.
     */
    public function module(): BelongsTo
    {
        return $this->belongsTo(NetatmoModule::class, 'netatmo_module_id');
    }
}
