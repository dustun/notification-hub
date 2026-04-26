# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added (Planned)
- Image processing domain
- Task management for background jobs
- Filament resources for future bounded contexts

## [0.4.1] - 2026-04-26

### Added
- Centralized system logging for application activity and failures
  - HTTP request activity logging middleware
  - exception logging through application bootstrap configuration
  - dedicated `system_logs` model and logging service
- Filament resources for operational monitoring
  - full system action log resource
  - separate error log resource for warnings, errors, and critical issues
  - detailed log pages with HTTP context, user context, and structured payload data
- Advanced filtering for system logs in the admin panel
  - level
  - category
  - HTTP method
  - status code
  - date range
- Feature tests for request logging and unhandled exception logging

### Changed
- Improved health widgets in the admin panel with clearer Russian labels and more readable system resource output
- Added application-level media events into the shared operational logging flow

### Fixed
- Improved operational visibility by surfacing structured errors directly in the admin panel

---

## [0.4.0] - 2026-04-26

### Added
- Unified media storage foundation based on `spatie/laravel-medialibrary`
  - `MediaAsset` registry model for application-level media records
  - custom `EloquentMedia` model for media-library integration
  - media type detection for images, video, audio, PDF, documents, spreadsheets, presentations, archives, and generic files
- Modular Filament admin resource for media management
  - media list, create, edit, and view pages
  - media table, form, and infolist inside the `Media` module
  - shared form field wrappers for file upload, text inputs, and rich editor components
- Media storage path generator with directory separation by file type
- Media feature tests for unified asset creation and type validation

### Changed
- Converted media type storage to numeric enum values instead of strings
- Moved media UI texts to direct Russian labels in the resource layer
- Added media form autofill from uploaded file metadata
  - original file name
  - extension
  - MIME type
  - suggested logical name
  - detected media type
  - readable file size

### Fixed
- Fixed false client-side file type rejection during media upload by validating selected type on the server side
- Fixed recursive memory issue in the media form placeholder that could break opening the create page
- Fixed default disk resolution in the media form to use configured storage settings instead of hardcoded fallback

---

## [0.3.4] - 2026-04-24

### Added
- Repository Git hooks for `pre-commit` and `pre-push`
  - both hooks now run the local quality gate before changes can be committed or pushed
- `task test` command for Laravel tests inside the application container
- `task check` command as a single local quality gate entry point
- `task pre-push` and `task ci` aliases for clearer local verification workflows
- `task hooks:install` and `task hooks:uninstall` commands to manage the repository hook path
- Dependabot configuration for:
  - Composer dependencies
  - npm dependencies
  - GitHub Actions updates

### Changed
- Expanded GitHub Actions quality gate to include Laravel tests
- Raised CI static analysis to PHPStan level 9
- Updated project documentation to explain:
  - local hook-based guardrails
  - the full quality gate workflow
  - Dependabot behavior and setup

### Fixed
- Aligned local checks and CI checks around the same validation flow

---

## [0.3.3] - 2026-04-24

### Added
- OpenAPI documentation for all auth HTTP endpoints using PHP attributes
  - sign up
  - sign in
  - verify email
  - logout
- Base OpenAPI specification with API info, server, auth tag, and Sanctum bearer security scheme
- Swagger generation feature test to verify docs build and contain expected auth routes

### Changed
- Refactored logout endpoint to fully match the project use case flow
  - request
  - response
  - input
  - output
  - handler
- Renamed CLI admin command to match the `<Domain><CLI><Action>` naming convention
- Kept console command registration in the shared infrastructure provider

### Fixed
- Verified Swagger generation works through `l5-swagger:generate`
- Verified formatting, tests, and static analysis after the refactor

---

## [0.3.2] - 2026-04-24

### Added
- Authentication test baseline
  - feature tests for sign up, sign in, logout, and email verification
  - unit test for repository mapping of `email_verified_at`
