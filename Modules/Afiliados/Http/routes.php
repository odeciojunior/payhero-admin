<?php

Route::group(['middleware' => ['web','auth'], 'prefix' => 'afiliados', 'namespace' => 'Modules\Afiliados\Http\Controllers'], function()
{
    Route::get('/afiliar/{id_projeto}', 'AfiliadosController@afiliar');

    Route::get('/meusafiliados', [
        'uses' => 'AfiliadosController@meusAfiliados',
        'as'   => 'afiliados.meusafiliados'
    ]);

    Route::get('/minhasafiliacoes', [
        'uses' => 'AfiliadosController@minhasAfiliacoes',
        'as'   => 'afiliados.minhasafiliacoes'
    ]);

    Route::get('/getafiliadosprojeto/{id_projeto}', [
        'uses' => 'AfiliadosController@getAfiliadosProjeto',
        'as'   => 'afiliados.getafiliadosprojeto'
    ]);

    Route::get('/getdetalhesafiliacao/{id_projeto}', [
        'uses' => 'AfiliadosController@getDetalhesAfiliacao',
        'as'   => 'afiliados.getdetalhesafiliacao'
    ]);

    Route::post('/meusafiliados/data-source','AfiliadosController@dadosMeusAfiliados');

    Route::post('/minhasafiliacoes/data-source','AfiliadosController@dadosMinhasAfiliacoes');

    Route::post('/setempresa','AfiliadosController@setEmpresaAfiliacao');

});


Route::group(['middleware' => 'web', 'prefix' => 'cfredirect', 'namespace' => 'Modules\Afiliados\Http\Controllers'], function()
{
    Route::get('/{parametro}', 'CookieController@setCookie');

});


