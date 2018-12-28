<?php

Route::group(['middleware' => ['web','auth'], 'prefix' => 'convites', 'namespace' => 'Modules\Convites\Http\Controllers'], function()
{
    Route::get('/', [
        'uses' => 'ConvitesController@index',
        'as' => 'convites'
    ]);

    Route::post('/enviarconvite', [
        'uses' => 'ConvitesController@enviarConvite',
        'as' => 'convites.enviarconvite'
    ]);

});
