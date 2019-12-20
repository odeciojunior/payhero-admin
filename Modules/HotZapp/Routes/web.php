<?php


Route::group(
    [
        'middleware' => ['web', 'auth', 'setUserAsLogged'],
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
        Route::get('/newboleto/{boleto_id}', 'HotZappApiController@regenerateBoleto');
    }
);



