<?php

namespace Ekstremedia\NetatmoWeather\Models;

use Ekstremedia\NetatmoWeather\Services\TokenRefreshService;
use Ekstremedia\NetatmoWeather\Traits\Encryptable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NetatmoToken extends Model
{
    use Encryptable, HasFactory;

    protected $table = 'netatmo_tokens';

    protected $fillable = [
        'netatmo_station_id',
        'access_token',
        'refresh_token',
        'expires_at',
    ];

    /**
     * Attributes that should be encrypted.
     */
    protected array $encryptable = [
        'access_token',
        'refresh_token',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    /**
     * Get the user that owns this token.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(config('netatmo-weather.user_model', 'App\Models\User'));
    }

    /**
     * Get the weather station that owns this token.
     */
    public function netatmoStation(): BelongsTo
    {
        return $this->belongsTo(NetatmoStation::class, 'netatmo_station_id');
    }

    /**
     * Check if the token is valid and not expired.
     */
    public function hasValidToken(): bool
    {
        return $this->access_token && $this->expires_at && $this->expires_at->isFuture();
    }

    /**
     * Refresh the OAuth token using the refresh token.
     *
     *
     * @throws \Ekstremedia\NetatmoWeather\Exceptions\TokenRefreshException
     */
    public function refreshToken(): void
    {
        app(TokenRefreshService::class)->refreshToken($this);
    }
}
