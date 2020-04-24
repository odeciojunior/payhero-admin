<?php

Route::group(['middleware' => ['web','auth', 'scopes:admin'], 'prefix' => 'vitrine', 'namespace' => 'Modules\Showcase\Http\Controllers'], function()
{
    Route::get('/',[
        'uses' => 'ShowcaseController@index',
        'as' => 'showcase'
    ]);
});


Route::group(['middleware' => ['auth:api', 'scopes:admin'], 'prefix' => 'api/vitrine', 'namespace' => 'Modules\Showcase\Http\Controllers'], function()
{
    Route::get('/', [
        'uses' => 'ShowcaseApiController@index',
    ]);

});
