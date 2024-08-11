<?php
//
//namespace Ekstremedia\NetatmoWeather\Http\Controllers;
//
//use App\Http\Controllers\Controller;
//use Ekstremedia\NetatmoWeather\Models\WeatherStation;
//use Illuminate\Http\Request;
//
//class NetatmoWeatherController extends Controller
//{
//    public function index(Request $request)
//    {
//        // Front page of netatmo weather
//        logger('NetatmoWeatherController@index');
//
//        $weatherStations = WeatherStation::where('user_id', auth()->id())->get();
//
//        return view('netatmoweather::netatmo.index', compact('weatherStations'));
//    }
//}
