<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        'middleware' => ['auth:api'],
    ],
    function () {
        Route::get('/projects/user-projects', 'ProjectsApiController@getProjects');

        Route::apiResource('/projects', 'ProjectsApiController')
            ->only('index', 'create', 'store', 'edit', 'destroy', 'update', 'show');

        // Verificação de telefone de suporte
        Route::post('/projects/{projectId}/verifysupportphone', 'ProjectsApiController@verifySupportphone');

        Route::post('/projects/{projectId}/matchsupportphoneverifycode', 'ProjectsApiController@matchSupportphoneVerifyCode');

        // Verificação de email de contato
        Route::post('/projects/{projectId}/verifycontact', 'ProjectsApiController@verifyContact');

        Route::post('/projects/{projectId}/matchcontactverifycode', 'ProjectsApiController@matchContactVerifyCode');
    }
);
