<?php

namespace Ekstremedia\NetatmoWeather\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NetatmoWeatherStationRequest extends FormRequest
{
    public function authorize(): true
    {
        // Only authenticated users can create a weather station
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'station_name' => 'required|string|max:255',
            'client_id' => 'required|string|max:255',
            'client_secret' => 'required|string|max:255',
            'redirect_uri' => 'nullable|url',
            'webhook_uri' => 'nullable|url',
            'is_public' => 'nullable|boolean',
            'api_enabled' => 'nullable|boolean',
            'api_token' => 'nullable|string|min:32|max:255',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Convert checkbox values to boolean
        $data = [
            'is_public' => $this->boolean('is_public'),
            'api_enabled' => $this->boolean('api_enabled'),
        ];

        // If remove_api_token is set, clear the api_token
        if ($this->input('remove_api_token') === '1') {
            $data['api_token'] = null;
        }

        $this->merge($data);
    }
}
