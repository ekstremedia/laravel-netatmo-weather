<?php

namespace Ekstremedia\NetatmoWeather\Services;

use Carbon\Carbon;
use Ekstremedia\NetatmoWeather\Models\NetatmoStation;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;

class NetatmoService
{
    protected mixed $apiUrl;

    protected mixed $tokenUrl;

    public function __construct()
    {
        $this->apiUrl = config('netatmo-weather.netatmo_api_url');
        $this->tokenUrl = config('netatmo-weather.netatmo_token_url');
    }

    /**
     * Fetch data from the weather station.
     *
     * @throws RequestException|ConnectionException
     */
    public function getStationData(NetatmoStation $weatherStation): array
    {
        // Refresh token if necessary
        if (!$weatherStation->token->hasValidToken()) {
            $weatherStation->token->refreshToken();
        }

        // Make the API request
        $response = Http::withToken($weatherStation->token->access_token)
            ->get($this->apiUrl . '/getstationsdata');

        if ($response->failed()) {
            throw new RequestException($response);
        }

        $this->storeStationData($weatherStation, $response->json()['body']);

        return $response->json();
    }

    public function storeStationData(NetatmoStation $weatherStation, array $data): void
    {
        $mainDeviceData = $data['devices'][0];

        // Store or update the main device
        $mainDevice = $weatherStation->modules()->updateOrCreate(
            ['module_id' => $mainDeviceData['_id']],
            [
                'module_name' => $mainDeviceData['module_name'],
                'module_type' => $mainDeviceData['type'],
                'data_type' => $mainDeviceData['data_type'],
            ]
        );

        $mainDevice->readings()->updateOrCreate(
            ['netatmo_module_id' => $mainDevice->id],
            [
                'time_utc' => Carbon::createFromTimestamp($mainDeviceData['dashboard_data']['time_utc']),
                'dashboard_data' => $mainDeviceData['dashboard_data'],
            ]);

        // Store or update the addon modules
        foreach ($mainDeviceData['modules'] as $moduleData) {
            if ($moduleData['_id']) {
                $module = $weatherStation->modules()->updateOrCreate(
                    ['module_id' => $moduleData['_id']],
                    [
                        'module_name' => $moduleData['module_name'],
                        'module_type' => $moduleData['type'],
                        'data_type' => $moduleData['data_type'],
                    ]
                );

                $module->readings()->updateOrCreate(
                    ['netatmo_module_id' => $module->id],
                    [
                        'time_utc' => Carbon::createFromTimestamp($moduleData['dashboard_data']['time_utc']),
                        'dashboard_data' => $moduleData['dashboard_data'],
                    ]);
            } else {
                // Handle the error case, log it, etc.
                logger()->error('Failed to retrieve module ID.', ['module' => $moduleData]);
            }
        }
    }

}
