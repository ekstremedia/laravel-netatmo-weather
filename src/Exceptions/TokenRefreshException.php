<?php

namespace Ekstremedia\NetatmoWeather\Exceptions;

use Exception;

class TokenRefreshException extends Exception
{
    /**
     * Create exception for missing refresh token.
     */
    public static function noRefreshToken(): self
    {
        return new self('No refresh token available for authentication.');
    }

    /**
     * Create exception for missing weather station.
     */
    public static function stationNotFound(): self
    {
        return new self('Associated weather station not found.');
    }

    /**
     * Create exception for API error.
     */
    public static function apiError(string $message): self
    {
        return new self("Netatmo API error: {$message}");
    }
}
