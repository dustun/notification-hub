<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\OpenApi;

use OpenApi\Attributes as OA;

#[OA\OpenApi(
    openapi: '3.0.0',
    security: [['sanctumBearer' => []]],
)]
#[OA\Info(
    version: '0.4.0',
    description: 'API документация проекта Notification Hub.',
    title: 'Image Processor API',
)]
#[OA\Server(
    url: 'http://localhost',
    description: 'Local development server',
)]
#[OA\SecurityScheme(
    securityScheme: 'sanctumBearer',
    type: 'http',
    description: 'Sanctum bearer token in the Authorization header.',
    bearerFormat: 'Token',
    scheme: 'bearer',
)]
#[OA\Tag(
    name: 'Auth',
    description: 'Authentication endpoints',
)]
final class OpenApiSpec {}
