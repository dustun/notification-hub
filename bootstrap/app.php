<?php

use App\Shared\Domain\Enums\SystemLogLevel;
use App\Shared\Infrastructure\Middleware\LogRequestActivity;
use App\Shared\Infrastructure\Services\SystemLogManager;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Auth;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->append(LogRequestActivity::class);
    })
    ->withExceptions(
        function (Exceptions $exceptions): void {
            $exceptions
                ->report(function (Throwable $throwable): void {
                    /** @var SystemLogManager $logger */
                    $logger = app(SystemLogManager::class);
                    $request = app()->bound('request') ? request() : null;
                    $route = $request?->route();
                    $routeName = $route?->getName();
                    $userId = Auth::id();

                    $logger->log(
                        level: SystemLogLevel::ERROR,
                        category: 'exception',
                        message: $throwable->getMessage() !== '' ? $throwable->getMessage() : 'В системе произошло необработанное исключение.',
                        action: class_basename($throwable::class),
                        context: [
                            'exception' => $throwable::class,
                            'file' => $throwable->getFile(),
                            'line' => $throwable->getLine(),
                        ],
                        routeName: is_string($routeName) ? $routeName : null,
                        method: $request?->method(),
                        path: $request !== null ? ('/' . trim($request->path(), '/')) : null,
                        ipAddress: $request?->ip(),
                        userAgent: $request?->userAgent(),
                        userId: is_string($userId) || is_int($userId) ? (string)$userId : null,
                    );
                });
        }
    )
    ->create();
