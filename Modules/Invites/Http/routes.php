<?php

Route::group(['middleware' => ['web','auth'], 'prefix' => 'convites', 'namespace' => 'Modules\Invites\Http\Controllers'], function()
{
    Route::get('/', [
        'uses' => 'InvitesController@index',
        'as' => 'invites'
    ]);

    Route::post('/enviarconvite', [
        'uses' => 'InvitesController@sendInvitation',
        'as' => 'invites.sendinvitation'
    ]);

});

Route::group(['prefix' => 'convites', 'namespace' => 'Modules\Invites\Http\Controllers'], function(){

    Route::post('/obterconvite', [
        'uses' => 'InvitesController@getInvitation'
    ]);
    
});

Route::group(['middleware' => 'auth:api', 'prefix' => 'api/convites', 'namespace' => 'Modules\Invites\Http\Controllers'], function()
{
    Route::get('/', [
        'uses' => 'InvitesApiController@convites',
    ]);

    Route::post('/', [
        'uses' => 'InvitesApiController@enviarConvite',
    ]);

});

