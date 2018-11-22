<?php

Route::group(['middleware' => 'web', 'prefix' => 'brindes', 'namespace' => 'Modules\Brindes\Http\Controllers'], function()
{

    Route::get('/', [
        'uses' => 'BrindesController@index',
        'as' => 'brindes',
    ]);

    Route::get('/cadastro', [
        'uses' => 'BrindesController@cadastro',
        'as' => 'brindes.cadastro',
    ]);

    Route::get('/editar/{id}', [
        'uses' => 'BrindesController@editarBrinde',
        'as' => 'brindes.editar',
    ]);

    Route::post('/editarbrinde', [
        'uses' => 'BrindesController@updateBrinde',
        'as' => 'brindes.update',
    ]);

    Route::post('/deletarbrinde', [
        'uses' => 'BrindesController@deletarBrinde',
        'as' => 'brindes.deletar',
    ]);

    Route::post('/cadastrarbrinde', [
        'uses' => 'BrindesController@cadastrarBrinde',
        'as' => 'brindes.cadastrarbrinde',
    ]);

    Route::post('/data-source',[
        'as' => 'brindes.dadosbrindes',
        'uses' => 'BrindesController@dadosBrindes'
    ]);

    Route::post('/detalhe',[
        'as' => 'brindes.detalhes',
        'uses' => 'BrindesController@getDetalhesBrinde'
    ]);

    Route::get('/getformaddbrinde',[
        'as' => 'brindes.getformaddbrindes',
        'uses' => 'BrindesController@getFormAddBrinde'
    ]);

    Route::post('/getformeditarbrinde',[
        'as' => 'brindes.getformaddbrindes',
        'uses' => 'BrindesController@getFormEditarBrinde'
    ]);

});

