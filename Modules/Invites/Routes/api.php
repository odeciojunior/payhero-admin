<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['web', 'auth']], function() {
    Route::apiResource('invitations', 'InvitesApiController')->names('api.invites');
});

