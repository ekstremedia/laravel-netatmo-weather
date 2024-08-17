<?php

namespace Ekstremedia\NetatmoWeather\Http\Controllers;

use Ekstremedia\NetatmoWeather\Models\NetatmoWeatherStation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

class NetatmoWeatherStationAuthController extends Controller
{
    public function authenticate(NetatmoWeatherStation $weatherstation): RedirectResponse
    {
        if ($weatherstation->token?->hasValidToken()) {
            return redirect()->route('netatmo.index')->with('success', 'Already authenticated.');
        }

        $queryParams = http_build_query([
            'client_id' => $weatherstation->client_id,
            'redirect_uri' => route('netatmo.callback', $weatherstation),
            'response_type' => 'code',
            'scope' => 'read_station',
            'state' => csrf_token(),
        ]);

        $authUrl = config('netatmo-weather.netatmo_auth_url') . '?' . $queryParams;

        return redirect($authUrl);
    }

    public function handleCallback(Request $request, NetatmoWeatherStation $weatherstation): RedirectResponse
    {
        if ($request->has('error')) {
            return redirect()->route('netatmo.index')->with('error', 'Authentication failed.');
        }

        $response = Http::asForm()->post(config('netatmo-weather.netatmo_token_url'), [
            'grant_type' => 'authorization_code',
            'client_id' => $weatherstation->client_id,
            'client_secret' => $weatherstation->client_secret,
            'code' => $request->get('code'),
            'redirect_uri' => route('netatmo.callback', $weatherstation),
        ]);

        if ($response->failed()) {
            return redirect()->route('netatmo.index')->with('error', 'Failed to authenticate with Netatmo.');
        }

        $tokens = $response->json();

        // Create or update the token in the NetatmoWeatherToken model
        $weatherstation->token()->updateOrCreate(
            ['netatmo_weather_station_id' => $weatherstation->id],
            [
                'access_token' => $tokens['access_token'],
                'refresh_token' => $tokens['refresh_token'],
                'expires_at' => now()->addSeconds($tokens['expires_in']),
            ]
        );

        return redirect()->route('netatmo.index')->with('success', 'Authenticated successfully.');
    }

}
