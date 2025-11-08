<?php

namespace Ekstremedia\NetatmoWeather\Http\Controllers;

use Ekstremedia\NetatmoWeather\Http\Requests\NetatmoWeatherStationRequest;
use Ekstremedia\NetatmoWeather\Models\NetatmoModule;
use Ekstremedia\NetatmoWeather\Models\NetatmoStation;
use Ekstremedia\NetatmoWeather\Services\NetatmoService;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class NetatmoStationController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        // Fetch weather stations with eager loading to prevent N+1 queries
        $weatherStations = NetatmoStation::where('user_id', auth()->id())
            ->with(['token', 'modules'])
            ->get();

        // Check each weather station's token validity and refresh if necessary
        foreach ($weatherStations as $weatherStation) {
            if ($weatherStation->token && ! $weatherStation->token->hasValidToken()) {
                try {
                    $weatherStation->token->refreshToken();
                } catch (Exception $e) {
                    logger()->error('Failed to refresh token for weather station', [
                        'weather_station_id' => $weatherStation->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }

        return view('netatmoweather::netatmo.index', compact('weatherStations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $fields = $this->getFormFields();

        return view('netatmoweather::netatmo.form', compact('fields'));

    }

    private function getFormFields(): array
    {
        return [
            ['name' => 'station_name', 'type' => 'text', 'required' => true],
            ['name' => 'client_id', 'type' => 'text', 'required' => true],
            ['name' => 'client_secret', 'type' => 'password', 'required' => true],
        ];
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(NetatmoWeatherStationRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['user_id'] = auth()->id();

        $weatherStation = NetatmoStation::create($data);

        // Redirect directly to authentication
        return redirect()->route('netatmo.authenticate', $weatherStation)
            ->with('success', 'Weather station created. Please authenticate with Netatmo.');

    }

    /**
     * Display the specified resource.
     */
    public function show(NetatmoStation $weatherStation, NetatmoService $netatmoService): View|RedirectResponse
    {
        $this->authorize('view', $weatherStation);

        // Check if token exists
        if (! $weatherStation->token) {
            return redirect()->route('netatmo.authenticate', $weatherStation)
                ->with('error', 'Please authenticate with Netatmo first.');
        }

        // Try to refresh token if expired
        if (! $weatherStation->token->hasValidToken()) {
            try {
                $weatherStation->token->refreshToken();
            } catch (Exception $e) {
                logger()->error('Failed to refresh token', [
                    'station_id' => $weatherStation->id,
                    'error' => $e->getMessage(),
                ]);

                return redirect()->route('netatmo.authenticate', $weatherStation)
                    ->with('error', 'Session expired. Please re-authenticate with Netatmo.');
            }
        }

        // Check if device selection is needed
        if (! $weatherStation->device_id) {
            try {
                $devices = $netatmoService->getAvailableDevices($weatherStation);
                if (count($devices) > 1) {
                    return redirect()->route('netatmo.select-device', $weatherStation)
                        ->with('info', 'Please select which weather station this configuration should use.');
                }
            } catch (Exception $e) {
                // Continue to try fetching data anyway
            }
        }

        try {
            // Fetch data from the weather station
            $netatmoService->getStationData($weatherStation);

            return view('netatmoweather::netatmo.show', compact('weatherStation'));
        } catch (Exception $e) {
            logger()->error('Failed to retrieve data from Netatmo', [
                'error' => $e->getMessage(),
                'station_id' => $weatherStation->id,
            ]);

            return redirect()->route('netatmo.index')
                ->with('error', 'Failed to retrieve data from Netatmo: '.$e->getMessage());
        }
    }

    /**
     * Show device selection page.
     */
    public function selectDevice(NetatmoStation $weatherStation, NetatmoService $netatmoService): View|RedirectResponse
    {
        $this->authorize('update', $weatherStation);

        // Check if token exists
        if (! $weatherStation->token) {
            return redirect()->route('netatmo.authenticate', $weatherStation)
                ->with('error', 'Please authenticate with Netatmo first.');
        }

        // Try to refresh token if expired
        if (! $weatherStation->token->hasValidToken()) {
            try {
                $weatherStation->token->refreshToken();
            } catch (Exception $e) {
                logger()->error('Failed to refresh token', [
                    'station_id' => $weatherStation->id,
                    'error' => $e->getMessage(),
                ]);

                return redirect()->route('netatmo.authenticate', $weatherStation)
                    ->with('error', 'Session expired. Please re-authenticate with Netatmo.');
            }
        }

        try {
            $devices = $netatmoService->getAvailableDevices($weatherStation);

            return view('netatmoweather::netatmo.select-device', compact('weatherStation', 'devices'));
        } catch (Exception $e) {
            logger()->error('Failed to get available devices', [
                'error' => $e->getMessage(),
                'station_id' => $weatherStation->id,
            ]);

            return redirect()->route('netatmo.index')
                ->with('error', 'Failed to get available devices: '.$e->getMessage());
        }
    }

    /**
     * Set the device ID for a station.
     */
    public function setDevice(Request $request, NetatmoStation $weatherStation): RedirectResponse
    {
        $this->authorize('update', $weatherStation);

        $validated = $request->validate([
            'device_id' => 'required|string|max:255',
        ]);

        $weatherStation->update([
            'device_id' => $validated['device_id'],
        ]);

        return redirect()->route('netatmo.show', $weatherStation)
            ->with('success', 'Device selected successfully.');
    }

    /**
     * Display the public view of a weather station.
     */
    public function publicShow(NetatmoStation $weatherStation, NetatmoService $netatmoService): View
    {
        // Check if the station is marked as public
        abort_if(! $weatherStation->is_public, 404, 'This weather station is not publicly available.');

        // Check if token exists
        if (! $weatherStation->token) {
            abort(503, 'Weather station data is currently unavailable. Please contact the station owner.');
        }

        // Try to refresh token if expired
        if (! $weatherStation->token->hasValidToken()) {
            try {
                $weatherStation->token->refreshToken();
            } catch (Exception $e) {
                logger()->error('Failed to refresh token for public view', [
                    'station_id' => $weatherStation->id,
                    'error' => $e->getMessage(),
                ]);

                abort(503, 'Weather station authentication has expired. Please contact the station owner.');
            }
        }

        try {
            // Fetch data from the weather station
            $netatmoService->getStationData($weatherStation);

            return view('netatmoweather::netatmo.public', compact('weatherStation'));
        } catch (Exception $e) {
            logger()->error('Failed to retrieve data from Netatmo for public view', [
                'error' => $e->getMessage(),
                'station_id' => $weatherStation->id,
            ]);

            abort(503, 'Unable to retrieve weather data at this time.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(NetatmoStation $weatherStation): View
    {
        $this->authorize('update', $weatherStation);

        $fields = $this->getFormFields();

        return view('netatmoweather::netatmo.form', [
            'fields' => $fields,
            'weatherStation' => $weatherStation,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(NetatmoWeatherStationRequest $request, NetatmoStation $weatherStation): RedirectResponse
    {
        $this->authorize('update', $weatherStation);

        $data = $request->validated();

        $weatherStation->update($data);

        return redirect()->route('netatmo.index')
            ->with('success', 'Weather station updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(NetatmoStation $weatherStation): RedirectResponse
    {
        $this->authorize('delete', $weatherStation);

        $weatherStation->delete();

        return redirect()->route('netatmo.index')
            ->with('success', 'Weather station deleted successfully.');
    }

    /**
     * Toggle public access for a weather station.
     */
    public function togglePublic(NetatmoStation $weatherStation)
    {
        // Check if the current user owns this station
        if ($weatherStation->user_id != auth()->id()) {
            return response()->json([
                'success' => false,
                'error' => 'Unauthorized',
            ], 403);
        }

        $weatherStation->update([
            'is_public' => ! $weatherStation->is_public,
        ]);

        return response()->json([
            'success' => true,
            'is_public' => $weatherStation->is_public,
        ]);
    }

    /**
     * Reactivate an archived module.
     */
    public function activateModule(NetatmoStation $weatherStation, NetatmoModule $module): RedirectResponse
    {
        $this->authorize('update', $weatherStation);

        // Verify the module belongs to this station
        if ($module->netatmo_station_id !== $weatherStation->id) {
            abort(404);
        }

        $module->update(['is_active' => true]);

        return redirect()->route('netatmo.show', $weatherStation)
            ->with('success', 'Module reactivated successfully.');
    }

    /**
     * Delete an archived module.
     */
    public function destroyModule(NetatmoStation $weatherStation, NetatmoModule $module): RedirectResponse
    {
        $this->authorize('update', $weatherStation);

        // Verify the module belongs to this station
        if ($module->netatmo_station_id !== $weatherStation->id) {
            abort(404);
        }

        // Only allow deletion of inactive modules
        if ($module->is_active) {
            return redirect()->route('netatmo.show', $weatherStation)
                ->with('error', 'Cannot delete active modules. Module must be inactive first.');
        }

        $module->delete();

        return redirect()->route('netatmo.show', $weatherStation)
            ->with('success', 'Module deleted successfully.');
    }
}
