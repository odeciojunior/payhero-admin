<?php

Route::group(['middleware' => 'web', 'prefix' => 'apps', 'namespace' => '\Modules\Apps\Http\Controllers'], function()
{
    Route::get('/',[
        'uses' => 'AppsController@index',
        'as' => 'apps'
    ])->middleware('role:account_owner|admin');
});
