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

use Illuminate\Support\Facades\Route;

/**
 * Public Rote
 */
Route::group(
    [
        'prefix'     => 'invitations',
        'as'         => 'invitations.',
        'middleware' => ['web'],
    ],
    function() {
        Route::post('/obterconvite', 'InvitesController@getInvitation')->name('get.invitation')->middleware('role:account_owner');
        Route::post('/obterconvitehubsmart', 'InvitesController@getHubsmartInvitation')->name('get.hubsmartinvitation')->middleware('role:account_owner');
    }
);

/**
 * Private Rote
 */
Route::group(['middleware' => ['web', 'auth','permission:invitations']], function() {
    Route::get('invitations', 'InvitesController@index')->name('invitations.index');
    Route::Resource('invitations', 'InvitesController')
    ->only('create', 'store')->names('invitations')
    ->middleware('permission:invitations_manage');
});

Route::group(
    [
        'middleware' => ['web'],
        'prefix'     => 'api/invitations',
    ],
    function() {
        Route::get('verifyinvite/{code}', 'InvitesApiController@verifyInviteRegistration')
             ->name('api.verifyinvite');
    }
);
