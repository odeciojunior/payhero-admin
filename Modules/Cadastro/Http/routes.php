<?php

Route::group(['middleware' => 'web', 'prefix' => 'cadastro', 'namespace' => 'Modules\Cadastro\Http\Controllers'], function()
{
//    Route::get('/', 'CadastroController@cadastro');

    Route::get('/{parametro}', 'CadastroController@cadastro');

    Route::post('/novousuario', 'CadastroController@novoUsuario');
});


Route::group([ 'prefix' => 'api/user', 'namespace' => 'Modules\Usuario\Http\Controllers'], function(){

    Route::post('/', [
        'uses' => 'UsuarioApiController@novoUsuario',
    ]);

});

