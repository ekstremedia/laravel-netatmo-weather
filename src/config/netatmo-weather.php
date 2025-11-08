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
     * Cache duration for API responses in minutes
     * Shorter cache for API to provide more real-time data
     */
    'api_cache_duration_minutes' => env('NETATMO_API_CACHE_DURATION', 5),

    /*
     * Route configuration
     * Customize route prefixes and middleware for different route groups
     */
    'routes' => [
        // Web routes (UI) - includes authenticated and public routes
        // Web routes use default /netatmo prefix and handle middleware internally
        'web' => [
            'enabled' => env('NETATMO_WEB_ROUTES_ENABLED', true),
        ],

        // API routes (JSON responses) - fully configurable
        'api' => [
            'enabled' => env('NETATMO_API_ROUTES_ENABLED', true),
            'prefix' => env('NETATMO_API_PREFIX', 'api/netatmo'),
            'middleware' => ['web', 'api'], // Both web and api for session + token auth
        ],
    ],

    /*
     * Enable debug logging for troubleshooting
     */
    'debug_logging' => env('NETATMO_DEBUG_LOGGING', false),
];
