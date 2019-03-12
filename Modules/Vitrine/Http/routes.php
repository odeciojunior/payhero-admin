<?php

Route::group(['middleware' => ['web','auth'], 'prefix' => 'vitrine', 'namespace' => 'Modules\Vitrine\Http\Controllers'], function()
{
    Route::get('/',[
        'uses' => 'VitrineController@index',
        'as' => 'vitrine'
    ]);
});


Route::group(['middleware' => 'auth:api', 'prefix' => 'api/vitrine', 'namespace' => 'Modules\Vitrine\Http\Controllers'], function()
{
    Route::get('/', [
        'uses' => 'VitrineController@getVitrine',
    ]);

});
