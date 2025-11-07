<?php

namespace Ekstremedia\NetatmoWeather\Models;

use Ekstremedia\NetatmoWeather\Database\Factories\NetatmoWeatherStationFactory;
use Ekstremedia\NetatmoWeather\Traits\Encryptable;
use Ekstremedia\NetatmoWeather\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class NetatmoStation extends Model
{
    use Encryptable, HasFactory, HasUuid;

    protected $table = 'netatmo_stations';

    protected $fillable = [
        'user_id',
        'station_name',
        'device_id',
        'is_public',
        'client_id',
        'client_secret',
        'redirect_uri',
        'webhook_uri',
    ];

    /**
     * Attributes that should be cast.
     */
    protected $casts = [
        'is_public' => 'boolean',
    ];

    /**
     * Attributes that should be encrypted.
     */
    protected array $encryptable = [
        'client_id',
        'client_secret',
    ];

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    protected static function newFactory(): NetatmoWeatherStationFactory
    {
        return NetatmoWeatherStationFactory::new();
    }

    /**
     * Get the user that owns the weather station.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(config('netatmo-weather.user_model', 'App\Models\User'));
    }

    /**
     * Get the OAuth token for this weather station.
     */
    public function token(): HasOne
    {
        return $this->hasOne(NetatmoToken::class);
    }

    /**
     * Get all modules for this weather station.
     */
    public function modules(): HasMany
    {
        return $this->hasMany(NetatmoModule::class);
    }
}
