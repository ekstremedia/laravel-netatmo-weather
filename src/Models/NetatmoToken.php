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
            'station_id' => $this->netatmo_station_id,
            'refresh_token' => $this->refresh_token,
        ]);

        if (!$this->refresh_token) {
            throw new \Exception('No refresh token available.');
        }

        $weatherStation = NetatmoStation::find($this->netatmo_station_id);

        ray($weatherStation->client_id, $weatherStation->client_secret);

        if (!$weatherStation) {
            throw new \Exception('Associated weather station not found.');
        }

        $apiData = [
            'grant_type' => 'refresh_token',
            'client_id' => $weatherStation->client_id,
            'client_secret' => $weatherStation->client_secret,
            'refresh_token' => $this->refresh_token,
        ];

        ray($apiData)->green();
        ray(config('netatmo-weather.netatmo_token_url'));

        $response = Http::asForm()->post(config('netatmo-weather.netatmo_token_url'), $apiData);

        logger('Netatmo API Response', [
            'response' => $response->json(),
            'status' => $response->status(),
        ]);

        if ($response->failed()) {
            logger('Failed to refresh token ;/', $response->json());
            throw new \Exception('Failed to refresh tokenz.');
        }

        $tokens = $response->json();

        $this->update([
            'access_token' => $tokens['access_token'],
            'refresh_token' => $tokens['refresh_token'],
            'expires_at' => now()->addSeconds($tokens['expires_in']),
        ]);

        logger('Token refreshed successfully', [
            'station_id' => $this->netatmo_station_id,
        ]);
    }

    public function station(): BelongsTo
    {
        return $this->belongsTo(NetatmoStation::class, 'netatmo_station_id');
    }
}
