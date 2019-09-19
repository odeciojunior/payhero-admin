<?php


Route::group(
    [
        'middleware' => ['web', 'auth']
    ],
    function() {
        Route::apiResource('/apps/hotzapp', 'HotZappApiController')
            ->only('index', 'show', 'store', 'edit', 'update', 'destroy');
    }
);
