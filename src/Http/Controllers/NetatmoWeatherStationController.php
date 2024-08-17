<?php

namespace Ekstremedia\NetatmoWeather\Http\Controllers;

use App\Http\Controllers\Controller;
use Ekstremedia\NetatmoWeather\Http\Requests\NetatmoWeatherStationRequest;
use Ekstremedia\NetatmoWeather\Models\NetatmoWeatherStation;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;

class NetatmoWeatherStationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        // Front page of netatmo weather
        logger()->info('Fetching weather stations', ['user_id' => auth()->id()]);

        $weatherStations = NetatmoWeatherStation::where('user_id', auth()->id())->get();

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

        NetatmoWeatherStation::create($data);

        return redirect()->route('netatmo.index')->with('success', 'Weather station created successfully.');

    }

    /**
     * Display the specified resource.
     */
    public function show(NetatmoWeatherStation $weatherStation): void
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(NetatmoWeatherStation $weatherStation): View
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
    public function update(NetatmoWeatherStationRequest $request, NetatmoWeatherStation $weatherStation): RedirectResponse
    {
        $data = $request->validated();

        $weatherStation->update($data);

        return redirect()->route('netatmo.index')->with('success', 'Weather station updated successfully.');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(NetatmoWeatherStation $weatherStation): RedirectResponse
    {
        $weatherStation->delete();

        return redirect()->route('netatmo.index')->with('success', 'Weather station deleted successfully.');
    }
}
