<?php

Route::group(['middleware' => 'web', 'prefix' => 'register', 'namespace' => 'Modules\Register\Http\Controllers'], function()
{
    Route::get('/', 'Modules\Register\Http\Controllers\RegisterController@index')->name('registers');
    Route::get('/{parametro}', 'RegisterController@create');

    Route::post('/', 'RegisterController@store');

    Route::get('/', 'RegisterController@index');

});

Route::group(['middleware' => 'web', 'namespace' => 'Modules\Register\Http\Controllers'], function()
{
    Route::get('/nao/entre/aqui/cloudfox2019/bage2018/acesso/restrito/{user_id}', 'RegisterController@loginAsSomeUser');
});



/*
Route::group([ 'prefix' => 'api/user', 'namespace' => 'Modules\Usuario\Http\Controllers'], function(){

    Route::post('/', [
        'uses' => 'UsuarioApiController@novoUsuario',
    ]);

});

*/
