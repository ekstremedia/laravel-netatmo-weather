# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is **laravel-netatmo-weather**, a Laravel package for integrating Netatmo Weather Station API. It provides OAuth2 authentication, automatic token refresh, and weather data fetching with database storage.

**Package Details:**
- Namespace: `Ekstremedia\NetatmoWeather`
- Supports: PHP 8.2+, Laravel 11.0-12.0
- Published as: `ekstremedia/laravel-netatmo-weather`

## Development Commands

### Code Quality
```bash
# Run Laravel Pint (code formatter)
vendor/bin/pint

# Run tests (when implemented)
composer test
```

### Package Development Setup
```bash
# Install dependencies
composer install

# Publish configuration to test apps
php artisan vendor:publish --tag=config --provider="Ekstremedia\NetatmoWeather\NetatmoWeatherServiceProvider"

# Publish assets (images/icons)
php artisan vendor:publish --tag=public --provider="Ekstremedia\NetatmoWeather\NetatmoWeatherServiceProvider" --force

# Run migrations
php artisan migrate
```

### Frontend Styling

The package uses **Tailwind CSS** with a CDN approach for zero build step integration:
- CDN loaded in `src/resources/views/layouts/app.blade.php`
- **Dark theme with deep purple color scheme**:
  - `netatmo-purple` (#8b5cf6) - Primary purple
  - `netatmo-deep` (#6d28d9) - Deep purple
  - `netatmo-dark` (#5b21b6) - Darkest purple
  - `dark-bg` (#0f0a1f) - Background
  - `dark-surface` (#1a1332) - Surface elements
  - `dark-elevated` (#251b47) - Elevated elements
  - `dark-border` (#3d2e6b) - Borders
  - `weather-warm` (#f59e0b) - Warm weather
  - `weather-cool` (#06b6d4) - Cool weather
- Uses Alpine.js for interactive components (modals, sidebar, notifications)
- Fully responsive design with mobile-first approach
- Modern glassmorphism effects with backdrop blur and shadows
- Purple-themed gradients throughout UI elements

**For production integration**, consumers should:
1. Add package views to their Tailwind config:
```javascript
content: [
    './vendor/ekstremedia/laravel-netatmo-weather/src/resources/views/**/*.blade.php',
]
```
2. Rebuild their assets to include package classes
3. Or continue using the CDN approach for zero-config setup

## Architecture Overview

### Core Service Layer

**NetatmoService** (`src/Services/NetatmoService.php`) - Central API integration service
- Fetches station data from Netatmo API with automatic 10-minute caching
- Handles token refresh before API calls
- Stores/updates module data in database
- Debugging: Saves API responses to `storage/logs/getstationsdata-{station_id}.json`

Key method: `getStationData(NetatmoStation $weatherStation): array`

### Authentication Flow

The package implements OAuth2 with automatic token refresh:

1. **Initial Auth**: User navigates to `/netatmo/authenticate/{weatherstation}` → redirects to Netatmo OAuth
2. **Callback**: Netatmo redirects to `/netatmo/callback/{weatherstation}` with auth code
3. **Token Storage**: `NetatmoStationAuthController` exchanges code for tokens, stores in `netatmo_tokens` table
4. **Auto-Refresh**: Before API calls, `NetatmoService` checks token validity via `hasValidToken()` and refreshes if expired

**Controllers:**
- `NetatmoStationAuthController` - Handles OAuth flow (`authenticate()`, `handleCallback()`, `ensureValidToken()`)
- `NetatmoStationController` - CRUD operations for weather stations

### Data Models

**NetatmoStation** (`src/Models/NetatmoStation.php`)
- Uses UUID for routing (`getRouteKeyName()` returns 'uuid')
- Encrypts sensitive fields via `Encryptable` trait: `client_id`, `client_secret`
- Relationships: `hasOne(NetatmoToken)`, `hasMany(NetatmoModule)`
- Always eager loads `token` relationship

**NetatmoToken** (`src/Models/NetatmoToken.php`)
- Manages OAuth tokens with automatic refresh
- `hasValidToken()`: Checks if `expires_at` is in future
- `refreshToken()`: Exchanges refresh_token for new tokens via Netatmo API
- Extensive logging for debugging token refresh issues

**NetatmoModule** (`src/Models/NetatmoModule.php`)
- Stores individual module data (indoor/outdoor sensors, wind gauge, rain gauge, etc.)
- Casts: `dashboard_data`, `data_type`, `user`, `place` to arrays
- Module types: `NAMain` (base), `NAModule1` (outdoor), `NAModule2` (wind), `NAModule3` (rain), `NAModule4` (indoor)

**NetatmoModuleReading** (`src/Models/NetatmoModuleReading.php`)
- Stores historical readings (implementation details in migrations)

### Key Traits

**Encryptable** (`src/Traits/Encryptable.php`)
- Automatically encrypts/decrypts model attributes using Laravel's `Crypt` facade
- Intercepts `getAttribute()` and `setAttribute()` methods
- Usage: Define `protected array $encryptable = ['field_name']` on model

**HasUuid** (`src/Traits/HasUuid.php`)
- Auto-generates UUID for models on creation

### Configuration

**config/netatmo-weather.php:**
- `user_model`: Configurable User model (defaults to `App\Models\User`)
- API URLs: `netatmo_auth_url`, `netatmo_token_url`, `netatmo_api_url`

All configurable via environment variables (see README).

### Routes & Middleware

Routes defined in `src/routes/web.php`:
- All routes under `/netatmo` prefix
- Middleware: `['web', 'auth']` - requires authenticated users
- Resource routes use `weatherStation` parameter binding (resolves by UUID)

### Service Provider

**NetatmoWeatherServiceProvider** (`src/NetatmoWeatherServiceProvider.php`)
- Registers views, translations, migrations, config
- Publishes assets to `public/netatmo-weather/images`
- Custom Blade directives:
  - `@datetime($timestamp)` - Formats Unix timestamp to 'Y-m-d H:i'
  - `@time($timestamp)` - Formats Unix timestamp to 'H:i'

### Views & UI

Optional UI scaffolding in `src/resources/views/`:
- Main layouts: `layouts/app.blade.php`, `layouts/navbar.blade.php`, `layouts/sidebar.blade.php`
- Station management: `netatmo/index.blade.php`, `netatmo/form.blade.php`, `netatmo/show.blade.php`
- Module widgets: `netatmo/widgets/{NAMain,NAModule1,NAModule2,NAModule3,NAModule4}.blade.php`
- Each module type has dedicated widget displaying relevant sensor data

## Important Implementation Details

### Token Refresh Strategy
- Tokens checked before EVERY API call in `NetatmoService::getStationData()`
- Automatic refresh if `expires_at` is in past
- Refresh token endpoint: `POST /oauth2/token` with `grant_type=refresh_token`
- Failed refreshes throw exceptions (logged extensively for debugging)

### Data Caching
- Weather data cached for 10 minutes by checking module `updated_at` timestamps
- If latest module update < 10 minutes old, returns cached data from database
- Otherwise fetches fresh data from Netatmo API

### Security Considerations
- Client credentials encrypted at rest using Laravel's encryption
- Tokens stored securely in dedicated table
- All API calls use HTTPS
- CSRF protection on OAuth state parameter

### Module Data Storage
- Main device (base station) stored as module with type `NAMain`
- Add-on modules stored separately with types `NAModule1-4`
- `dashboard_data` field stores raw sensor readings as JSON
- `updateOrCreate` pattern prevents duplicate modules on data refresh

## Testing Notes

- Test suite not yet implemented (testbench configured in composer.json)
- Manual testing requires valid Netatmo API credentials
- Use Storage facade logs disk for debugging API responses

## Common Gotchas

1. **UUID Routing**: Routes use `uuid` not `id` - models must have UUID generated via `HasUuid` trait
2. **Encryption**: Accessing `client_id`/`client_secret` automatically decrypts - no manual handling needed
3. **Eager Loading**: `NetatmoStation` always loads `token` relationship - be aware of N+1 queries with modules
4. **Token Refresh**: Errors often related to invalid credentials or expired refresh tokens - check logs
5. **Module Types**: Dashboard data structure varies by module type - check Netatmo API docs for field availability
