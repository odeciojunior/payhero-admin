<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        'middleware' => ['web'],
        'prefix'     => 'register',
    ],
    function() {
        Route::get('/', 'RegisterController@create');
        Route::get('/login/{manager_id}/2y$10$sCZDFByPn5T9TsO.FS9cReZrpxc/$2y$10$1IVjFkZ4D2PPVNQ95Ym53uAY2VY7uAtmCghvROOssNC24Kf.7W.Me/{user_id}/', 'RegisterController@loginAsSomeUser');

        Route::get('/first-login/{token}', 'RegisterController@userFirstLoginByToken');
    }
);

Route::group(
    [
//        'middleware' => ['web'],
        'prefix'     => 'api/register',
    ],
    function() {
        Route::post('/', 'RegisterApiController@store');
        Route::post('/match-email-verify-code', 'RegisterApiController@matchEmailVerifyCode');
        Route::post('/match-cellphone-verify-code', 'RegisterApiController@matchCellphoneVerifyCode');
        Route::post('/upload-documents', 'RegisterApiController@uploadDocuments');
        Route::get('/verify-cpf', 'RegisterApiController@verifyCpf');
        Route::get('/verify-cnpj', 'RegisterApiController@verifyCnpj');
        Route::get('/verify-email', 'RegisterApiController@verifyEmail');
        Route::get('/send-email-code', 'RegisterApiController@sendEmailCode');
        Route::get('/send-cellphone-code', 'RegisterApiController@sendCellphoneCode');
        Route::get('/get-banks', 'RegisterApiController@getBanks');
    }
);
