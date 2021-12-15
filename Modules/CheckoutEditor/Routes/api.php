<?php

use Illuminate\Support\Facades\Route;

Route::prefix('checkouteditor')->group(function() {
    Route::apiResource('checkouteditor', 'CheckoutEditorController')->only('show', 'update');

    Route::post('/sendsupportphoneverification', 'CheckoutEditorApiController@sendSupportPhoneVerification')
        ->middleware('role:account_owner|admin');

    Route::post('/verifysupportphone', 'CheckoutEditorApiController@verifySupportPhone')
        ->middleware('role:account_owner|admin');

    Route::post('/sendsupportemailverification', 'CheckoutEditorApiController@sendSupportEmailVerification')
        ->middleware('role:account_owner|admin');

    Route::post('/verifysupportemail', 'CheckoutEditorApiController@verifySupportEmail')
        ->middleware('role:account_owner|admin');
});
