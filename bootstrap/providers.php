<?php

use App\Shared\Infrastructure\Providers\AppServiceProvider;
use App\Shared\Infrastructure\Providers\EventServiceProvider;
use App\Shared\Infrastructure\Providers\Filament\AdminPanelProvider;

return [
    AppServiceProvider::class,
    AdminPanelProvider::class,
    EventServiceProvider::class
];
