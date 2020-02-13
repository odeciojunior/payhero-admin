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

Route::group(['middleware' => ['auth:api', 'setUserAsLogged']], function() {
    Route::post('tickets/sendmessage', 'TicketsApiController@sendMessage')->name('api.tickets.sendmessage');
    Route::post('tickets/upload', 'TicketsApiController@upload')->name('api.tickets.upload');
    Route::post('tickets/deletefile', 'TicketsApiController@deleteFile')->name('api.tickets.deletefile');
    Route::apiResource('tickets', 'TicketsApiController')->only('index', 'show', 'create', 'store', 'update')->names('api.tickets');
});
