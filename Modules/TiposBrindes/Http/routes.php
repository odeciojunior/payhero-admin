<?php

Route::group(['middleware' => ['web', 'auth'], 'prefix' => 'tiposbrindes', 'namespace' => 'Modules\TiposBrindes\Http\Controllers'], function()
{
    Route::get('/', [
        'uses' => 'TiposBrindesController@index',
        'as' => 'tiposbrindes',
    ]);

    Route::get('/cadastro', [
        'uses' => 'TiposBrindesController@cadastro',
        'as' => 'tiposbrindes.cadastro',
    ]);

    Route::get('/editar/{id}', [
        'uses' => 'TiposBrindesController@editarTipoBrinde',
        'as' => 'tiposbrindes.editar',
    ]);

    Route::post('/editartipobrinde', [
        'uses' => 'TiposBrindesController@updateTipoBrinde',
        'as' => 'tiposbrindes.update',
    ]);

    Route::get('/deletartipobrinde/{id}', [
        'uses' => 'TiposBrindesController@deletarTipoBrinde',
        'as' => 'tiposbrindes.deletar',
    ]);

    Route::post('/cadastrartipobrinde', [
        'uses' => 'TiposBrindesController@cadastrarTipoBrinde',
        'as' => 'tiposbrindes.cadastrartipobrinde',
    ]);

    Route::post('/data-source',[
        'as' => 'tiposbrindes.dadostipobrindes',
        'uses' => 'TiposBrindesController@dadosTiposBrindes'
    ]);

});

