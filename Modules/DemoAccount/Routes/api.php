<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'prefix'=>'demoaccount',
    'middleware'=>'auth:api'
],function(){

    Route::group(['prefix'=>'dashboard'],function(){
        Route::post('getvalues', 'DashboardApiController@getValues')->name('dashboard.getvalues');
        Route::get('get-chart-data', 'DashboardApiController@getChartData')->name('dashboard.getvalues');  
    });

});