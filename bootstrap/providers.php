<?php

use App\Shared\Infrastructure\Providers\AppServiceProvider;
use App\Shared\Infrastructure\Providers\ConsoleServiceProvider;
use App\Shared\Infrastructure\Providers\EventServiceProvider;
use App\Shared\Infrastructure\Providers\Filament\AdminPanelProvider;

return [
    ConsoleServiceProvider::class,
    AppServiceProvider::class,
    AdminPanelProvider::class,
    EventServiceProvider::class
];
