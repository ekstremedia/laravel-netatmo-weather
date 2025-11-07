<?php

namespace Ekstremedia\NetatmoWeather\Exceptions;

use Exception;

class InvalidApiResponseException extends Exception
{
    /**
     * Create exception for missing devices in API response.
     */
    public static function noDevices(): self
    {
        return new self('No devices found in Netatmo API response.');
    }

    /**
     * Create exception for missing required fields.
     */
    public static function missingRequiredFields(string $fields): self
    {
        return new self("Required fields missing from API response: {$fields}");
    }
}
