<?php

Route::group(['middleware' => ['web','auth'], 'prefix' => 'vitrine', 'namespace' => 'Modules\Showcase\Http\Controllers'], function()
{
    Route::get('/',[
        'uses' => 'ShowcaseController@index',
        'as' => 'showcase'
    ]);
});


Route::group(['middleware' => 'auth:api', 'prefix' => 'api/vitrine', 'namespace' => 'Modules\Showcase\Http\Controllers'], function()
{
    Route::get('/', [
        'uses' => 'ShowcaseApiController@index',
    ]);

});
