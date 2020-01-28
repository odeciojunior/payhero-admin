<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        'middleware' => ['auth:api', 'setUserAsLogged'],
    ],
    function() {
        Route::get('/projects/user-projects', 'ProjectsApiController@getProjects')
             ->middleware('role:account_owner|admin|attendance');

        Route::apiResource('/projects', 'ProjectsApiController')
             ->only('index', 'create', 'store', 'edit', 'destroy', 'update', 'show')
             ->middleware('role:account_owner|admin|attendance');

        // Verificação de telefone de suporte
        Route::post('/projects/{projectId}/verifysupportphone', 'ProjectsApiController@verifySupportphone')
             ->middleware('role:account_owner|admin');

        Route::post('/projects/{projectId}/matchsupportphoneverifycode', 'ProjectsApiController@matchSupportphoneVerifyCode')
             ->middleware('role:account_owner|admin');

        // Verificação de email de contato
        Route::post('/projects/{projectId}/verifycontact', 'ProjectsApiController@verifyContact')
             ->middleware('role:account_owner|admin');

        Route::post('/projects/{projectId}/matchcontactverifycode', 'ProjectsApiController@matchContactVerifyCode')
             ->middleware('role:account_owner|admin');
    }
);
