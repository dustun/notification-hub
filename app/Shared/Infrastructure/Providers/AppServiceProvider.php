<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Providers;

use App\Auth\Domain\Contracts\UserRepositoryInterface;
use App\Auth\Infrastructure\Repositories\EloquentUserRepository;
use App\Mail\Application\Contracts\MailSender;
use App\Mail\Infrastructure\Services\LaravelMailSender;
use App\Media\Infrastructure\Models\EloquentMedia;
use App\Media\Infrastructure\Support\MediaPathGenerator;
use App\Shared\Domain\Contracts\HasherInterface;
use App\Shared\Domain\Contracts\TokenCreatorInterface;
use App\Shared\Infrastructure\Services\HasherService;
use App\Shared\Infrastructure\Services\SanctumTokenCreatorService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            UserRepositoryInterface::class,
            EloquentUserRepository::class
        );

        $this->app->bind(
            HasherInterface::class,
            HasherService::class
        );

        $this->app->bind(
            TokenCreatorInterface::class,
            SanctumTokenCreatorService::class
        );

        $this->app->bind(
            MailSender::class,
            LaravelMailSender::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $storageDisk = config('filesystems.disks', 'public');
        $storageDisk = is_string($storageDisk) ? $storageDisk : 'public';

        config()->set('media-library.media_model', EloquentMedia::class);
        config()->set('media-library.path_generator', MediaPathGenerator::class);
        config()->set('media-library.disk_name', $storageDisk);
    }
}
