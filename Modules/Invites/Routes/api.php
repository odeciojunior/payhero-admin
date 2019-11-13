<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth:api']], function() {
    Route::apiResource('invitations', 'InvitesApiController')->only('index', 'store','destroy')->names('api.invites')->middleware('role:account_owner');

    Route::get('/invitations/getinvitationdata', 'InvitesApiController@getInvitationData')
         ->name('api.getinvitationdata')->middleware('role:account_owner');
    Route::post('/invitations/resendinvitation', 'InvitesApiController@resendInvitation')
         ->name('api.resendinvitation')->middleware('role:account_owner');
});