- RabbitMQ-first queue baseline in configuration
  - `rabbitmq` connection added to `config/queue.php`
  - failover queue flow updated to start from RabbitMQ
- Docker-oriented environment template
  - `.env.example` now points to `pgsql`, `redis` and `rabbitmq` service names
  - default app metadata updated for the project context
- Filament user management inside the `Auth` module
  - modular `UserResource` with forms, table, infolist, and pages
  - configured navigation icon, group, badge, and admin UX polish
- Project CLI admin creation command with domain-oriented naming
  - custom command based on `EloquentUser`
  - command registration through shared console provider

### Changed
- Switched default infrastructure baseline from `sqlite + database queue` to `pgsql + redis + rabbitmq`
- Refactored authentication handlers to depend on contracts instead of concrete infrastructure services
- Wrapped sign-up persistence flow in transaction and moved event dispatch to `afterCommit`
- Verification email job now uses explicit queue settings, retries, timeout and backoff
- README was replaced with project-specific setup and usage documentation
- Filament resource discovery now supports module-oriented placement under `app/*`
- Logout endpoint now follows the same request/response/input/output/handler flow as the rest of the auth module

### Fixed
- Fixed missing `email_verified_at` mapping from Eloquent model to domain entity
- Restored `MailSender` contract binding in the application container
- Fixed sign-in request validation to avoid incorrect password rule usage
- Fixed mismatch between verification URL expiration and email template text
- Fixed `public` filesystem disk URL configuration
- Removed legacy sqlite-oriented bootstrap step from Composer project creation flow
- Fixed PHPUnit baseline by adding real auth coverage and valid `tests/Unit` structure

---

## [0.3.1] - 2026-04-23

### Added
- User authentication (SignIn)
    - `POST /api/v1/auth/sign-in` endpoint
    - Password verification using HasherService
    - Sanctum token generation via TokenCreator service
- Email verification system (stateless)
    - Temporary signed URL verification
    - `GET /api/v1/auth/verify-email` endpoint
    - Email confirmation via signed route
- Queue-based email delivery
    - `SendEmailVerificationJob` for async email sending

### Changed
- Refactored authentication flow to align with DDD principles
- Replaced token-based email verification with signed URLs (removed DB dependency)
- Simplified queue architecture (eliminated double queue issue)
- Improved MailSender abstraction and typing
- Updated UserRepository to support email verification updates

### Fixed
- Fixed double queue execution (listener + mailable conflict)
- Fixed URL generation issues (port leakage in verification links)
- Fixed PHPStan errors across Mail and Auth modules (strict typing, generics)
- Remove outdated ignore patterns from phpstan.neon
- Add ext-pdo dependency to composer.json (required by PHPStan for PDO::MYSQL_ATTR_SSL_CA usage)
- Remove incorrect 'use PDO;' statement from database.php (PDO is built-in class, not a namespace)
- Use PDO::MYSQL_ATTR_SSL_CA constant directly instead of version-dependent check
- All PHPStan checks now pass without errors

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

[Unreleased]: https://github.com/Drukster/image-processor/compare/v0.4.1...HEAD
[0.4.1]: https://github.com/Drukster/image-processor/compare/v0.4.0...v0.4.1
[0.4.0]: https://github.com/Drukster/image-processor/compare/v0.3.4...v0.4.0
[0.3.4]: https://github.com/Drukster/image-processor/compare/v0.3.3...v0.3.4
[0.3.3]: https://github.com/Drukster/image-processor/compare/v0.3.2...v0.3.3
[0.3.2]: https://github.com/Drukster/image-processor/compare/v0.3.1...v0.3.2
[0.3.1]: https://github.com/Drukster/image-processor/compare/v0.3.0...v0.3.1
[0.3.0]: https://github.com/Drukster/image-processor/releases/tag/v0.3.0
[0.2.0]: https://github.com/Drukster/image-processor/releases/tag/v0.2.0
[0.1.0]: https://github.com/Drukster/image-processor/releases/tag/v0.1.0
