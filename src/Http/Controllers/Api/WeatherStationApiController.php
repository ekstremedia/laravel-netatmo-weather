<?php

namespace Ekstremedia\NetatmoWeather\Http\Controllers\Api;

use Ekstremedia\NetatmoWeather\Http\Resources\ModuleResource;
use Ekstremedia\NetatmoWeather\Http\Resources\StationResource;
use Ekstremedia\NetatmoWeather\Models\NetatmoModule;
use Ekstremedia\NetatmoWeather\Models\NetatmoModuleReading;
use Ekstremedia\NetatmoWeather\Models\NetatmoStation;
use Ekstremedia\NetatmoWeather\Services\MeasurementService;
use Ekstremedia\NetatmoWeather\Services\NetatmoService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class WeatherStationApiController extends Controller
{
    public function __construct(
        protected NetatmoService $netatmoService,
        protected MeasurementService $measurementService
    ) {}

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

    /**
     * Debug endpoint - shows module info and Netatmo API call details
     */
    public function debugModuleMeasurements(Request $request, string $uuid, string $moduleId): JsonResponse
    {
        $station = NetatmoStation::where('uuid', $uuid)->firstOrFail();

        // Check authentication: allow if user owns the station
        $isOwner = auth()->check() && auth()->id() === $station->user_id;

        if (! $isOwner) {
            return response()->json([
                'error' => 'Debug endpoint only available to station owner',
            ], 403);
        }

        // Find the module
        $module = NetatmoModule::where('netatmo_station_id', $station->id)
            ->where('module_id', $moduleId)
            ->where('is_active', true)
            ->with(['netatmoStation.token'])
            ->firstOrFail();

        $period = $request->get('period', '1day');
        $scale = $request->get('scale', '30min');

        // Get date range
        $dateEnd = now();
        $dateBegin = match ($period) {
            '1hour' => now()->subHour(),
            '6hours' => now()->subHours(6),
            '12hours' => now()->subHours(12),
            '1day' => now()->subDay(),
            '3days' => now()->subDays(3),
            '1week' => now()->subWeek(),
            '1month' => now()->subMonth(),
            default => now()->subDay(),
        };

        // Build params that would be sent to Netatmo
        $params = [
            'device_id' => $station->device_id,
            'scale' => $scale,
            'type' => implode(',', $module->data_type ?? ['Temperature', 'Humidity']),
            'date_begin' => $dateBegin->timestamp,
            'date_end' => $dateEnd->timestamp,
            'optimize' => 'false',  // Netatmo API expects string, not boolean
            'real_time' => 'true',   // Netatmo API expects string, not boolean
        ];

        if ($module->type !== 'NAMain') {
            $params['module_id'] = $module->module_id;
        }

        return response()->json([
            'debug_info' => [
                'station' => [
                    'id' => $station->id,
                    'uuid' => $station->uuid,
                    'name' => $station->station_name,
                    'device_id' => $station->device_id,
                ],
                'module' => [
                    'db_id' => $module->id,
                    'module_id' => $module->module_id,
                    'name' => $module->module_name,
                    'type' => $module->type,
                    'data_types' => $module->data_type,
                ],
                'token' => [
                    'exists' => $station->token !== null,
                    'valid' => $station->token?->hasValidToken(),
                    'expires_at' => $station->token?->expires_at?->toIso8601String(),
                ],
                'api_request' => [
                    'url' => config('netatmo-weather.netatmo_api_url').'/getmeasure',
                    'method' => 'GET',
                    'params' => $params,
                    'date_range_human' => [
                        'begin' => $dateBegin->toDateTimeString(),
                        'end' => $dateEnd->toDateTimeString(),
                    ],
                ],
                'database_readings' => [
                    'count' => NetatmoModuleReading::where('netatmo_module_id', $module->id)
                        ->whereBetween('time_utc', [$dateBegin, $dateEnd])
                        ->count(),
                    'latest' => NetatmoModuleReading::where('netatmo_module_id', $module->id)
                        ->latest('time_utc')
                        ->first()
                        ?->time_utc
                        ?->toDateTimeString(),
                ],
            ],
        ]);
    }

    /**
     * Get historical measurements for a specific module
     */
    public function moduleMeasurements(Request $request, string $uuid, string $moduleId): JsonResponse
    {
        $station = NetatmoStation::where('uuid', $uuid)->firstOrFail();

        // Check authentication: allow if user owns the station OR API is enabled
        $isOwner = auth()->check() && auth()->id() === $station->user_id;

        if (! $isOwner) {
            // Not the owner, so check API access
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
        }

        // Find the module with station relationship
        $module = NetatmoModule::where('netatmo_station_id', $station->id)
            ->where('module_id', $moduleId)
            ->where('is_active', true)
            ->with(['netatmoStation.token'])
            ->firstOrFail();

        // Get query parameters
        $period = $request->get('period', '1day'); // 1hour, 6hours, 12hours, 1day, 3days, 1week, 1month
        $scale = $request->get('scale', '30min'); // max, 30min, 1hour, 3hours, 1day, 1week, 1month

        // Validate period
        $validPeriods = ['1hour', '6hours', '12hours', '1day', '3days', '1week', '1month'];
        if (! in_array($period, $validPeriods)) {
            return response()->json([
                'error' => 'Invalid period. Valid options: '.implode(', ', $validPeriods),
            ], 400);
        }

        // Validate scale
        $validScales = ['max', '30min', '1hour', '3hours', '1day', '1week', '1month'];
        if (! in_array($scale, $validScales)) {
            return response()->json([
                'error' => 'Invalid scale. Valid options: '.implode(', ', $validScales),
            ], 400);
        }

        try {
            $data = $this->measurementService->getMeasurements($module, $period, $scale);

            return response()->json([
                'module' => [
                    'id' => $module->module_id,
                    'name' => $module->module_name,
                    'type' => $module->type,
                ],
                'period' => $period,
                'scale' => $scale,
                'measurements' => $data,
            ]);
        } catch (Exception $e) {
            Log::error('Failed to fetch module measurements', [
                'module_id' => $module->id,
                'module_name' => $module->module_name,
                'station_id' => $station->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'error' => 'Failed to fetch measurements',
                'message' => $e->getMessage(),
                'debug' => [
                    'module_id' => $module->module_id,
                    'module_type' => $module->type,
                    'data_types' => $module->data_type,
                    'station_device_id' => $station->device_id,
                ],
            ], 500);
        }
    }
}
