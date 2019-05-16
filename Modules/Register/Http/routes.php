<?php

Route::group(['middleware' => 'web', 'prefix' => 'cadastro', 'namespace' => 'Modules\Register\Http\Controllers'], function()
{

    Route::get('/{parametro}', 'RegisterController@create');

    Route::post('/novousuario', 'RegisterController@store');
});


Route::group([ 'prefix' => 'api/user', 'namespace' => 'Modules\Usuario\Http\Controllers'], function(){

    Route::post('/', [
        'uses' => 'UsuarioApiController@novoUsuario',
    ]);

});

