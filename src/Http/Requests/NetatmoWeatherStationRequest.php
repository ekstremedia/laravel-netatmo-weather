<?php

namespace Ekstremedia\NetatmoWeather\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NetatmoWeatherStationRequest extends FormRequest
{
    public function authorize(): true
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'station_name' => 'required|string|max:255',
            'client_id' => 'required|string|max:255',
            'client_secret' => 'required|string|max:255',
            'redirect_uri' => 'nullable|url',
            'webhook_uri' => 'nullable|url',
        ];
    }
}
