<?php

use Illuminate\Support\Facades\Route;

Route::group(
     [
          'middleware' => ['auth:api', 'scopes:admin'],
     ],
     function() {
          Route::get('/projects/user-projects', 'ProjectsApiController@getProjects')
               ->middleware('permission:projects_manage');

          Route::apiResource('/projects', 'ProjectsApiController')
               ->only('index','show')
               ->middleware('permission:projects|sales');

          Route::apiResource('/projects', 'ProjectsApiController')
          ->only('create', 'store', 'edit', 'destroy', 'update')
          ->middleware('permission:projects_manage');

          // Verificação de telefone de suporte
          Route::post('/projects/{projectId}/verifysupportphone', 'ProjectsApiController@verifySupportphone')
               ->middleware('permission:projects_manage');

          Route::post('/projects/{projectId}/matchsupportphoneverifycode', 'ProjectsApiController@matchSupportphoneVerifyCode')
               ->middleware('permission:projects_manage');

          // Verificação de email de contato
          Route::post('/projects/{projectId}/verifycontact', 'ProjectsApiController@verifyContact')
               ->middleware('permission:projects_manage');

          Route::post('/projects/{projectId}/matchcontactverifycode', 'ProjectsApiController@matchContactVerifyCode')
               ->middleware('permission:projects_manage');

          Route::post('/projects/updateorder', 'ProjectsApiController@updateOrder')
               ->middleware('permission:projects_manage');

          Route::post('/projects/updateconfig', 'ProjectsApiController@updateConfig')
               ->middleware('permission:projects_manage');

          Route::get('/projects/{id}/companie', 'ProjectsApiController@getCompanieByProject')
               ->middleware('permission:projects_manage');
     }
);
