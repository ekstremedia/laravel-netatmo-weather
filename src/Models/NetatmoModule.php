<?php

namespace Ekstremedia\NetatmoWeather\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NetatmoModule extends Model
{

    protected $guarded = [];

    protected $casts = [
        'data_type' => 'array',
        'user' => 'array',
        'place' => 'array',
        'dashboard_data' => 'array',
    ];

    public function weatherStation(): BelongsTo
    {
        return $this->belongsTo(NetatmoStation::class);
    }

    public function readings(): HasMany
    {
        return $this->hasMany(NetatmoModuleReading::class);
    }
//
//    public function latestReading(): \Illuminate\Database\Eloquent\Relations\HasOne
//    {
//        return $this->hasOne(NetatmoModuleReading::class)->latest('time_utc');
//    }
}
