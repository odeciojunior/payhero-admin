<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'prefix'=>'demoaccount',
    'middleware'=>'auth:api'
],function(){

    //core
    Route::group(['prefix'=>'core'],function(){
        Route::get('verifydocuments', 'CoreApiDemoController@verifyDocuments');
    });

    //Dashboard
    Route::group([],function(){
        Route::post('/dashboard/getvalues', 'DashboardApiDemoController@getValues');
        Route::get('/dashboard/get-chart-data', 'DashboardApiDemoController@getChartData');
        Route::get('/dashboard/get-performance', 'DashboardApiDemoController@getPerformance');
        Route::get('/dashboard/get-account-health', 'DashboardApiDemoController@getAccountHealth');
        Route::get('/dashboard/verify-achievements', 'DashboardApiDemoController@getAchievements');
        Route::get('/dashboard/verify-pix-onboarding', 'DashboardApiDemoController@verifyPixOnboarding');

        Route::get('/dashboard', 'DashboardApiDemoController@index')->name('demo.dashboard');
    });

    //Domains
    Route::group([],function(){   
        Route::get('/project/{projectId}/domains', 'DomainsApiDemoController@index');        
        Route::get('/project/{projectId}/domains/{domainId}', 'DomainsApiDemoController@show');
    });

    //Projects
    Route::group([],function(){        
        Route::get('/projects', 'ProjectsApiDemoController@index');
        Route::get('/projects/{id}', 'ProjectsApiDemoController@show');
        Route::get('/projects/{id}/edit', 'ProjectsApiDemoController@edit');
    });

    //Plans
    Route::group([],function(){
        Route::get('/project/{projectId}/plans', 'PlansApiDemoController@index');
    });

    //Shippings
    Route::group([],function(){
        Route::get('/project/{projectId}/shippings', 'ShippingApiDemoController@index');
        Route::get('/project/{projectId}/shippings/{id}', 'ShippingApiDemoController@show');
    });

    //ProjectUpsellRule
    Route::group([],function(){
        Route::get('/projectupsellrule', 'ProjectUpsellRuleApiDemoController@index');
        Route::get('/projectupsellrule/{id}', 'ProjectUpsellRuleApiDemoController@show');
    });

    //OrderBump
    Route::group([],function(){
        Route::get('orderbump', 'OrderBumpApiDemoController@index');
        Route::get('orderbump/{id}', 'OrderBumpApiDemoController@show');
    });

    //discountCoupons
    Route::group([],function(){
        Route::get('/project/{projectId}/couponsdiscounts', 'DiscountCouponsApiDemoController@index');
        Route::get('/project/{projectId}/couponsdiscounts/{id}', 'DiscountCouponsApiDemoController@show');
    });

    //ProjectReviews
    Route::group([],function(){
        Route::get('/projectreviews', 'ProjectReviewsApiDemoController@index');
        Route::get('/projectreviews/{id}', 'ProjectReviewsApiDemoController@show');
    });

    //ChekoutEditor
    Route::group([],function(){
        Route::apiResource('checkouteditor', 'CheckoutEditorApiDemoController')->only('show');
    });

    //Pixels
    Route::group([],function(){
        Route::get("/projects/{projectId}/pixels/configs", 'PixelsApiDemoController@getPixelConfigs')->name('pixels.getconfig');        
        Route::get('/project/{projectId}/pixels', 'PixelsApiDemoController@index');
        Route::get('/project/{projectId}/pixels/{id}', 'PixelsApiDemoController@show');
    });

    //ProjectNotification
    Route::group([],function(){
        Route::get('/project/{projectId}/projectnotification', 'ProjectNotificationApiDemoController@index');
        Route::get('/project/{projectId}/projectnotification/{id}', 'ProjectNotificationApiDemoController@show');
    });

    Route::get('/not-authorized','DemoAccountController@notAuthorized')->name('demo.not_authorized');
});