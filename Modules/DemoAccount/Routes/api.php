<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'prefix'=>'demoaccount',
    'middleware'=>'auth:api'
],function(){

    Route::group(['prefix'=>'core'],function(){
        Route::get('verifydocuments', 'CoreApiDemoController@verifyDocuments')->name('demo.verifydocuments');
    });

    Route::group([],function(){
        Route::post('/dashboard/getvalues', 'DashboardApiDemoController@getValues');
        Route::get('/dashboard/get-chart-data', 'DashboardApiDemoController@getChartData');
        Route::get('/dashboard/get-performance', 'DashboardApiDemoController@getPerformance');
        Route::get('/dashboard/get-account-health', 'DashboardApiDemoController@getAccountHealth');
        Route::get('/dashboard/verify-achievements', 'DashboardApiDemoController@getAchievements');
        Route::get('/dashboard/verify-pix-onboarding', 'DashboardApiDemoController@verifyPixOnboarding');

        Route::get('/dashboard', 'DashboardApiDemoController@index')->name('demo.dashboard');
    });

    Route::group([],function(){        
        Route::get('/projects', 'ProjectsApiController@index');
        Route::get('/projects/{id}', 'ProjectsApiController@show');
        Route::get('/projects/{id}/edit', 'ProjectsApiController@edit');
    });

});