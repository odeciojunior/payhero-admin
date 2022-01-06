<?php

use Illuminate\Support\Facades\Route;

Route::group(
     [
          'middleware' => ['auth:api', 'scopes:admin'],
     ],
     function () {
          Route::get('/projects/user-projects', 'ProjectsApiController@getProjects')
               ->middleware('permission:projects|apps');

          //role:account_owner|admin|attendance|finantial
          Route::get('/projects', 'ProjectsApiController@index');          
          Route::get('/projects/create', 'ProjectsApiController@create');
          Route::get('/projects/{id}', 'ProjectsApiController@show');
          Route::get('/projects/{id}/edit', 'ProjectsApiController@edit');

          Route::apiResource('/projects', 'ProjectsApiController')
               ->only('store', 'destroy', 'update')->middleware('permission:projects_manage');

          // Nova Edicao de projeto com novo metodo
          Route::put("/projects/{id}/settings", "ProjectsApiController@updateSettings");

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
     }
);
