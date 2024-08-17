<?php

namespace Ekstremedia\NetatmoWeather\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

class NetatmoWeatherToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'access_token',
        'refresh_token',
        'netatmo_weather_station_id',
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
        if (!$this->refresh_token) {
            throw new \Exception('No refresh token available.');
        }

        $response = Http::asForm()->post(config('netatmo.netatmo_token_url'), [
            'grant_type' => 'refresh_token',
            'client_id' => $this->client_id,
            'client_secret' => $this->client_secret,
            'refresh_token' => $this->refresh_token,
        ]);

        if ($response->failed()) {
            throw new \Exception('Failed to refresh token.');
        }

        $tokens = $response->json();

        $this->update([
            'access_token' => $tokens['access_token'],
            'expires_at' => now()->addSeconds($tokens['expires_in']),
        ]);
    }
}
