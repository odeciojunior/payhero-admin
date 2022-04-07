<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'middleware' => ['auth:api', 'scopes:admin'],
], function () {
    Route::apiResource('checkouteditor', 'CheckoutEditorApiController')->only('show', 'update');

    Route::prefix('checkouteditor')->group(function () {

        Route::post('/sendsupportphoneverification', 'CheckoutEditorApiController@sendSupportPhoneVerification')
            ->middleware('role:account_owner|admin');

        Route::post('/verifysupportphone', 'CheckoutEditorApiController@verifySupportPhone')
            ->middleware('role:account_owner|admin');

        Route::post('/sendsupportemailverification', 'CheckoutEditorApiController@sendSupportEmailVerification')
            ->middleware('role:account_owner|admin');

        Route::post('/verifysupportemail', 'CheckoutEditorApiController@verifySupportEmail')
            ->middleware('role:account_owner|admin');
    });
});


