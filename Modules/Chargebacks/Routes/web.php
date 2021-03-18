<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware(['web', 'auth', 'role:account_owner|admin'])->prefix('contestations')->group(function() {
    Route::get('/', 'ContestationsController@index')->name('contestations.index');
    Route::get('/getcontestations', 'ContestationsController@getContestations')->name('contestations.getchargebacks');
    Route::get('/gettotalvalues', 'ContestationsController@getTotalValues')->name('contestations.gettotalvalues');
    Route::get('/{contestation_id}', 'ContestationsController@show')->name('contestations.show');

    Route::post('/set-observation/{id}', 'ContestationsController@setValueObservation')->name('contestations.setvalueobservation');
    Route::get('/get-observation/{id}', 'ContestationsController@getObservation')->name('contestations.getobservation');
    Route::post('/update-is-contested', 'ContestationsController@updateIsContested')->name('users.updateiscontested');
    Route::get('/generation-dispute/{id}', 'ContestationsController@generateDispute')->name('contestations.generateDispute');
    Route::post('/send-contestation', 'ContestationsController@sendContestation')->name('contestations.sendContestation');

    Route::get('/get-contestation-files/{salecontestation}', 'ContestationsController@getContestationFiles')->name('contestations.getContestationFiles');

});

//Route::prefix('chargebacks')->group(function() {
//    Route::get('/', 'ChargebacksController@index');
//});
