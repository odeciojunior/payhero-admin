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
    Route::get('tickets/getvalues', 'TicketsApiController@getTotalValues')->name('api.tickets.getvalues');
    Route::post('tickets/createmessage', 'TicketsApiController@createMessage')->name('api.tickets.createmessage');
    Route::apiResource('tickets', 'TicketsApiController')->only('index', 'show', 'update')->names('api.tickets');
});
