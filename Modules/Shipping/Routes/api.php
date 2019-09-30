<?php

Route::group(
    [
        'middleware' => ['web', 'auth']
    ],
    function() {
        Route::apiResource('/project/{projectId}/shippings', 'ShippingApiController')
            ->only('index', 'store', 'update', 'destroy', 'show', 'edit');
    }
);
