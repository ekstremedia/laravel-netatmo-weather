<?php
namespace Ekstremedia\NetatmoWeather\Models;

use Ekstremedia\NetatmoWeather\Traits\Encryptable;
use Illuminate\Database\Eloquent\Model;

class NetatmoWeatherStation extends Model
{
    use Encryptable;

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
}
