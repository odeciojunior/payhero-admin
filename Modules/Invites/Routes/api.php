<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['web', 'auth']], function() {
    Route::apiResource('invitations', 'InvitesApiController')->only('index', 'store')->names('api.invites');

    Route::get('/invitations/getinvitationdata', 'InvitesApiController@getInvitationData')
         ->name('api.getinvitationdata');
});

