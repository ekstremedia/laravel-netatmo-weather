# Laravel Netatmo Weather

[![Latest Version](https://img.shields.io/packagist/v/ekstremedia/laravel-netatmo-weather)](https://packagist.org/packages/ekstremedia/laravel-netatmo-weather)
[![Total Downloads](https://img.shields.io/packagist/dt/ekstremedia/laravel-netatmo-weather)](https://packagist.org/packages/ekstremedia/laravel-netatmo-weather)
[![Tests](https://github.com/ekstremedia/laravel-netatmo-weather/workflows/Tests/badge.svg)](https://github.com/ekstremedia/laravel-netatmo-weather/actions)
[![codecov](https://codecov.io/gh/ekstremedia/laravel-netatmo-weather/branch/main/graph/badge.svg)](https://codecov.io/gh/ekstremedia/laravel-netatmo-weather)
[![PHP Version](https://img.shields.io/packagist/php-v/ekstremedia/laravel-netatmo-weather)](https://packagist.org/packages/ekstremedia/laravel-netatmo-weather)
[![Laravel Version](https://img.shields.io/badge/Laravel-11.x%20%7C%2012.x-orange)](https://laravel.com)
[![License](https://img.shields.io/packagist/l/ekstremedia/laravel-netatmo-weather)](https://github.com/ekstremedia/laravel-netatmo-weather/blob/main/LICENSE)

A Laravel package for integrating Netatmo Weather Station API with your Laravel application. This package provides an easy-to-use interface for authenticating with Netatmo, fetching weather data, and storing it in your database.

## Features

- OAuth2 authentication with Netatmo API
- Automatic token refresh
- Fetch and store weather station data
- Support for multiple weather stations per user
- Built-in database models and migrations
- Encrypted storage of sensitive credentials
- Configurable caching of weather data
- Full UI scaffolding (optional)

## Requirements

- PHP 8.2 or higher
- Laravel 11.0 or 12.0
- A Netatmo Weather Station
- Netatmo API credentials (Client ID and Client Secret)

## Installation

### 1. Install the package via Composer

```bash
composer require ekstremedia/laravel-netatmo-weather
```

### 2. Publish the configuration file

```bash
php artisan vendor:publish --tag=config --provider="Ekstremedia\NetatmoWeather\NetatmoWeatherServiceProvider"
```

### 3. Publish the public assets (optional, if using the included UI)

```bash
php artisan vendor:publish --tag=public --provider="Ekstremedia\NetatmoWeather\NetatmoWeatherServiceProvider"
```

### 4. Run the migrations

```bash
php artisan migrate
```

## Configuration

### 1. Get Netatmo API Credentials

1. Go to [Netatmo Developer Portal](https://dev.netatmo.com/)
2. Create an app or use an existing one
3. Copy your Client ID and Client Secret

### 2. Configure your environment

Add the following to your `.env` file:

```env
NETATMO_AUTH_URL=https://api.netatmo.com/oauth2/authorize
NETATMO_TOKEN_URL=https://api.netatmo.com/oauth2/token
NETATMO_API_URL=https://api.netatmo.com/api

# Optional: Customize the User model
# NETATMO_USER_MODEL=App\Models\User
```

### 3. Configure the package

The configuration file `config/netatmo-weather.php` contains:

```php
return [
    'name' => 'Laravel Netatmo Weather',

    // The User model that will be used to associate with Netatmo stations
    'user_model' => env('NETATMO_USER_MODEL', 'App\Models\User'),

    // Netatmo API URLs
    'netatmo_auth_url' => env('NETATMO_AUTH_URL', 'https://api.netatmo.com/oauth2/authorize'),
    'netatmo_token_url' => env('NETATMO_TOKEN_URL', 'https://api.netatmo.com/oauth2/token'),
    'netatmo_api_url' => env('NETATMO_API_URL', 'https://api.netatmo.com/api'),
];
```

## Usage

### Using the Service

```php
use Ekstremedia\NetatmoWeather\Services\NetatmoService;
use Ekstremedia\NetatmoWeather\Models\NetatmoStation;

// Get the service instance
$netatmoService = app(NetatmoService::class);

// Find a weather station
$weatherStation = NetatmoStation::first();

// Fetch data from Netatmo API
// This will automatically cache data for 10 minutes
$data = $netatmoService->getStationData($weatherStation);
```

### Using the Models

```php
use Ekstremedia\NetatmoWeather\Models\NetatmoStation;
use Ekstremedia\NetatmoWeather\Models\NetatmoModule;
use Ekstremedia\NetatmoWeather\Models\NetatmoToken;

// Get all weather stations
$stations = NetatmoStation::all();

// Get a station with its modules
$station = NetatmoStation::with('modules')->first();

// Check if token is valid
if ($station->token->hasValidToken()) {
    echo "Token is valid!";
}

// Manually refresh token
$station->token->refreshToken();

// Access module data
foreach ($station->modules as $module) {
    echo "Module: {$module->module_name}\n";
    echo "Type: {$module->type}\n";
    echo "Battery: {$module->battery_percent}%\n";
}
```

### Using the UI (Optional)

If you want to use the included UI scaffolding, the package provides routes and controllers:

```php
// In your routes/web.php or through the package routes
// The following routes are automatically registered:

Route::get('/netatmo', [NetatmoStationController::class, 'index'])->name('netatmo.index');
Route::get('/netatmo/create', [NetatmoStationController::class, 'create'])->name('netatmo.create');
Route::post('/netatmo', [NetatmoStationController::class, 'store'])->name('netatmo.store');
Route::get('/netatmo/{weatherstation}', [NetatmoStationController::class, 'show'])->name('netatmo.show');
Route::get('/netatmo/{weatherstation}/edit', [NetatmoStationController::class, 'edit'])->name('netatmo.edit');
Route::put('/netatmo/{weatherstation}', [NetatmoStationController::class, 'update'])->name('netatmo.update');
Route::delete('/netatmo/{weatherstation}', [NetatmoStationController::class, 'destroy'])->name('netatmo.destroy');
```

Visit `/netatmo` in your browser to manage your weather stations.

## Authentication Flow

1. Create a weather station record with your Client ID, Client Secret, and Redirect URI
2. Navigate to the authentication route: `/netatmo/{station}/authenticate`
3. You'll be redirected to Netatmo to authorize the app
4. After authorization, you'll be redirected back with access tokens
5. The package automatically stores and refreshes tokens as needed

## Data Structure

### NetatmoStation

- `id` - Primary key
- `uuid` - UUID for public routing
- `user_id` - Associated user
- `station_name` - Name of the station
- `client_id` - Encrypted Netatmo Client ID
- `client_secret` - Encrypted Netatmo Client Secret
- `redirect_uri` - OAuth redirect URI
- `webhook_uri` - Webhook URI (optional)

### NetatmoModule

Stores data for each module (base station and add-on modules):

- Module identification (module_id, module_name, type)
- Battery status
- Connection status (wifi_status, rf_status, reachable)
- Dashboard data (stored as JSON)

### NetatmoToken

- `access_token` - Current access token
- `refresh_token` - Refresh token
- `expires_at` - Token expiration timestamp

## Supported Module Types

- `NAMain` - Main indoor module
- `NAModule1` - Outdoor module
- `NAModule2` - Wind gauge
- `NAModule3` - Rain gauge
- `NAModule4` - Additional indoor module

## Caching

The package automatically caches weather data for 10 minutes to reduce API calls. You can modify this behavior in the `NetatmoService` class.

## Security

- Client ID and Client Secret are automatically encrypted in the database
- Tokens are securely stored and automatically refreshed
- All API communications use HTTPS

## Testing

The package includes comprehensive test coverage using **Pest PHP**:

```bash
# Run all tests
composer test

# Run tests with coverage report
composer test-coverage

# Run specific test suite
vendor/bin/pest --testsuite Unit
vendor/bin/pest --testsuite Feature

# Run tests in parallel
vendor/bin/pest --parallel

# Check code style
composer format
vendor/bin/pint --test
```

### Test Coverage

**Unit Tests (14 tests):**
- Model functionality (NetatmoStation, NetatmoToken, NetatmoModule)
- Encryption and UUID generation
- Relationships and cascade deletes
- Token validation and expiration
- JSON field casting

**Feature Tests (13 tests):**
- CRUD operations for weather stations
- Authentication flow and redirects
- API integration with HTTP mocking
- Data caching (10-minute strategy)
- Module updates without duplication

### Why Pest?

Pest provides a more elegant and expressive testing syntax:

```php
it('can create a netatmo station', function () {
    $station = NetatmoStation::create([...]);
    expect($station->uuid)->not->toBeNull();
});
```

### CI/CD

The package uses GitHub Actions for continuous integration:
- Tests against PHP 8.2 & 8.3
- Tests against Laravel 11.x & 12.x
- Uses Pest for elegant test execution
- Code style checks with Laravel Pint
- Runs on every push and pull request

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## License

This package is open-sourced software licensed under the MIT license.

## Credits

- [Terje Nesthus](https://github.com/ekstremedia)
- [All Contributors](../../contributors)

## Support

If you discover any security-related issues, please email terje@nesthus.no instead of using the issue tracker.

## Links

- [Netatmo API Documentation](https://dev.netatmo.com/apidocumentation/weather)
- [Netatmo Developer Portal](https://dev.netatmo.com/)
