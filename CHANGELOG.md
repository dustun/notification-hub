# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added (Planned)
- User authentication (login) endpoint
- Logout functionality
- Email verification (send confirmation email + verification endpoint)

## [0.3.0] - 2026-04-21

### Added
- User registration (SignUp) feature
    - `POST /api/v1/auth/sign-up` endpoint
    - Request validation (name, email, password)
    - Password hashing with bcrypt
    - Sanctum token generation on successful registration
- Authentication infrastructure
    - `App\Auth` module with DDD-style structure (Application, Domain, Infrastructure, Http)
    - EloquentUser model with `HasFactory` trait
- PHPStan configuration improvements
    - Level 9 static analysis with strict type checking
    - Larastan integration for Laravel-specific rules
    - Fixed type errors in config files and response classes

### Changed
- PostgreSQL container optimized for better performance

### Fixed
- PHPStan errors in `config/session.php` (mixed type casting issues)
- PHPStan errors in `SignUpResponse::toArray()` (missing array value type specification)
- PHPStan errors in `DatabaseSeeder` (Eloquent factory method recognition via Larastan)

## [0.2.0] - 2026-04-11
### Added
- Filament v5 admin panel fully installed and configured
- `spatie/laravel-medialibrary` prepared for image handling
- PHP-CS-Fixer configuration with proper Laravel rules
- GitHub Actions workflow for automatic checks on `dev` and `master` branches

### Changed
- Caddy + PHP-FPM integration stabilized (fixed 502 Bad Gateway)
- Supervisor configuration reworked for reliable php-fpm startup in Alpine

### Fixed
- php-fpm privilege dropping errors (`Can't drop privilege as nonroot user`)
- Container startup crashes (`exit status 127`)
- Caddy not serving PHP requests correctly
- `task shell` command (switched to `sh` for Alpine)

## [0.1.0] - 2026-04-10
### Added
- Initial clean Docker setup (without Laravel Sail)
- Custom Dockerfile based on PHP 8.3-fpm-alpine
- Caddy as web server
- docker-compose.yml with PostgreSQL 16, Redis 7, RabbitMQ 4
- Taskfile.yml for development workflow
- Volume mount for instant code changes
- Basic configuration for PostgreSQL, Redis and RabbitMQ

### Changed
- Switched from Laravel Sail to fully custom Docker environment

[Unreleased]: https://github.com/Drukster/image-processor/compare/v0.3.0...HEAD
[0.3.0]: https://github.com/Drukster/image-processor/releases/tag/v0.3.0
[0.2.0]: https://github.com/Drukster/image-processor/releases/tag/v0.2.0
[0.1.0]: https://github.com/Drukster/image-processor/releases/tag/v0.1.0
