<?php


Route::group(
    [
        'middleware' => ['web', 'auth']
    ],
    function() {
        Route::apiResource('/project/{projectId}/pixels', 'PixelsApiController')
            ->only('index', 'store', 'update', 'destroy', 'show', 'edit');
    }
);
