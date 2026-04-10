# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [0.1.0] - 2026-04-10
### Added
- Initial project structure with clean Docker setup
- Custom multi-stage Dockerfile based on PHP 8.3-fpm-alpine
- Caddy as web server (instead of Nginx/Apache)
- Full docker-compose.yml with PostgreSQL 16, Redis 7, RabbitMQ 4 (with management UI)
- Taskfile.yml for convenient development commands 
- Self-written Docker environment where code changes apply instantly via volume mount
- Basic configuration for RabbitMQ, PostgreSQL and Redis
- Supervisor ready for future queue workers

[Unreleased]: https://github.com/Drukster/image-processor/compare/v0.1.0...HEAD
[0.1.0]: https://github.com/Drukster/image-processor/releases/tag/v0.1.0
