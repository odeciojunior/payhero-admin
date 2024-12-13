<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\VegaCheckout\Http\Controllers\VegaCheckoutController;

Route::prefix('apps')
    ->name('apps.')
    ->middleware(['web', 'auth', 'permission:apps'])
    ->group(function () {
        Route::resource('vegacheckout', VegaCheckoutController::class)
            ->only(['index'])
            ->names('vegacheckout');
    });
