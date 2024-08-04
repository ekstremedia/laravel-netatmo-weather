<?php

namespace Ekstremedia\NetatmoWeather\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NetatmoWeatherController extends Controller
{
    public function index(Request $request)
    {
        // Your code here
        return view('netatmoweather::netatmo.index');
    }
}
