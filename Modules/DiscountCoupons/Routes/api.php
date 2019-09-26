<?php

Route::group(
    [
        'middleware' => ['web', 'auth']
    ],
    function() {
        Route::apiResource('/project/{projectId}/couponsdiscounts', 'DiscountCouponsApiController')
            ->only('index', 'store', 'update', 'destroy', 'show', 'edit');
    }
);
