# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added
- Public sharing feature with per-station access control
- `is_public` boolean field to NetatmoStation model
- Public route `/netatmo/public/{uuid}` for unauthenticated access
- Toggle switch UI for enabling/disabling public access
- Dedicated public layout without admin controls
- Copy-to-clipboard functionality for public URLs
- Database migration for `is_public` column
- Device selection feature for accounts with multiple Netatmo weather stations
- `device_id` field to NetatmoStation model for device identification
- Device selection UI showing station name, module count, and device ID
- Routes for manual device selection and reassignment (`/netatmo/{station}/select-device`, `/netatmo/{station}/set-device`)
- `getAvailableDevices()` method in NetatmoService to fetch all devices from API
- Database migration for `device_id` column
- Module lifecycle management with `is_active` status tracking
- Automatic archiving of modules no longer detected by Netatmo API
- Archived Modules section in station view showing inactive modules
- Delete functionality for archived/inactive modules
- "Change Device" button on station detail page
- Database migration for `is_active` column on netatmo_modules table

### Changed
- Enhanced widget spacing and sizing for better readability
- Improved background gradient to prevent stretching on long pages
- Fixed SVG icon colors by removing CSS filter classes
- Updated policy to use loose comparison for user ID checks
- All views now filter to show only active modules by default
- Module counts now reflect only active modules
- Service automatically marks modules not in API response as inactive during data sync

### Fixed
- Fixed duplicate module constraint when adding multiple stations with shared modules
- Changed module_id unique constraint to composite (station_id + module_id) to allow same modules across different stations
- Fixed data mixing between multiple stations by implementing device_id matching
- API now correctly identifies and stores data for the correct physical device when multiple stations share the same Netatmo account
- Service now properly matches devices by device_id instead of always selecting the first device from API response
- Added automatic redirect to device selection page when device_id is not set and multiple devices exist
- Missing `use Illuminate\Http\Request;` import in NetatmoStationController

## [1.0.0] - 2025-11-07

First stable release with comprehensive security fixes, testing, and code quality improvements.

### Added
- OAuth2 authentication with Netatmo API
- Automatic token refresh mechanism via dedicated TokenRefreshService
- Weather station data fetching and storage
- Support for multiple weather stations per user
- Database models and migrations (stations, modules, tokens, module readings)
- Encrypted storage of sensitive credentials (client_id, client_secret, access_token, refresh_token)
- Configurable data caching (default 10 minutes, configurable via config)
- Full UI scaffolding with Blade templates
- Support for all Netatmo module types (NAMain, NAModule1-4)
- Comprehensive test suite (67 tests, 146 assertions)
- Custom exception classes (TokenRefreshException, InvalidApiResponseException)
- Authorization policies (NetatmoStationPolicy) for secure resource access
- Codecov integration for automated coverage tracking
- Database transactions for data integrity
- Comprehensive documentation and README

### Security
- Fixed OAuth CSRF vulnerability with proper state token validation
- Added encryption for access_token and refresh_token in database
- Fixed mass assignment vulnerability in NetatmoModule model
- Added authorization checks to all controller methods
- Added input validation to OAuth callback
- Implemented policy-based authorization for all station operations

### Changed
- Refactored business logic from models to dedicated service classes
- Eliminated 50+ lines of duplicate code in NetatmoService
- Made User model configurable via config file
- Made cache duration configurable via environment variable
- Enhanced error handling with custom exceptions
- Improved code organization following SOLID principles
- Removed all debug statements and commented code for production readiness

### Fixed
- Fixed N+1 query problems with eager loading
- Fixed inconsistent return formats in NetatmoService
- Fixed missing return type hints throughout codebase
- Fixed duplicate relationships in models
- Fixed OAuth state validation (was using csrf_token incorrectly)
- Enhanced Encryptable trait with null safety and error handling
