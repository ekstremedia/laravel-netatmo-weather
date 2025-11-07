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

        // Find the correct device from the API response
        $mainDeviceData = $this->findCorrectDevice($weatherStation, $data['devices']);

        if (! $mainDeviceData) {
            throw InvalidApiResponseException::missingRequiredFields('Could not find matching device in API response');
        }

        // Validate required fields
        if (! isset($mainDeviceData['_id'], $mainDeviceData['type'], $mainDeviceData['data_type'])) {
            throw InvalidApiResponseException::missingRequiredFields('_id, type, data_type');
        }

        // Use database transaction to ensure atomicity
        DB::transaction(function () use ($weatherStation, $mainDeviceData) {
            // Store device_id if not already set
            if (! $weatherStation->device_id) {
                $weatherStation->update(['device_id' => $mainDeviceData['_id']]);
            }

            // Collect all module IDs from API response
            $activeModuleIds = [$mainDeviceData['_id']];
            if (isset($mainDeviceData['modules']) && is_array($mainDeviceData['modules'])) {
                foreach ($mainDeviceData['modules'] as $moduleData) {
                    $activeModuleIds[] = $moduleData['_id'];
                }
            }

            // Mark modules not in API response as inactive
            $weatherStation->modules()
                ->whereNotIn('module_id', $activeModuleIds)
                ->update(['is_active' => false]);

            // Store main device (will be marked as active)
            $this->storeModuleData($weatherStation, array_merge($mainDeviceData, [
                'module_name' => $mainDeviceData['module_name'] ?? 'Main Device',
            ]));

            // Store add-on modules (will be marked as active)
            if (isset($mainDeviceData['modules']) && is_array($mainDeviceData['modules'])) {
                foreach ($mainDeviceData['modules'] as $moduleData) {
                    $this->storeModuleData($weatherStation, $moduleData);
                }
            }
        });
    }

    /**
     * Get all available devices from Netatmo API.
     */
    public function getAvailableDevices(NetatmoStation $weatherStation): array
    {
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

        if (! isset($responseBody['body']['devices']) || empty($responseBody['body']['devices'])) {
            return [];
        }

        return collect($responseBody['body']['devices'])->map(function ($device) {
            return [
                'device_id' => $device['_id'],
                'station_name' => $device['station_name'] ?? 'Unknown Station',
                'module_count' => isset($device['modules']) ? count($device['modules']) + 1 : 1, // +1 for main device
            ];
        })->toArray();
    }

    /**
     * Find the correct device from the API response.
     */
    protected function findCorrectDevice(NetatmoStation $weatherStation, array $devices): ?array
    {
        // If device_id is set, find the matching device
        if ($weatherStation->device_id) {
            foreach ($devices as $device) {
                if (isset($device['_id']) && $device['_id'] === $weatherStation->device_id) {
                    return $device;
                }
            }

            // Device ID is set but not found - log warning and don't fallback
            logger()->warning('Device ID not found in API response', [
                'station_id' => $weatherStation->id,
                'device_id' => $weatherStation->device_id,
                'available_devices' => array_column($devices, '_id'),
            ]);

            return null;
        }

        // If only one device, use it
        if (count($devices) === 1) {
            return $devices[0];
        }

        // Multiple devices but no device_id - don't auto-select
        // User needs to manually select the correct device
        logger()->warning('Multiple devices found but no device_id set', [
            'station_id' => $weatherStation->id,
            'station_name' => $weatherStation->station_name,
            'available_devices' => array_map(fn ($d) => [
                'id' => $d['_id'] ?? null,
                'name' => $d['station_name'] ?? null,
            ], $devices),
        ]);

        return null;
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
                'is_active' => true,
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
