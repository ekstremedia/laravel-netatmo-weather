<?php

namespace Ekstremedia\NetatmoWeather\Http\Controllers;

use App\Http\Controllers\Controller;
use Ekstremedia\NetatmoWeather\Models\NetatmoWeatherStation;
use Illuminate\Http\Request;

class NetatmoWeatherStationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Front page of netatmo weather
        logger('NetatmoWeatherStationController@index');

        $weatherStations = NetatmoWeatherStation::where('user_id', auth()->id())->get();

        return view('netatmoweather::netatmo.index', compact('weatherStations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $fields = $this->getFormFields();
        ray($fields);
        return view('netatmoweather::netatmo.form', [
            'fields' => $fields
        ]);

    }
    private function getFormFields(): array
    {
        return [
            ['name' => 'station_name', 'type' => 'text', 'label' => 'Brand', 'required' => true]
        ];
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(NetatmoWeatherStation $weatherStation)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(NetatmoWeatherStation $weatherStation)
    {
        $fields = $this->getFormFields();
        return view('netatmoweather::netatmo.form', [
            'fields' => $fields,
            'weatherStation' => $weatherStation
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, NetatmoWeatherStation $weatherStation)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(NetatmoWeatherStation $weatherStation)
    {
        //
    }
}
