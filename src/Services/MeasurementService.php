<?php

namespace Ekstremedia\NetatmoWeather\Services;

use Carbon\Carbon;
use Ekstremedia\NetatmoWeather\Models\NetatmoModule;
use Ekstremedia\NetatmoWeather\Models\NetatmoModuleReading;
use Ekstremedia\NetatmoWeather\Models\NetatmoStation;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MeasurementService
{
    /**
     * Fetch measurements from Netatmo API and store in database
     *
     * @param  string  $scale  Options: max, 30min, 1hour, 3hours, 1day, 1week, 1month
     * @param  array|null  $types  Measurement types (Temperature, Humidity, CO2, etc.)
     */
    public function fetchAndStoreMeasurements(
        NetatmoModule $module,
        string $scale = '30min',
        ?Carbon $dateBegin = null,
        ?Carbon $dateEnd = null,
        ?array $types = null
    ): array {
        // Load station relationship if not already loaded
        if (! $module->relationLoaded('netatmoStation')) {
            $module->load('netatmoStation.token');
        }

        $station = $module->netatmoStation;

        // Default to last 24 hours if no dates provided
        $dateBegin = $dateBegin ?? now()->subDay();
        $dateEnd = $dateEnd ?? now();

        // Determine measurement types from module's data_type if not provided
        if (! $types) {
            $types = $module->data_type ?? ['Temperature', 'Humidity'];
        }

        // Map data types to valid Netatmo API types
        $types = $this->mapToNetatmoTypes($types);

        // Fetch from Netatmo API
        $measurements = $this->fetchFromNetatmo(
            $station,
            $module,
            $scale,
            $dateBegin,
            $dateEnd,
            $types
        );

        // Store in database
        $this->storeMeasurements($module, $measurements, $types);

        return $measurements;
    }

    /**
     * Fetch measurements from Netatmo API
     */
    protected function fetchFromNetatmo(
        NetatmoStation $station,
        NetatmoModule $module,
        string $scale,
        Carbon $dateBegin,
        Carbon $dateEnd,
        array $types
    ): array {
        // Build request parameters
        $params = [
            'device_id' => $station->device_id,
            'scale' => $scale,
            'type' => implode(',', $types),
            'date_begin' => $dateBegin->timestamp,
            'date_end' => $dateEnd->timestamp,
            'optimize' => 'false',  // Netatmo API expects string, not boolean
            'real_time' => 'true',   // Netatmo API expects string, not boolean
        ];

        // For non-main modules, add module_id
        // Main station (NAMain) uses device_id as the module
        if ($module->type !== 'NAMain') {
            $params['module_id'] = $module->module_id;
        }

        Log::info('Fetching measurements from Netatmo API', [
            'station_id' => $station->id,
            'station_device_id' => $station->device_id,
            'module_id' => $module->module_id,
            'module_type' => $module->type,
            'params' => $params,
            'date_range' => [
                'begin' => $dateBegin->toDateTimeString(),
                'end' => $dateEnd->toDateTimeString(),
            ],
        ]);

        $response = Http::withToken($station->token->access_token)
            ->get(config('netatmo-weather.netatmo_api_url').'/getmeasure', $params);

        $responseData = $response->json();

        Log::info('Netatmo API response received', [
            'status' => $response->status(),
            'response_keys' => array_keys($responseData ?? []),
            'full_response' => $responseData,
        ]);

        if (! $response->successful()) {
            Log::error('Failed to fetch measurements from Netatmo', [
                'status' => $response->status(),
                'body' => $response->body(),
                'params' => $params,
                'station_id' => $station->id,
                'module_id' => $module->module_id,
            ]);

            throw new \RuntimeException(
                'Netatmo API error: '.$response->json('error.message', 'Unknown error')
            );
        }

        $data = $response->json('body', []);

        if (empty($data)) {
            Log::warning('Netatmo API returned no measurement data', [
                'station_id' => $station->id,
                'module_id' => $module->module_id,
                'params' => $params,
                'full_response' => $responseData,
            ]);
        } else {
            Log::info('Netatmo API returned measurement data', [
                'station_id' => $station->id,
                'module_id' => $module->module_id,
                'data_point_count' => count($data),
                'first_timestamp' => ! empty($data) ? Carbon::createFromTimestamp(array_key_first($data))->toDateTimeString() : null,
                'last_timestamp' => ! empty($data) ? Carbon::createFromTimestamp(array_key_last($data))->toDateTimeString() : null,
            ]);
        }

        return $data;
    }

    /**
     * Store measurements in database
     */
    protected function storeMeasurements(NetatmoModule $module, array $measurements, array $types): void
    {
        foreach ($measurements as $timestamp => $values) {
            $dashboardData = [];

            // Map values to measurement types
            foreach ($types as $index => $type) {
                if (isset($values[$index])) {
                    $dashboardData[$type] = $values[$index];
                }
            }

            // Upsert the reading (insert or update if exists)
            NetatmoModuleReading::updateOrCreate(
                [
                    'netatmo_module_id' => $module->id,
                    'time_utc' => Carbon::createFromTimestamp($timestamp),
                ],
                [
                    'dashboard_data' => $dashboardData,
                ]
            );
        }
    }

    /**
     * Get measurements for a module with caching
     *
     * @param  string  $period  Options: 1hour, 6hours, 12hours, 1day, 3days, 1week, 1month
     * @param  string  $scale  Options: max, 30min, 1hour, 3hours, 1day, 1week, 1month
     */
    public function getMeasurements(
        NetatmoModule $module,
        string $period = '1day',
        string $scale = '30min'
    ): array {
        $cacheKey = "measurements.{$module->id}.{$period}.{$scale}";
        $cacheDuration = $this->getCacheDuration($period);

        return Cache::remember($cacheKey, $cacheDuration, function () use ($module, $period, $scale) {
            // Load station relationship if not already loaded
            if (! $module->relationLoaded('netatmoStation')) {
                $module->load('netatmoStation.token');
            }

            // Calculate date range based on period
            [$dateBegin, $dateEnd] = $this->getDateRange($period);

            // Try to get from database first
            $readings = NetatmoModuleReading::where('netatmo_module_id', $module->id)
                ->whereBetween('time_utc', [$dateBegin, $dateEnd])
                ->orderBy('time_utc', 'asc')
                ->get();

            // If no data or data is old, fetch from Netatmo
            if ($readings->isEmpty() || $this->shouldRefresh($readings, $dateEnd)) {
                $this->fetchAndStoreMeasurements($module, $scale, $dateBegin, $dateEnd);

                // Reload from database
                $readings = NetatmoModuleReading::where('netatmo_module_id', $module->id)
                    ->whereBetween('time_utc', [$dateBegin, $dateEnd])
                    ->orderBy('time_utc', 'asc')
                    ->get();
            }

            return $this->formatReadings($readings);
        });
    }

    /**
     * Get date range for a period
     */
    protected function getDateRange(string $period): array
    {
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

        return [$dateBegin, $dateEnd];
    }

    /**
     * Determine cache duration based on period
     */
    protected function getCacheDuration(string $period): int
    {
        return match ($period) {
            '1hour', '6hours' => 300, // 5 minutes
            '12hours', '1day' => 900, // 15 minutes
            '3days' => 1800, // 30 minutes
            '1week', '1month' => 3600, // 1 hour
            default => 900,
        };
    }

    /**
     * Check if we should refresh data from Netatmo
     */
    protected function shouldRefresh($readings, Carbon $dateEnd): bool
    {
        if ($readings->isEmpty()) {
            return true;
        }

        $latestReading = $readings->last();
        $latestTime = Carbon::parse($latestReading->time_utc);

        // If latest reading is more than 30 minutes old, refresh
        return $latestTime->diffInMinutes($dateEnd) > 30;
    }

    /**
     * Format readings for API response
     */
    protected function formatReadings($readings): array
    {
        $formatted = [
            'timestamps' => [],
            'data' => [],
        ];

        foreach ($readings as $reading) {
            $formatted['timestamps'][] = Carbon::parse($reading->time_utc)->toIso8601String();

            foreach ($reading->dashboard_data as $type => $value) {
                if (! isset($formatted['data'][$type])) {
                    $formatted['data'][$type] = [];
                }
                $formatted['data'][$type][] = $value;
            }
        }

        return $formatted;
    }

    /**
     * Map data types to valid Netatmo API measurement types
     */
    protected function mapToNetatmoTypes(array $types): array
    {
        $mapping = [
            'Wind' => 'WindStrength',  // Wind module stores as "Wind" but API expects "WindStrength"
        ];

        return array_map(function ($type) use ($mapping) {
            return $mapping[$type] ?? $type;
        }, $types);
    }
}
