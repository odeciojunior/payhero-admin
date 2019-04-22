<?php

Route::group(['middleware' => ['web', 'auth'], 'prefix' => 'empresas', 'namespace' => 'Modules\Empresas\Http\Controllers'], function()
{
    Route::get('/', [
        'uses' => 'EmpresasController@index',
        'as' => 'empresas',
    ]);

    Route::get('/cadastro', [
        'uses' => 'EmpresasController@cadastro',
        'as' => 'empresas.cadastro',
    ]);

    Route::get('/editar/{id}', [
        'uses' => 'EmpresasController@editarEmpresa',
        'as' => 'empresas.editar',
    ]);

    Route::post('/editarempresa', [
        'uses' => 'EmpresasController@updateEmpresa',
        'as' => 'empresas.update',
    ]);

    Route::get('/deletarempresa/{id}', [
        'uses' => 'EmpresasController@deletarEmpresa',
        'as' => 'empresas.deletar',
    ]);

    Route::post('/cadastrarempresa', [
        'uses' => 'EmpresasController@cadastrarEmpresa',
        'as' => 'empresas.cadastrarempresa',
    ]);

    Route::post('/data-source',[
        'as' => 'empresas.dadosempresas',
        'uses' => 'EmpresasController@dadosEmpresas'
    ]);

    Route::post('/detalhe',[
        'as' => 'usuario.detalhes',
        'uses' => 'EmpresasController@getDetalhesEmpresa'
    ]);

});


Route::group(['middleware' => 'auth:api', 'prefix' => 'api/empresas', 'namespace' => 'Modules\Empresas\Http\Controllers'], function(){

    Route::get('/', [
        'uses' => 'EmpresasApiController@index',
    ]);

    Route::post('/', [
        'uses' => 'EmpresasApiController@create',
    ]);

    Route::put('/', [
        'uses' => 'EmpresasApiController@update',
    ]);

    Route::delete('/{id}', [
        'uses' => 'EmpresasApiController@delete',
    ]);
    
    Route::get('/{id}', [
        'uses' => 'EmpresasApiController@show',
    ]);

    Route::get('/getbancos', [
        'uses' => 'EmpresasApiController@getBancos',
    ]);
    
});
