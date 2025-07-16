<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(
    [
        "middleware" => ["auth:api", "demo_account","scopes:admin", "permission:apps"],
    ],
    function () {
        Route::get("/apps/notificacoesinteligentes", "NotificacoesInteligentesApiController@index");
        Route::get("/apps/notificacoesinteligentes/{id}", "NotificacoesInteligentesApiController@show");
        Route::get("/apps/notificacoesinteligentes/{id}/edit", "NotificacoesInteligentesApiController@edit");

        Route::apiResource("/apps/notificacoesinteligentes", "NotificacoesInteligentesApiController")
            ->only("store", "update", "destroy")
            ->names("api.notificacoesinteligentes_api")
            ->middleware("permission:apps_manage");
    }
);
