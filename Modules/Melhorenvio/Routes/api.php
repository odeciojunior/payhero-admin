<?php

use Illuminate\Support\Facades\Route;

Route::get('/apps/melhorenvio/finish', 'MelhorenvioApiController@finishIntegration')
    ->name('melhorenvio.finish');

Route::group(['middleware' => ['auth:api', 'scopes:admin','permission:apps|projects'],], function () {

    Route::get('/apps/melhorenvio/continue/{id}', 'MelhorenvioApiController@continueIntegration')
        ->name('melhorenvio.continue');

    Route::get('/apps/melhorenvio', 'MelhorenvioApiController@index');
        
    Route::apiResource('/apps/melhorenvio', 'MelhorenvioApiController')
        ->only('store', 'destroy')
        ->names('melhorenvio')->middleware('permission:apps_manage');
});
