<?php

use Illuminate\Support\Facades\Route;

//Route::group(['middleware' => ['web', 'auth'], 'prefix' => 'dashboard'], function()
//{
//    Route::get('/',[
//        'uses' => 'DashboardController@index',
//        'as' => 'dashboard',
//    ]);
//
//    Route::post('/getvalues',[
//        'uses' => 'DashboardController@getValues',
//        'as' => 'dashboard.getvalues',
//    ]);
//
//});

Route::group(
    [
        'middleware' => ['web', 'auth']
    ],
    function() {
        Route::resource('/dashboard', 'DashboardController')->only('index');
    }
);
