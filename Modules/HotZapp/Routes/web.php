<?php


Route::group(
    [
        'middleware' => ['web', 'auth','permission:apps'],
        'as' => 'hotzapp.'
    ],
    function() {
        Route::resource('/apps/hotzapp', 'HotZappController')->only('index');
    }
);


Route::group(
    [
        'middleware' => ['web','permission:apps'],
        'prefix'     => 'apps/hotzapp',
    ],
    function() {
        Route::get('/newboleto/{boleto_id}', 'HotZappApiController@regenerateBoleto');
    }
);