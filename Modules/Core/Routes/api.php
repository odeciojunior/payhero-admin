<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        'middleware' => ['auth:api'],
    ],
    function() {
        Route::get('/core/verify-account/{userID}', 'CoreApiController@verifyAccount');

        Route::get('/core/verifydocuments', 'CoreApiController@verifyDocuments');

        Route::get('/core/usercompanies', 'CoreApiController@getCompanies')
        //->name('api.companies.getcompanies')
        ->middleware('role:account_owner|admin');

        Route::get('/core/companies', 'CoreApiController@companies')
        //->names('api.companies')
        ->middleware('permission:sales|finances|report_pending|apps|invitations');

        Route::get('/core/check-bonus-balance', 'CoreApiController@hasBonusBalance');
        Route::get('/core/get-bonus-balance', 'CoreApiController@getBonusBalance');
    }
);

Route::group(
    [
        'middleware' => ['InternalApiAuth'],
    ],
    function() {
        Route::get('/core/sac/allow-block/{company_id}/{sale_id}', 'CoreApiController@allowBlockBalance');
        Route::post('/core/sac/ticket-notification/{ticketId}', 'CoreApiController@notifyTicket');
    }
);

