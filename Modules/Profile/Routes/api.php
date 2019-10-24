<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        'middleware' => ['auth:api'],
    ],
    function() {
        Route::apiResource('profile', 'ProfileApiController')
             ->only('index', 'show', 'edit', 'store', 'update', 'destroy', 'create')
             ->names('api.profile');

        Route::post('/profile/uploaddocuments', 'ProfileApiController@uploaddocuments');

        // Verificação de celular
        Route::post('/profile/verifycellphone', 'ProfileApiController@verifyCellphone');

        Route::post('/profile/matchcellphoneverifycode', 'ProfileApiController@matchCellphoneVerifyCode');

        // Verificação de email
        Route::post('/profile/verifyemail', 'ProfileApiController@verifyEmail');

        Route::post('/profile/matchemailverifycode', 'ProfileApiController@matchEmailVerifyCode');

        Route::post('/profile/changepassword', 'ProfileApiController@changePassword');

        Route::post('/profile/updatetaxes', 'ProfileApiController@updateTaxes');

        Route::get('/profile/{usercode}/tax', 'ProfileApiController@getTax');

        // Atualização da userNotification
        Route::post('/profile/updatenotification', 'ProfileApiController@updateUserNotification');
    }
);
