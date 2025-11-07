<?php

namespace Ekstremedia\NetatmoWeather\Http\Controllers;

use Ekstremedia\NetatmoWeather\Models\NetatmoStation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Http;

class NetatmoStationAuthController extends Controller
{
    public function authenticate(NetatmoStation $weatherstation): RedirectResponse
    {
        try {
            $this->ensureValidToken($weatherstation);

            return redirect()->route('netatmo.index')->with('success', 'Already authenticated.');
        } catch (\Exception $e) {
            // If token refresh fails, proceed with re-authentication
            $queryParams = http_build_query([
                'client_id' => $weatherstation->client_id,
                'redirect_uri' => route('netatmo.callback', $weatherstation),
                'response_type' => 'code',
                'scope' => 'read_station',
                'state' => csrf_token(),
            ]);

            $authUrl = config('netatmo-weather.netatmo_auth_url').'?'.$queryParams;

            return redirect($authUrl);
        }
    }

    public function handleCallback(Request $request, NetatmoStation $weatherstation): RedirectResponse
    {
        if ($request->has('error')) {
            logger()->error('Netatmo authentication error', $request->all());

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
            ['netatmo_station_id' => $weatherstation->id],
            [
                'access_token' => $tokens['access_token'],
                'refresh_token' => $tokens['refresh_token'],
                'expires_at' => now()->addSeconds($tokens['expires_in']),
            ]
        );

        return redirect()->route('netatmo.index')->with('success', 'Authenticated successfully.');
    }

    public function ensureValidToken(NetatmoStation $weatherstation): void
    {
        // Check if the token is valid or if it needs to be refreshed
        if ($weatherstation->token && $weatherstation->token->hasValidToken()) {
            return; // Token is valid, no need to refresh
        }

        if (! $weatherstation->token || ! $weatherstation->token->refresh_token) {
            throw new \Exception('No refresh token available.');
        }

        // Make the request to refresh the token
        $response = Http::asForm()->post(config('netatmo-weather.netatmo_token_url'), [
            'grant_type' => 'refresh_token',
            'client_id' => $weatherstation->client_id,
            'client_secret' => $weatherstation->client_secret,
            'refresh_token' => $weatherstation->token->refresh_token,
        ]);

        if ($response->failed()) {
            throw new \Exception('Failed to refresh token!.');
        }

        $tokens = $response->json();

        // Update the token in the database
        $weatherstation->token()->update([
            'access_token' => $tokens['access_token'],
            'refresh_token' => $tokens['refresh_token'],
            'expires_at' => now()->addSeconds($tokens['expires_in']),
        ]);
    }
}
