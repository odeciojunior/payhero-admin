<?php

Route::group(['middleware' => ['web', 'auth', 'scopes:admin'], 'prefix' => 'layouts', 'namespace' => 'Modules\Layouts\Http\Controllers'], function()
{
    Route::post('/data-source',[
        'as' => 'layouts.index',
        'uses' => 'LayoutsController@index'
    ]);

    Route::post('/editarlayout', [
        'uses' => 'LayoutsController@update',
        'as' => 'layouts.update',
    ]);

    Route::post('/removerlayout', [
        'uses' => 'LayoutsController@deletar',
        'as' => 'layouts.deletar',
    ]);

    Route::post('/cadastrarlayout', [
        'uses' => 'LayoutsController@store',
        'as' => 'layouts.store',
    ]);

    Route::post('/detalhe',[
        'as' => 'usuario.details',
        'uses' => 'LayoutsController@details'
    ]);

    Route::post('/preview', [
        'as' => 'checkout.preview',
        'uses' => 'PreViewCheckoutController@checkoutPreView',
    ]);

    Route::post('/getformaddlayout', [
        'as' => 'checkout.create',
        'uses' => 'LayoutsController@create',
    ]);

    Route::post('/getformeditarlayout', [
        'as' => 'checkout.edit',
        'uses' => 'LayoutsController@edit',
    ]);

});
