<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\AdooreiCheckout\Http\Controllers\AdooreiCheckoutController;

Route::prefix('apps')
    ->name('apps.')
    ->middleware(['web', 'auth', 'permission:apps'])
    ->group(function () {
        Route::get("adooreicheckout", [AdooreiCheckoutController::class, 'index'])
            ->name('adooreicheckout.index');
    });
