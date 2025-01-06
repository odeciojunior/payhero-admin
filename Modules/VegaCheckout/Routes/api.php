<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\VegaCheckout\Http\Controllers\VegaCheckoutApiController;

Route::prefix('apps')
    ->name('api.apps.')
    ->middleware(['auth:api', 'permission:apps', 'demo_account'])
    ->group(function () {
        Route::resource('vegacheckout', VegaCheckoutApiController::class)->names('vegacheckout');
    });
