<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'prefix'=>'demoaccount',
    'middleware'=>'auth:api'
],function(){

    Route::group(['prefix'=>'core'],function(){
        Route::get('verifydocuments', 'CoreApiDemoController@verifyDocuments')->name('demo.verifydocuments');
    });

    Route::group(['prefix'=>'dashboard'],function(){
        Route::post('getvalues', 'DashboardApiDemoController@getValues')->name('demo.dashboard.getvalues');
        Route::get('get-chart-data', 'DashboardApiDemoController@getChartData')->name('demo.dashboard.get-chart-data');  
        Route::get('get-performance', 'DashboardApiDemoController@getPerformance')->name('demo.dashboard.get-performance');
        Route::get('get-account-health', 'DashboardApiDemoController@getAccountHealth')->name('demo.dashboard.get-account-health');
        Route::get('verify-achievements', 'DashboardApiDemoController@getAchievements')->name('demo.dashboard.verify-achievements');
        Route::get('verify-pix-onboarding', 'DashboardApiDemoController@verifyPixOnboarding')->name('demo.dashboard.verify-pix-onboarding');

        Route::get('/', 'DashboardApiDemoController@index')->name('demo.dashboard');
    });

    Route::group(['prefix'=>'projects'],function(){
        Route::get('/', 'ProjectsApiDemoController@index')->name('demo.projects');
    });

});