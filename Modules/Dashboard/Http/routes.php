<?php

Route::group(['middleware' => ['web', 'auth'], 'prefix' => 'dashboard', 'namespace' => 'Modules\Dashboard\Http\Controllers'], function()
{
    Route::get('/',[
        'uses' => 'DashboardController@index',
        'as' => 'dashboard',
    ]);
});
