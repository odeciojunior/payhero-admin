<?php

Route::group(
    [
        'middleware' => ['web', 'auth']
    ],
    function() {
        Route::apiResource('/couponsdiscounts', 'DiscountCouponsApiController')
            ->only('index', 'store', 'update', 'destroy', 'show', 'edit');
    }
);
