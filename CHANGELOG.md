# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added
- Initial release
- OAuth2 authentication with Netatmo API
- Automatic token refresh mechanism
- Weather station data fetching and storage
- Support for multiple weather stations per user
- Database models and migrations for stations, modules, and tokens
- Encrypted storage of sensitive credentials (client_id, client_secret)
- Configurable data caching (10 minutes default)
- Full UI scaffolding with Blade templates
- Support for all Netatmo module types (NAMain, NAModule1-4)
- Comprehensive documentation and README

### Changed
- Removed debug statements (ray calls) for production readiness
- Made User model configurable via config file
- Added proper Laravel package dependencies

### Fixed
- Fixed hardcoded User model references to use configuration

## [1.0.0] - TBD

First stable release
