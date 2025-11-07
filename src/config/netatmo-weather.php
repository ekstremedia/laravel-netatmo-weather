<?php

return [
    /*
     * Package name displayed in UI
     */
    'name' => env('NETATMO_PACKAGE_NAME', 'Laravel Netatmo Weather'),

    /*
     * The User model that will be used to associate with Netatmo stations.
     * You can customize this to use your own User model.
     */
    'user_model' => env('NETATMO_USER_MODEL', 'App\Models\User'),

    /*
     * Netatmo API endpoints
     */
    'netatmo_auth_url' => env('NETATMO_AUTH_URL', 'https://api.netatmo.com/oauth2/authorize'),
    'netatmo_token_url' => env('NETATMO_TOKEN_URL', 'https://api.netatmo.com/oauth2/token'),
    'netatmo_api_url' => env('NETATMO_API_URL', 'https://api.netatmo.com/api'),

    /*
     * Cache duration for station data in minutes
     * Data will be fetched from database if updated within this timeframe
     */
    'cache_duration_minutes' => env('NETATMO_CACHE_DURATION', 10),

    /*
     * Enable debug logging for troubleshooting
     */
    'debug_logging' => env('NETATMO_DEBUG_LOGGING', false),
];
