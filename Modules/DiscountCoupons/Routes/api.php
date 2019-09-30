<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        'middleware' => ['auth:api'],
    ],
    function() {
        Route::apiResource('/project/{projectId}/couponsdiscounts', 'DiscountCouponsApiController')
            ->only('index', 'store', 'update', 'destroy', 'show', 'edit');
    }
);
