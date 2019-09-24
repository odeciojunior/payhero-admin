<?php

Route::group(
    [
        'middleware' => ['web', 'auth']
    ],
    function() {
        Route::apiResource('/shippings', 'ShippingApiController')
            ->only('index', 'store', 'update', 'destroy', 'show', 'edit');
    }
);
