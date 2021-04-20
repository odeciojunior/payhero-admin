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
        'middleware' => ['auth:api', 'role:attendance|account_owner|admin'],
        'prefix' => 'contestations'
    ],
    function() {


        Route::get('/getcontestations', 'ContestationsApiController@getContestations')->name('contestations.getchargebacks');
        Route::get('/gettotalvalues', 'ContestationsApiController@getTotalValues')->name('contestations.gettotalvalues');
        Route::get('/get-contestation-files/{salecontestation}', 'ContestationsApiController@getContestationFiles')->name('contestations.getContestationFiles');
        Route::post('/send-files', 'ContestationsApiController@sendContestationFiles')->name('contestations.sendContestationFiles');
        Route::get('/{contestationfile}/removefile', 'ContestationsApiController@removeContestationFiles')->name('contestations.removeContestationFiles');
        Route::post('/update-is-file-completed', 'ContestationsApiController@updateIsFileCompleted')->name('users.updateIsFileCompleted');
        Route::get('/{contestation_id}/contestation', 'ContestationsApiController@show')->name('contestations.show');

    });
