<?php

namespace Ekstremedia\NetatmoWeather\Models;

use Ekstremedia\NetatmoWeather\Database\Factories\NetatmoWeatherStationFactory;
use Ekstremedia\NetatmoWeather\Traits\Encryptable;
use Ekstremedia\NetatmoWeather\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class NetatmoStation extends Model
{
    use Encryptable;
    use HasFactory;
    use HasUuid;

    //        protected $table = 'netatmo_stations';
    //    protected $primaryKey = 'id';
    //    public $incrementing = true;
    //    protected $keyType = 'int';
    protected $with = ['token'];

    protected $fillable = [
        'user_id',
        'station_name',
        'client_id',
        'client_secret',
        'redirect_uri',
        'webhook_uri',
    ];

    // Specify that UUID should be used as the route key name
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    // Specify which attributes should be encrypted
    protected array $encryptable = [
        'client_id',
        'client_secret',
    ];

    protected static function newFactory(): NetatmoWeatherStationFactory
    {
        return NetatmoWeatherStationFactory::new();
    }

    public function token(): HasOne
    {
        return $this->hasOne(NetatmoToken::class);
    }

    public function modules(): HasMany
    {
        return $this->hasMany(NetatmoModule::class);
    }
}
