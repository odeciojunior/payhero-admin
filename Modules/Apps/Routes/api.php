<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        'middleware' => ['auth:api', 'scopes:admin', 'permission:apps'],
    ],
    function() {
        
        Route::get('/apps/notificacoesinteligentes', 'NotificacoesinteligentesApiController@index');
        Route::get('/apps/notificacoesinteligentes/{id}', 'NotificacoesinteligentesApiController@show');
        Route::get('/apps/notificacoesinteligentes/{id}/edit', 'NotificacoesinteligentesApiController@edit');

        Route::apiResource('/apps/notificacoesinteligentes', 'NotificacoesinteligentesApiController')
            ->only('store', 'update', 'destroy')->middleware('permission:apps_manage');
    }
);

