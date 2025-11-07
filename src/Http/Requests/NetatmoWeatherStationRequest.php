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
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Convert checkbox value to boolean
        $this->merge([
            'is_public' => $this->has('is_public') ? true : false,
        ]);
    }
}
