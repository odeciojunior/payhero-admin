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
        Route::post('/obterconvite', 'InvitesController@getInvitation')->name('get.invitation');
        Route::post('/obterconvitehubsmart', 'InvitesController@getHubsmartInvitation')->name('get.hubsmartinvitation');
    }
);

/**
 * Private Rote
 */
Route::group(['middleware' => ['web', 'auth']], function() {
    Route::Resource('invitations', 'InvitesController')->only('index', 'create', 'store')->names('invitations');
});
