<?php

class NetatmoWeatherController extends Controller {
    public function index(): Response
    {
        return view('netatmo.index');
    }
}
