<?php


Route::group(
    [
        'middleware' => ['web', 'auth']
    ],
    function() {
        Route::apiResource('/pixels', 'PixelsApiController')
            ->only('index', 'store', 'update', 'destroy', 'show', 'edit');
    }
);
