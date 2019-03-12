<?php

Route::group(['middleware' => 'web', 'prefix' => 'notificacoes', 'namespace' => 'Modules\Notificacoes\Http\Controllers'], function()
{
    Route::get('/', 'NotificacoesController@index');

    Route::post('/markasread', 'NotificacoesController@markasread');
});

Route::group(['middleware' => 'auth:api', 'prefix' => 'api/notificacoes', 'namespace' => 'Modules\Usuario\Http\Controllers'], function(){

    Route::get('/', [
        'uses' => 'NotificacoesController@notificacoes',
    ]);

    Route::get('/qtdnotificacoes', [
        'uses' => 'NotificacoesController@qtdNotificacoes',
    ]);

});
