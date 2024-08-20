<?php

namespace Ekstremedia\NetatmoWeather\Http\Controllers;

use App\Http\Controllers\Controller;
use Ekstremedia\NetatmoWeather\Http\Requests\NetatmoWeatherStationRequest;
use Ekstremedia\NetatmoWeather\Models\NetatmoStation;
use Ekstremedia\NetatmoWeather\Services\NetatmoService;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class NetatmoStationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {

        //        $weatherStation = NetatmoStation::find(1);
        //        $weatherStation->token->refreshToken();
        //        dd("HER", $weatherStation);
        // Log the action of fetching weather stations

        // Fetch weather stations for the authenticated user
        $weatherStations = NetatmoStation::where('user_id', auth()->id())->get();

        //        logger()->info('Fetching weather stations', ['$weatherStations' => $weatherStations]);

        // Check each weather station's token validity and refresh if necessary
        foreach ($weatherStations as $weatherStation) {
            if ($weatherStation->token && ! $weatherStation->token->hasValidToken()) {
                try {
                    $weatherStation->token->refreshToken();
                } catch (Exception $e) {
                    logger()->error('Failed to refresh token!!', [
                        'weather_station_id' => $weatherStation->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }

        // Return the view with the list of weather stations
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
            ['name' => 'client_secret', 'type' => 'text', 'required' => true],
            ['name' => 'redirect_uri', 'type' => 'text', 'required' => false],
            ['name' => 'webhook_uri', 'type' => 'text', 'required' => false],
        ];
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(NetatmoWeatherStationRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['user_id'] = auth()->id();

        NetatmoStation::create($data);

        return redirect()->route('netatmo.index')->with('success', 'Weather station created successfully.');

    }

    /**
     * Display the specified resource.
     */
    public function show(NetatmoStation $weatherStation, NetatmoService $netatmoService): view|RedirectResponse
    {
        try {
            // Fetch data from the weather station
            $netatmoService->getStationData($weatherStation);
//            $weatherStation->load('modules.latestReading');
            //          $weatherStation->refresh();

            return view('netatmoweather::netatmo.show', compact('weatherStation'));
        } catch (Exception $e) {
            ray($e);

            return redirect()->route('netatmo.index')->with('error', 'Failed to retrieve data from Netatmo: '.$e->getMessage());
        }
        //        return view('netatmoweather::netatmo.show', compact('weatherStation'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(NetatmoStation $weatherStation): View
    {
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
        $data = $request->validated();

        $weatherStation->update($data);

        return redirect()->route('netatmo.index')->with('success', 'Weather station updated successfully.');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(NetatmoStation $weatherStation): RedirectResponse
    {
        $weatherStation->delete();

        return redirect()->route('netatmo.index')->with('success', 'Weather station deleted successfully.');
    }
}
