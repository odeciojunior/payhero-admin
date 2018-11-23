<?php

Route::group(['middleware' => ['web', 'auth'], 'prefix' => 'layouts', 'namespace' => 'Modules\Layouts\Http\Controllers'], function()
{
    Route::get('/', [
        'uses' => 'LayoutsController@index',
        'as' => 'layouts',
    ]);

    Route::get('/cadastro', [
        'uses' => 'LayoutsController@cadastro',
        'as' => 'layouts.cadastro',
    ]);

    Route::get('/editar/{id}', [
        'uses' => 'LayoutsController@editarLayout',
        'as' => 'layouts.editar',
    ]);

    Route::post('/editarlayout', [
        'uses' => 'LayoutsController@updateLayout',
        'as' => 'layouts.update',
    ]);

    Route::get('/deletarlayout/{id}', [
        'uses' => 'LayoutsController@deletarLayout',
        'as' => 'layouts.deletar',
    ]);

    Route::post('/cadastrarlayout', [
        'uses' => 'LayoutsController@cadastrarLayout',
        'as' => 'layouts.cadastrarlayout',
    ]);

    Route::post('/data-source',[
        'as' => 'layouts.dadoslayouts',
        'uses' => 'LayoutsController@dadosLayout'
    ]);

    Route::post('/detalhe',[
        'as' => 'usuario.detalhes',
        'uses' => 'LayoutsController@getDetalhesLayout'
    ]);

    Route::post('/preview', [
        'as' => 'checkout.preview',
        'uses' => 'PreViewCheckoutController@checkoutPreView',
    ]);

    Route::post('/getformaddlayout', [
        'as' => 'checkout.getformaddlayout',
        'uses' => 'LayoutsController@getFormAddLayout',
    ]);

    Route::post('/getformeditarlayout', [
        'as' => 'checkout.getformeditarlayout',
        'uses' => 'LayoutsController@getFormEditarLayout',
    ]);

});
