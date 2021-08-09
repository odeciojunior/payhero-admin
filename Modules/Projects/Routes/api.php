<?php

use Illuminate\Support\Facades\Route;

Route::group(
     [
          'middleware' => ['auth:api', 'scopes:admin'],
     ],
     function () {
          Route::get('/projects/user-projects', 'ProjectsApiController@getProjects')
               ->middleware('role:account_owner|admin|attendance');

          //role:account_owner|admin|attendance|finantial
          Route::get('/projects', 'ProjectsApiController@index');          
          Route::get('/projects/{id}', 'ProjectsApiController@show');
          Route::get('/projects/{id}/edit', 'ProjectsApiController@edit');

          Route::apiResource('/projects', 'ProjectsApiController')
               ->only('create', 'store', 'destroy', 'update')->middleware('permission:projects_manage');

          // Verificação de telefone de suporte
          Route::post('/projects/{projectId}/verifysupportphone', 'ProjectsApiController@verifySupportphone')
               ->middleware('permission:projects_managex');

          Route::post('/projects/{projectId}/matchsupportphoneverifycode', 'ProjectsApiController@matchSupportphoneVerifyCode')
               ->middleware('permission:projects_managex');

          // Verificação de email de contato
          Route::post('/projects/{projectId}/verifycontact', 'ProjectsApiController@verifyContact')
               ->middleware('permission:projects_managex');

          Route::post('/projects/{projectId}/matchcontactverifycode', 'ProjectsApiController@matchContactVerifyCode')
               ->middleware('permission:projects_managex');

          Route::post('/projects/updateorder', 'ProjectsApiController@updateOrder')
               ->middleware('permission:projects_managex');

          Route::post('/projects/updateconfig', 'ProjectsApiController@updateConfig')
               ->middleware('permission:projects_managex');
     }
);
