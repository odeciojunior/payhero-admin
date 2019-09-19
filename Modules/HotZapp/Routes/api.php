<?php


Route::group(
    [
        'middleware' => ['web', 'auth']
    ],
    function() {
        Route::apiResource('/apps/hotzapp', 'HotZappApiController')
            ->only('index', 'store', 'edit', 'update', 'destroy');

        Route::get('/getintegrations', 'HotZappApiController@getIntegrations');
    }
);
