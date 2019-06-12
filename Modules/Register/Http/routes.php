<?php

Route::group(['middleware' => 'web', 'prefix' => 'register', 'namespace' => 'Modules\Register\Http\Controllers'], function()
{

    Route::get('/{parametro}', 'RegisterController@create');

    Route::post('/', 'RegisterController@store');

    Route::get('/', 'RegisterController@index');

});

/*
Route::group([ 'prefix' => 'api/user', 'namespace' => 'Modules\Usuario\Http\Controllers'], function(){

    Route::post('/', [
        'uses' => 'UsuarioApiController@novoUsuario',
    ]);

});

*/
