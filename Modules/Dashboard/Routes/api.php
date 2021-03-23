<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        'middleware' => ['auth:api', 'role:account_owner|admin', 'scopes:admin'],
    ],
    function() {
        Route::apiResource('dashboard', 'DashboardApiController')
             ->only('index')
             ->names('api.dashboard');

        Route::post('/dashboard/getvalues', 'DashboardApiController@getValues');
        Route::get('/dashboard/get-releases', 'DashboardApiController@getReleases');
        Route::get('/dashboard/get-chart-data', 'DashboardApiController@getChartData');
        Route::get('/dashboard/verifypendingdata', 'DashboardApiController@verifyPendingData');

        Route::get('/dashboard/get-performance', 'DashboardApiController@getPerformance');
        Route::get('/dashboard/get-account-health', 'DashboardApiController@getAccountHealth');
        Route::get('/dashboard/get-account-chargeback', 'DashboardApiController@getAccountChargeback');
        Route::get('/dashboard/get-account-attendance', 'DashboardApiController@getAccountAttendance');
        Route::get('/dashboard/get-account-tracking', 'DashboardApiController@getAccountTracking');
        Route::get('/dashboard/verify-achievements', 'DashboardApiController@getAchievements');
        Route::put('/dashboard/verify-achievements/{achievement}', 'DashboardApiController@updateAchievements');
    }
);
