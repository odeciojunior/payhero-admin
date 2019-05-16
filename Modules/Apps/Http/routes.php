<?php

Route::group(['middleware' => 'web', 'prefix' => 'aplicativos', 'namespace' => 'Modules\Apps\Http\Controllers'], function()
{
    Route::get('/',[
        'uses' => 'AppsController@index',
        'as' => 'apps'
    ]);
});
