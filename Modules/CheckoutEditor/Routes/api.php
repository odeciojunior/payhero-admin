<?php

use Illuminate\Support\Facades\Route;

Route::prefix('checkouteditor')->group(function() {
    Route::apiResource('checkouteditor', 'CheckoutEditorController')->only('show', 'update');
});
