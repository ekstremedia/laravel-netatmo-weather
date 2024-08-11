<?php

namespace Ekstremedia\NetatmoWeather\Database\Seeders;

use Ekstremedia\NetatmoWeather\Models\NetatmoWeatherStation;
use Illuminate\Database\Seeder;

class WeatherStationSeeder extends Seeder
{
    public function run(): void
    {
        NetatmoWeatherStation::factory()->create([
            'station_name' => 'Terje sin værstasjon',
            'user_id' => 1,
            'client_id' => '123547',
            'client_secret' => '5fdsfsdfsdf4',
            'redirect_uri' => 'http://localhost:8000',
            'webhook_uri' => 'http://localhost:8000/webhook',
        ]);
    }
}
