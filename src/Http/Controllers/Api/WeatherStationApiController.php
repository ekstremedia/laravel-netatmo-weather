<?php

namespace Ekstremedia\NetatmoWeather\Http\Controllers\Api;

use Ekstremedia\NetatmoWeather\Http\Resources\ModuleResource;
use Ekstremedia\NetatmoWeather\Http\Resources\StationResource;
use Ekstremedia\NetatmoWeather\Models\NetatmoModule;
use Ekstremedia\NetatmoWeather\Models\NetatmoStation;
use Ekstremedia\NetatmoWeather\Services\NetatmoService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class WeatherStationApiController extends Controller
{
    public function __construct(protected NetatmoService $netatmoService) {}

    /**
     * Get all weather data for a station.
     */
    public function show(Request $request, string $uuid): JsonResponse|StationResource
    {
        $station = NetatmoStation::where('uuid', $uuid)->firstOrFail();

        // Check if API is enabled for this station
        if (! $station->api_enabled) {
            return response()->json([
                'error' => 'API access is not enabled for this station',
            ], 403);
        }

        // Verify API token if set
        if ($station->api_token && $request->bearerToken() !== $station->api_token) {
            return response()->json([
                'error' => 'Invalid or missing API token',
            ], 401);
        }

        // Check if token exists
        if (! $station->token) {
            return response()->json([
                'error' => 'Station not authenticated with Netatmo',
            ], 503);
        }

        // Try to refresh token if expired
        if (! $station->token->hasValidToken()) {
            try {
                $station->token->refreshToken();
            } catch (Exception $e) {
                Log::error('Failed to refresh token for API request', [
                    'station_id' => $station->id,
                    'error' => $e->getMessage(),
                ]);

                return response()->json([
                    'error' => 'Station authentication has expired',
                ], 503);
            }
        }

        // Use cache for fast responses
        $cacheKey = "api.station.{$station->uuid}.data";
        $cacheDuration = config('netatmo-weather.api_cache_duration_minutes', 5);

        $data = Cache::remember($cacheKey, now()->addMinutes($cacheDuration), function () use ($station) {
            try {
                // Fetch fresh data from Netatmo
                $this->netatmoService->getStationData($station);

                // Reload station with modules
                return $station->fresh(['modules' => function ($query) {
                    $query->where('is_active', true)->orderBy('type');
                }]);
            } catch (Exception $e) {
                Log::error('Failed to fetch station data for API', [
                    'station_id' => $station->id,
                    'error' => $e->getMessage(),
                ]);

                throw $e;
            }
        });

        if (! $data) {
            return response()->json([
                'error' => 'Failed to retrieve weather data',
            ], 500);
        }

        return new StationResource($data);
    }

    /**
     * Get data for a specific module.
     */
    public function module(Request $request, string $uuid, string $moduleId): JsonResponse|ModuleResource
    {
        $station = NetatmoStation::where('uuid', $uuid)->firstOrFail();

        // Check if API is enabled for this station
        if (! $station->api_enabled) {
            return response()->json([
                'error' => 'API access is not enabled for this station',
            ], 403);
        }

        // Verify API token if set
        if ($station->api_token && $request->bearerToken() !== $station->api_token) {
            return response()->json([
                'error' => 'Invalid or missing API token',
            ], 401);
        }

        // Find the module
        $module = NetatmoModule::where('netatmo_station_id', $station->id)
            ->where('module_id', $moduleId)
            ->where('is_active', true)
            ->firstOrFail();

        // Use cache for fast responses
        $cacheKey = "api.station.{$station->uuid}.module.{$moduleId}";
        $cacheDuration = config('netatmo-weather.api_cache_duration_minutes', 5);

        $data = Cache::remember($cacheKey, now()->addMinutes($cacheDuration), function () use ($module) {
            return $module->fresh();
        });

        return new ModuleResource($data);
    }

    /**
     * Get latest measurements from all modules.
     */
    public function measurements(Request $request, string $uuid): JsonResponse
    {
        $station = NetatmoStation::where('uuid', $uuid)->firstOrFail();

        // Check if API is enabled for this station
        if (! $station->api_enabled) {
            return response()->json([
                'error' => 'API access is not enabled for this station',
            ], 403);
        }

        // Verify API token if set
        if ($station->api_token && $request->bearerToken() !== $station->api_token) {
            return response()->json([
                'error' => 'Invalid or missing API token',
            ], 401);
        }

        // Use cache for fast responses
        $cacheKey = "api.station.{$station->uuid}.measurements";
        $cacheDuration = config('netatmo-weather.api_cache_duration_minutes', 5);

        $measurements = Cache::remember($cacheKey, now()->addMinutes($cacheDuration), function () use ($station) {
            $modules = $station->modules()
                ->where('is_active', true)
                ->whereNotNull('dashboard_data')
                ->get();

            $result = [];

            foreach ($modules as $module) {
                if ($module->dashboard_data) {
                    $result[$module->module_name] = [
                        'module_id' => $module->module_id,
                        'type' => $module->type,
                        'data' => $module->dashboard_data,
                        'timestamp' => $module->updated_at->toIso8601String(),
                    ];
                }
            }

            return $result;
        });

        return response()->json([
            'station' => [
                'id' => $station->uuid,
                'name' => $station->station_name,
            ],
            'measurements' => $measurements,
            'cached_until' => now()->addMinutes($cacheDuration)->toIso8601String(),
        ]);
    }
}
