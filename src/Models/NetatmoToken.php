<?php

namespace Ekstremedia\NetatmoWeather\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

class NetatmoToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'access_token',
        'refresh_token',
        'netatmo_station_id',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime', // This will cast expires_at to a Carbon instance
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function hasValidToken(): bool
    {
        return $this->access_token && $this->expires_at && $this->expires_at->isFuture();
    }

    /**
     * @throws ConnectionException
     */
    public function refreshToken(): void
    {
        logger('Refreshing token', [
            'station_id' => $this->station_id,
        ]);

        if (! $this->refresh_token) {
            throw new \Exception('No refresh token available.');
        }

        // Retrieve the associated weather station
        $weatherStation = $this->weatherStation;

        if (! $weatherStation) {
            throw new \Exception('Associated weather station not found.');
        }

        //        dd(config('netatmo-weather.netatmo_token_url'));
        //        dd([
        //            'grant_type' => 'refresh_token',
        //            'client_id' => $weatherStation->client_id,
        //            'client_secret' => $weatherStation->client_secret,
        //            'refresh_token' => $this->refresh_token,
        //        ]);
        $response = Http::asForm()->post(config('netatmo-weather.netatmo_token_url'), [
            'grant_type' => 'refresh_token',
            'client_id' => $weatherStation->client_id,
            'client_secret' => $weatherStation->client_secret,
            'refresh_token' => $this->refresh_token,
        ]);

        if ($response->failed()) {
            ray($response->json());
            throw new \Exception('Failed to refresh token.');
        }

        $tokens = $response->json();

        $this->update([
            'access_token' => $tokens['access_token'],
            'expires_at' => now()->addSeconds($tokens['expires_in']),
        ]);

        logger('Token refreshed!!!!!', [
            'station_id' => $this->station_id,
        ]);
    }

    public function station(): BelongsTo
    {
        return $this->belongsTo(NetatmoStation::class, 'netatmo_station_id');
    }
}
