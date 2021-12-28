<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        'middleware' => ['auth:api', 'scopes:admin'],
    ],
    function() {
        Route::get('/project/{projectId}/couponsdiscounts', 'DiscountCouponsApiController@index');
        Route::get('/project/{projectId}/couponsdiscounts/{id}', 'DiscountCouponsApiController@show');
        Route::get('/project/{projectId}/couponsdiscounts/{id}/edit', 'DiscountCouponsApiController@edit');

        Route::apiResource('/project/{projectId}/couponsdiscounts', 'DiscountCouponsApiController')
            ->only('store', 'update', 'destroy')->middleware('permission:projects_manage');
    }
);
