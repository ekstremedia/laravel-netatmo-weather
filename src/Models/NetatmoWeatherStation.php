<?php

namespace Ekstremedia\NetatmoWeather\Models;

use Ekstremedia\NetatmoWeather\Database\Factories\NetatmoWeatherStationFactory;
use Ekstremedia\NetatmoWeather\Traits\Encryptable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NetatmoWeatherStation extends Model
{
    use Encryptable;
    use HasFactory;

    //    protected $table = 'netatmo_weather_stations';
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

    // Specify which attributes should be encrypted
    protected array $encryptable = [
        'client_id',
        'client_secret',
    ];

    protected static function newFactory(): NetatmoWeatherStationFactory
    {
        return NetatmoWeatherStationFactory::new();
    }

    public function token()
    {
        return $this->hasOne(NetatmoWeatherToken::class);
    }
}
