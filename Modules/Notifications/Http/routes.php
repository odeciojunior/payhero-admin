<?php

Route::group(['middleware' => 'web', 'prefix' => 'notificacoes', 'namespace' => 'Modules\Notifications\Http\Controllers'], function()
{
    Route::post('/markasread', 'NotificationsController@markasread');
});

Route::group(['middleware' => 'auth:api', 'prefix' => 'api/notificacoes', 'namespace' => 'Modules\Notifications\Http\Controllers'], function(){

    Route::get('/', [
        'uses' => 'NotificationsController@getUnreadNotifications',
    ]);

    Route::get('/qtdnotificacoes', [
        'uses' => 'NotificationsController@getUnreadNotificationsCount',
    ]);

});
