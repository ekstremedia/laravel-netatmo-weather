# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

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
