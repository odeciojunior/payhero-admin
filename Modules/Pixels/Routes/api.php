<?php


Route::group(
    [
        'middleware' => ['web', 'auth']
    ],
    function() {
        Route::apiResource('/pixels', 'PixelsApiController')
            ->only('index', 'edit', 'create', 'index', 'show', 'update', 'destroy', 'store');
    }
);
