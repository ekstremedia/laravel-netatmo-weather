<?php

namespace Ekstremedia\NetatmoWeather\Services;

use Ekstremedia\NetatmoWeather\Exceptions\TokenRefreshException;
use Ekstremedia\NetatmoWeather\Models\NetatmoToken;
use Illuminate\Support\Facades\Http;

class TokenRefreshService
{
    /**
     * Refresh the OAuth token for a Netatmo station.
     *
     *
     * @throws TokenRefreshException
     */
    public function refreshToken(NetatmoToken $token): void
    {
        if (! $token->refresh_token) {
            throw TokenRefreshException::noRefreshToken();
        }

        $weatherStation = $token->netatmoStation;

        if (! $weatherStation) {
            throw TokenRefreshException::stationNotFound();
        }

        logger()->info('Attempting to refresh Netatmo token', [
            'station_id' => $weatherStation->id,
        ]);

        $response = Http::asForm()->post(config('netatmo-weather.netatmo_token_url'), [
            'grant_type' => 'refresh_token',
            'client_id' => $weatherStation->client_id,
            'client_secret' => $weatherStation->client_secret,
            'refresh_token' => $token->refresh_token,
        ]);

        if ($response->failed()) {
            logger()->error('Failed to refresh Netatmo token', [
                'station_id' => $weatherStation->id,
                'status' => $response->status(),
            ]);

            throw TokenRefreshException::apiError('Failed to refresh token');
        }

        $tokens = $response->json();

        $token->update([
            'access_token' => $tokens['access_token'],
            'refresh_token' => $tokens['refresh_token'],
            'expires_at' => now()->addSeconds($tokens['expires_in']),
        ]);

        logger()->info('Token refreshed successfully', [
            'station_id' => $weatherStation->id,
        ]);
    }
}
