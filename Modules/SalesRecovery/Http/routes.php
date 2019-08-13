<?php

Route::group(['middleware' => ['web', 'auth'], 'prefix' => 'recovery', 'namespace' => 'Modules\SalesRecovery\Http\Controllers'], function() {
    Route::get('/', [
        'uses' => 'SalesRecoveryController@index',
        'as'   => 'recovery',
    ]);

    Route::get('/getrecoverydata', [
        'uses' => 'SalesRecoveryController@getRecoveryData',
    ]);

    Route::post('/details', [
        'uses' => 'SalesRecoveryController@getDetails',
    ]);

});


