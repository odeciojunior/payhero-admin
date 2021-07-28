<?php

use Illuminate\Support\Facades\Route;

Route::get('/apps/melhorenvio/finish', 'MelhorenvioApiController@finishIntegration')
    ->name('melhorenvio.finish');

Route::group(['middleware' => ['auth:api', 'scopes:admin'],], function () {

    Route::get('/apps/melhorenvio/continue/{id}', 'MelhorenvioApiController@continueIntegration')
        ->name('melhorenvio.continue');

    Route::apiResource('/apps/melhorenvio', 'MelhorenvioApiController')
        ->only('index', 'store', 'destroy')
        ->names('melhorenvio');
});
