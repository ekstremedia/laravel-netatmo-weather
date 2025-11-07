<?php

namespace Ekstremedia\NetatmoWeather\Services;

use Ekstremedia\NetatmoWeather\Exceptions\InvalidApiResponseException;
use Ekstremedia\NetatmoWeather\Models\NetatmoModule;
use Ekstremedia\NetatmoWeather\Models\NetatmoStation;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class NetatmoService
{
    protected string $apiUrl;

    protected string $tokenUrl;

    protected int $cacheDurationMinutes;

    public function __construct()
    {
        $this->apiUrl = config('netatmo-weather.netatmo_api_url');
        $this->tokenUrl = config('netatmo-weather.netatmo_token_url');
        $this->cacheDurationMinutes = config('netatmo-weather.cache_duration_minutes', 10);
    }

    /**
     * Fetch weather station data from Netatmo API.
     *
     * Returns cached data if available and less than configured minutes old,
     * otherwise fetches fresh data from the API.
     *
     * @param  NetatmoStation  $weatherStation  The weather station to fetch data for
     * @return array The station data including devices and modules
     *
     * @throws RequestException When API request fails
     * @throws ConnectionException When network connection fails
     * @throws InvalidApiResponseException When API response is invalid
     */
    public function getStationData(NetatmoStation $weatherStation): array
    {
        // Check if data exists and if it's younger than configured cache duration
        $latestModule = $weatherStation->modules()->latest('updated_at')->first();

        if ($latestModule && $latestModule->updated_at->gt(now()->subMinutes($this->cacheDurationMinutes))) {
            return $this->prepareStationData($weatherStation);
        }

        // Refresh token if necessary
        if (! $weatherStation->token->hasValidToken()) {
            $weatherStation->token->refreshToken();
        }

        // Make the API request
        $response = Http::withToken($weatherStation->token->access_token)
            ->get($this->apiUrl.'/getstationsdata');

        if ($response->failed()) {
            throw new RequestException($response);
        }

        $responseBody = $response->json();

        // Store the data
        $this->storeStationData($weatherStation, $responseBody['body']);

        // Return consistent format
        return $this->prepareStationData($weatherStation->fresh(['modules']));
    }

    /**
     * Store weather station data from API response.
     *
     *
     * @throws InvalidApiResponseException
     */
    public function storeStationData(NetatmoStation $weatherStation, array $data): void
    {
        if (! isset($data['devices']) || empty($data['devices'])) {
            throw InvalidApiResponseException::noDevices();
        }

        $mainDeviceData = $data['devices'][0];

        // Validate required fields
        if (! isset($mainDeviceData['_id'], $mainDeviceData['type'], $mainDeviceData['data_type'])) {
            throw InvalidApiResponseException::missingRequiredFields('_id, type, data_type');
        }

        // Use database transaction to ensure atomicity
        DB::transaction(function () use ($weatherStation, $mainDeviceData) {
            // Store main device
            $this->storeModuleData($weatherStation, array_merge($mainDeviceData, [
                'module_name' => $mainDeviceData['module_name'] ?? 'Main Device',
            ]));

            // Store add-on modules
            if (isset($mainDeviceData['modules']) && is_array($mainDeviceData['modules'])) {
                foreach ($mainDeviceData['modules'] as $moduleData) {
                    $this->storeModuleData($weatherStation, $moduleData);
                }
            }
        });
    }

    /**
     * Store or update a single module.
     */
    protected function storeModuleData(NetatmoStation $weatherStation, array $moduleData): NetatmoModule
    {
        return $weatherStation->modules()->updateOrCreate(
            ['module_id' => $moduleData['_id']],
            [
                'module_name' => $moduleData['module_name'] ?? 'Unknown Module',
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

    /**
     * Prepare station data from database for consistent API response format.
     */
    protected function prepareStationData(NetatmoStation $weatherStation): array
    {
        return [
            'body' => [
                'devices' => [
                    [
                        '_id' => $weatherStation->id,
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
            ],
        ];
    }
}
