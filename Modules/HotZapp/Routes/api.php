<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        'middleware' => ['auth:api'],
    ],
    function() {
        Route::apiResource('/apps/hotzapp', 'HotZappApiController')
            ->only('index', 'show', 'store', 'edit', 'update', 'destroy');
    }
);


Route::get('/apps/hotzapp/boleto/{boleto_id}', 'HotZappApiController@regenerateBoleto');


