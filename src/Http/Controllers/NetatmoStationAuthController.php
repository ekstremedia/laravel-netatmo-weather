<?php

namespace Ekstremedia\NetatmoWeather\Http\Controllers;

use Ekstremedia\NetatmoWeather\Exceptions\TokenRefreshException;
use Ekstremedia\NetatmoWeather\Models\NetatmoStation;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class NetatmoStationAuthController extends Controller
{
    use AuthorizesRequests;

    /**
     * Initiate OAuth authentication flow with Netatmo.
     */
    public function authenticate(NetatmoStation $weatherstation): RedirectResponse
    {
        // Verify ownership
        $this->authorize('authenticate', $weatherstation);

        try {
            $this->ensureValidToken($weatherstation);

            return redirect()->route('netatmo.index')
                ->with('success', 'Already authenticated with Netatmo.');
        } catch (TokenRefreshException $e) {
            logger()->info('Token refresh failed, redirecting to OAuth authentication', [
                'station_id' => $weatherstation->id,
                'error' => $e->getMessage(),
            ]);

            // Generate and store OAuth state token
            $state = Str::random(40);
            session()->put('netatmo_oauth_state_'.$weatherstation->id, $state);

            $queryParams = http_build_query([
                'client_id' => $weatherstation->client_id,
                'redirect_uri' => route('netatmo.callback', $weatherstation),
                'response_type' => 'code',
                'scope' => 'read_station',
                'state' => $state,
            ]);

            $authUrl = config('netatmo-weather.netatmo_auth_url').'?'.$queryParams;

            return redirect($authUrl);
        }
    }

    /**
     * Handle OAuth callback from Netatmo.
     */
    public function handleCallback(Request $request, NetatmoStation $weatherstation): RedirectResponse
    {
        // Verify ownership
        $this->authorize('authenticate', $weatherstation);

        // Check for errors in the callback
        if ($request->has('error')) {
            logger()->error('Netatmo OAuth authentication error', [
                'station_id' => $weatherstation->id,
                'error' => $request->get('error'),
                'error_description' => $request->get('error_description'),
            ]);

            return redirect()->route('netatmo.index')
                ->with('error', 'Authentication failed: '.$request->get('error_description', 'Unknown error'));
        }

        // Validate input
        $validated = $request->validate([
            'code' => 'required|string|max:255',
            'state' => 'required|string|max:255',
        ]);

        // Validate OAuth state to prevent CSRF
        $sessionState = session()->get('netatmo_oauth_state_'.$weatherstation->id);
        if (! $sessionState || $validated['state'] !== $sessionState) {
            logger()->warning('OAuth state mismatch - possible CSRF attack', [
                'station_id' => $weatherstation->id,
                'ip' => $request->ip(),
            ]);

            return redirect()->route('netatmo.index')
                ->with('error', 'Invalid authentication state. Please try again.');
        }

        // Clear the state from session
        session()->forget('netatmo_oauth_state_'.$weatherstation->id);

        // Exchange authorization code for access token
        $response = Http::asForm()->post(config('netatmo-weather.netatmo_token_url'), [
            'grant_type' => 'authorization_code',
            'client_id' => $weatherstation->client_id,
            'client_secret' => $weatherstation->client_secret,
            'code' => $validated['code'],
            'redirect_uri' => route('netatmo.callback', $weatherstation),
        ]);

        if ($response->failed()) {
            logger()->error('Failed to exchange authorization code for token', [
                'station_id' => $weatherstation->id,
                'status' => $response->status(),
            ]);

            return redirect()->route('netatmo.index')
                ->with('error', 'Failed to authenticate with Netatmo.');
        }

        $tokens = $response->json();

        // Store the tokens
        $weatherstation->token()->updateOrCreate(
            ['netatmo_station_id' => $weatherstation->id],
            [
                'access_token' => $tokens['access_token'],
                'refresh_token' => $tokens['refresh_token'],
                'expires_at' => now()->addSeconds($tokens['expires_in']),
            ]
        );

        logger()->info('Netatmo authentication successful', [
            'station_id' => $weatherstation->id,
        ]);

        return redirect()->route('netatmo.index')
            ->with('success', 'Successfully authenticated with Netatmo.');
    }

    /**
     * Ensure the weather station has a valid token.
     *
     *
     * @throws TokenRefreshException
     */
    protected function ensureValidToken(NetatmoStation $weatherstation): void
    {
        // Check if the token is valid
        if ($weatherstation->token && $weatherstation->token->hasValidToken()) {
            return;
        }

        // No token or no refresh token available
        if (! $weatherstation->token || ! $weatherstation->token->refresh_token) {
            throw TokenRefreshException::noRefreshToken();
        }

        // Attempt to refresh the token
        $weatherstation->token->refreshToken();
    }
}
