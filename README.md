# Notification Hub

`Notification Hub` is a Laravel 12 learning project for building a real queue-driven application around image uploads, background processing, and notifications.

The project is intentionally built around infrastructure that is common in production systems:
- `RabbitMQ` for asynchronous jobs and queue-based workflows
- `PostgreSQL` for persistent business data
- `Redis` for cache and auxiliary fast storage
- `Filament` for the admin panel
- `Docker Compose` for a reproducible local environment

## Project Idea

This project is meant to demonstrate why queues matter in a real application instead of a toy CRUD example.

The expected business flow is:
1. A user uploads an image.
2. The app stores metadata in PostgreSQL.
3. A background job is published to RabbitMQ.
4. A worker processes the image without blocking the HTTP request.
5. The app stores the result and updates the processing status.
6. The user or admin can later inspect the result and task history.

That makes the project a practical sandbox for:
- background jobs
- queue prioritization
- retries and backoff
- failed jobs
- dead letter routing
- event-driven design
- admin visibility over async workflows

## Architecture Notes

The codebase follows a modular structure instead of keeping everything in a flat Laravel default layout.

Main modules:
- `app/Auth` - authentication use cases, requests, responses, domain objects, repository, jobs
- `app/Mail` - mail abstraction and Laravel mail implementation
- `app/Shared` - shared contracts, DTOs, services, providers, value objects

This keeps HTTP, business logic, infrastructure and domain concerns more clearly separated.

## Main Dependencies

Core framework and application packages:
- [Laravel 12](https://laravel.com/docs/12.x)
- [Laravel Sanctum](https://laravel.com/docs/12.x/sanctum)
- [Filament](https://filamentphp.com/docs)
- [RabbitMQ Laravel Queue Driver](https://github.com/vyuldashev/laravel-queue-rabbitmq)
- [RabbitMQ](https://www.rabbitmq.com/)
- [PostgreSQL](https://www.postgresql.org/)
- [Redis](https://redis.io/)
- [Intervention Image](https://image.intervention.io/)
- [Spatie Laravel Data](https://spatie.be/docs/laravel-data)
- [Spatie Laravel Media Library](https://spatie.be/docs/laravel-medialibrary)

Developer tooling:
- [Docker Compose](https://docs.docker.com/compose/)
- [Task](https://taskfile.dev/)
- [PHPStan](https://phpstan.org/)
- [Laravel Pint](https://laravel.com/docs/12.x/pint)
- [PHP CS Fixer](https://cs.symfony.com/)
- [GitHub Actions](https://docs.github.com/actions)

## Infrastructure Services

The local environment is described in [`docker-compose.yml`](/Users/imranpskhu/projects/image-processor/docker-compose.yml:1).

Services:
- `app` - PHP 8.3 FPM application container
- `caddy` - web server
- `pgsql` - PostgreSQL 16
- `redis` - Redis 7
- `rabbitmq` - RabbitMQ 4 with management UI

Useful local endpoints:
- application: [http://localhost](http://localhost)
- RabbitMQ management: [http://localhost:15672](http://localhost:15672)
- PostgreSQL: `localhost:5432`
- Redis: `localhost:6379`

RabbitMQ default credentials in local Docker:
- username: `guest`
- password: `guest`

PostgreSQL default credentials in local Docker:
- database: `image_processor`
- username: `root`
- password: `1234`

## Environment Setup

1. Copy the environment template:

```bash
cp .env.example .env
```

2. Review and adjust values if needed.

3. Start containers:

```bash
task up-build
```

4. Generate application key:

```bash
task artisan -- key:generate
```

5. Run migrations:

```bash
task artisan -- migrate
```

If you use plain Docker instead of `task`:

```bash
docker compose up -d --build
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate
```

## .env Variables

The project template already targets Docker service names and the queue-first baseline.

Important variables:

```env
APP_NAME="Notification Hub"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

APP_LOCALE=ru
APP_FALLBACK_LOCALE=en

DB_CONNECTION=pgsql
DB_HOST=pgsql
DB_PORT=5432
DB_DATABASE=image_processor
DB_USERNAME=root
DB_PASSWORD=1234

CACHE_STORE=redis
QUEUE_CONNECTION=rabbitmq

REDIS_HOST=redis
REDIS_PORT=6379

RABBITMQ_HOST=rabbitmq
RABBITMQ_PORT=5672
RABBITMQ_USER=guest
RABBITMQ_PASSWORD=guest
RABBITMQ_VHOST=/
RABBITMQ_QUEUE=default
RABBITMQ_WORKER=default
```

Mail settings for local development depend on your chosen provider.

For simple local logging:

```env
MAIL_MAILER=log
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"
```

If later you connect a real SMTP provider, update:
- `MAIL_MAILER`
- `MAIL_HOST`
- `MAIL_PORT`
- `MAIL_USERNAME`
- `MAIL_PASSWORD`
- `MAIL_ENCRYPTION`

## Working With The Project

The recommended workflow is based on [`Taskfile.yml`](/Users/imranpskhu/projects/image-processor/Taskfile.yml:1).

Available commands:

- `task up` - start containers without rebuild
- `task up-build` - rebuild and start containers
- `task rebuild` - full rebuild without cache, including volume reset
- `task down` - stop the project
- `task restart` - restart running containers
- `task logs` - watch app logs
- `task shell` - open a shell inside the app container
- `task artisan -- <command>` - run artisan commands
- `task php` - check PHP version and required extensions
- `task phpstan` - run PHPStan
- `task phpstan:level9` - run stricter PHPStan analysis

Examples:

```bash
task up
task artisan -- migrate
task artisan -- queue:work rabbitmq --queue=notifications
task artisan -- test
task phpstan
```

## Changelog

Project history is tracked in [CHANGELOG.md](/Users/imranpskhu/projects/image-processor/CHANGELOG.md:1).
