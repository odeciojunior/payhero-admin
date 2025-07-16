<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::get("tickets/file/{id}", "TicketsApiController@getFile")->name("api.tickets.getfile");
Route::group(["middleware" => ["auth:api", "scopes:admin", "demo_account"]], function () {
    Route::post("tickets/sendmessage", "TicketsApiController@sendMessage")
        ->name("api.tickets.sendmessage")
        ->middleware("permission:attendance_manage");
    Route::get("tickets/getvalues", "TicketsApiController@getTotalValues")->name("api.tickets.getvalues");
    Route::put("tickets/{id}", "TicketsApiController@update")->middleware("permission:attendance_manage");
    Route::apiResource("tickets", "TicketsApiController")
        ->only("index", "show")
        ->names("api.tickets_api");
});
