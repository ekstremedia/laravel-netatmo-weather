<?php

namespace Ekstremedia\NetatmoWeather\Services;

use Carbon\Carbon;
use Ekstremedia\NetatmoWeather\Models\NetatmoStation;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

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
        // Check if data exists and if it's younger than 10 minutes
        $latestModule = $weatherStation->modules()->latest('updated_at')->first();

        if ($latestModule && $latestModule->updated_at->gt(now()->subMinutes(10))) {
            // Data is younger than 10 minutes, return it
            return $this->prepareStationData($weatherStation);
        }
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

        $responseBody = $response->json();

        $this->storeStationData($weatherStation, $responseBody['body']);

        return $response->json();
    }

    public function storeStationData(NetatmoStation $weatherStation, array $data): void
    {
        // Store or update the main device (the base station)
        $mainDeviceData = $data['devices'][0];
        $mainDevice = $weatherStation->modules()->updateOrCreate(
            ['module_id' => $mainDeviceData['_id']],
            [
                'module_name' => $mainDeviceData['module_name'] ?? 'Main Device',
                'type' => $mainDeviceData['type'],
                'battery_percent' => $mainDeviceData['battery_percent'] ?? null,
                'battery_vp' => $mainDeviceData['battery_vp'] ?? null,
                'firmware' => $mainDeviceData['firmware'] ?? null,
                'last_message' => $mainDeviceData['last_message'] ?? null,
                'last_seen' => $mainDeviceData['last_seen'] ?? null,
                'wifi_status' => $mainDeviceData['wifi_status'] ?? null,
                'rf_status' => $mainDeviceData['rf_status'] ?? null,
                'reachable' => $mainDeviceData['reachable'] ?? null,
                'last_status_store' => $mainDeviceData['last_status_store'] ?? null,
                'date_setup' => $mainDeviceData['date_setup'] ?? null,
                'last_setup' => $mainDeviceData['last_setup'] ?? null,
                'co2_calibrating' => $mainDeviceData['co2_calibrating'] ?? null,
                'home_id' => $mainDeviceData['home_id'] ?? null,
                'home_name' => $mainDeviceData['home_name'] ?? null,
                'user' => $mainDeviceData['user'] ?? null,
                'place' => $mainDeviceData['place'] ?? null,
                'data_type' => $mainDeviceData['data_type'],
                'dashboard_data' => $mainDeviceData['dashboard_data'] ?? null,
            ]
        );

        // Store or update the add-on modules
        foreach ($mainDeviceData['modules'] as $moduleData) {
            $module = $weatherStation->modules()->updateOrCreate(
                ['module_id' => $moduleData['_id']],
                [
                    'module_name' => $moduleData['module_name'],
                    'type' => $moduleData['type'],
                    'battery_percent' => $moduleData['battery_percent'] ?? null,
                    'battery_vp' => $moduleData['battery_vp'] ?? null,
                    'firmware' => $moduleData['firmware'] ?? null,
                    'last_message' => $moduleData['last_message'] ?? null,
                    'last_seen' => $moduleData['last_seen'] ?? null,
                    'wifi_status' => $moduleData['wifi_status'] ?? null,
                    'rf_status' => $moduleData['rf_status'] ?? null,
                    'reachable' => $moduleData['reachable'] ?? null,
                    'last_status_store' => $moduleData['last_status_store'] ?? null,
                    'date_setup' => $moduleData['date_setup'] ?? null,
                    'last_setup' => $moduleData['last_setup'] ?? null,
                    'co2_calibrating' => $moduleData['co2_calibrating'] ?? null,
                    'home_id' => $moduleData['home_id'] ?? null,
                    'home_name' => $moduleData['home_name'] ?? null,
                    'user' => $moduleData['user'] ?? null,
                    'place' => $moduleData['place'] ?? null,
                    'data_type' => $moduleData['data_type'],
                    'dashboard_data' => $moduleData['dashboard_data'] ?? null,
                ]
            );
        }
    }

    protected function prepareStationData(NetatmoStation $weatherStation): array
    {
        return [
            'devices' => [
                [
                    '_id' => $weatherStation->id, // Assuming station ID
                    'station_name' => $weatherStation->station_name,
                    'modules' => $weatherStation->modules->map(function ($module) {
                        return [
                            '_id' => $module->module_id,
                            'module_name' => $module->module_name,
                            'type' => $module->type,
                            'battery_percent' => $module->battery_percent,
                            'battery_vp' => $module->battery_vp,
                            'firmware' => $module->firmware,
                            'last_message' => $module->last_message,
                            'last_seen' => $module->last_seen,
                            'wifi_status' => $module->wifi_status,
                            'rf_status' => $module->rf_status,
                            'reachable' => $module->reachable,
                            'last_status_store' => $module->last_status_store,
                            'date_setup' => $module->date_setup,
                            'last_setup' => $module->last_setup,
                            'co2_calibrating' => $module->co2_calibrating,
                            'home_id' => $module->home_id,
                            'home_name' => $module->home_name,
                            'user' => $module->user,
                            'place' => $module->place,
                            'data_type' => $module->data_type,
                            'dashboard_data' => $module->dashboard_data,
                        ];
                    })->toArray(),
                ],
            ],
        ];
    }

}
