<?php

declare(strict_types=1);

use App\Auth\Http\SignUp\SignUpController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('sign-up', SignUpController::class);
    });
});
