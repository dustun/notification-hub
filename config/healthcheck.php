<?php

declare(strict_types=1);

use App\Shared\Infrastructure\HealthChecks\SystemResourcesHealthCheck;
use UKFast\HealthCheck\Checks\CacheHealthCheck;
use UKFast\HealthCheck\Checks\DatabaseHealthCheck;
use UKFast\HealthCheck\Checks\EnvHealthCheck;
use UKFast\HealthCheck\Checks\MigrationUpToDateHealthCheck;
use UKFast\HealthCheck\Checks\RedisHealthCheck;
use UKFast\HealthCheck\Checks\StorageHealthCheck;

return [
    'base-path' => '',
    'route-paths' => [
        'health' => '/health',
        'ping' => '/ping',
    ],
    'checks' => [
        DatabaseHealthCheck::class,
        RedisHealthCheck::class,
        CacheHealthCheck::class,
        StorageHealthCheck::class,
        MigrationUpToDateHealthCheck::class,
        EnvHealthCheck::class,
        SystemResourcesHealthCheck::class,
    ],
    'middleware' => [],
    'auth' => [
        'user' => env('HEALTH_CHECK_USER'),
        'password' => env('HEALTH_CHECK_PASSWORD'),
    ],
    'route-name' => 'healthcheck',
    'database' => [
        'connections' => ['default'],
    ],
    'required-env' => [
        'APP_KEY',
        'DB_CONNECTION',
        'QUEUE_CONNECTION',
        'REDIS_HOST',
        'MAIL_MAILER',
    ],
    'addresses' => [],
    'default-response-code' => 200,
    'default-problem-http-code' => 500,
    'default-curl-timeout' => 2.0,
    'x-service-checks' => [],
    'cache' => [
        'stores' => ['array', 'redis'],
    ],
    'storage' => [
        'disks' => ['local', 'public'],
    ],
    'package-security' => [
        'exclude-dev' => false,
        'ignore' => [],
    ],
    'scheduler' => [
        'cache-key' => 'laravel-scheduler-health-check',
        'minutes-between-checks' => 5,
    ],
    'env-check-key' => 'HEALTH_CHECK_ENV_DEFAULT_VALUE',
];
