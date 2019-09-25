<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['web', 'auth']], function() {
    Route::apiResource('invitations', 'InvitesApiController')->only('index', 'store','destroy')->names('api.invites');

    Route::get('/invitations/getinvitationdata', 'InvitesApiController@getInvitationData')
         ->name('api.getinvitationdata');
    Route::post('/invitations/resendinvitation', 'InvitesApiController@resendInvitation')
         ->name('api.resendinvitation');
});

