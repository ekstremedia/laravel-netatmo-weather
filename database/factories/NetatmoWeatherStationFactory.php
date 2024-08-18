<?php

namespace Ekstremedia\NetatmoWeather\Database\Factories;

use Ekstremedia\NetatmoWeather\Models\NetatmoStation;
use Illuminate\Database\Eloquent\Factories\Factory;

class NetatmoWeatherStationFactory extends Factory
{
    protected $model = NetatmoStation::class;

    public function definition(): array
    {
        return [
            'station_name' => $this->faker->name(),
            'user_id' => 1,
            'client_id' => $this->faker->randomNumber(8),
            'client_secret' => $this->faker->md5,
            'redirect_uri' => 'http://localhost:8000',
            'webhook_uri' => 'http://localhost:8000/webhook',
        ];
    }
}
