<?php

return [
    'name' => 'Laravel Netatmo Weather',

    /*
     * The User model that will be used to associate with Netatmo stations.
     * You can customize this to use your own User model.
     */
    'user_model' => env('NETATMO_USER_MODEL', 'App\Models\User'),

    'netatmo_auth_url' => env('NETATMO_AUTH_URL', 'https://api.netatmo.com/oauth2/authorize'),
    'netatmo_token_url' => env('NETATMO_TOKEN_URL', 'https://api.netatmo.com/oauth2/token'),
    'netatmo_api_url' => env('NETATMO_API_URL', 'https://api.netatmo.com/api'),
];
