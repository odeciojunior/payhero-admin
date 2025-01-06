<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\AdooreiCheckout\Http\Controllers\AdooreiCheckoutApiController;

Route::prefix('apps')
    ->name('api.apps')
    ->middleware(['auth:api', 'permission:apps_manage', 'demo_account'])
    ->group(function () {
        Route::apiResource('adooreicheckout', AdooreiCheckoutApiController::class)
            ->only('index', 'show', 'edit', 'create', 'store', 'update', 'destroy')
            ->middleware('permission:apps_manage')
            ->names('adooreicheckout');
    });
