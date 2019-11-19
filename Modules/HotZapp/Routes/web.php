<?php


Route::group(
    [
        'middleware' => ['web', 'auth'],
        'as' => 'hotzapp.'
    ],
    function() {
        Route::resource('/apps/hotzapp', 'HotZappController')->only('index');
    }
);


Route::group(
    [
        'middleware' => ['web'],
        'prefix'     => 'apps/hotzapp', 
    ],
    function() {
        Route::get('/boleto/{boleto_id}', 'HotZappApiController@regenerateBoleto');
    }
);



