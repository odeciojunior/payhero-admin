<?php
/**
 * Rotas publicas
 */
Route::group(
    [
        'prefix'     => 'invitations',
        'as'         => 'invitations.',
        'middleware' => ['web'],
        'namespace'  => 'Modules\Invites\Http\Controllers',
    ],
    function() {
        // rotas publicas

        Route::post('/obterconvite', 'InvitesController@getInvitation')->name('get.invitation');
        Route::post('/obterconvitehubsmart', 'InvitesController@getHubsmartInvitation')->name('get.hubsmartinvitation');
    }
);

/**
 * Rotas autenticadas
 */
Route::group(
    [
        'prefix'     => 'invitations',
        'as'         => 'invitations.',
        'middleware' => ['web', 'auth'],
        'namespace'  => 'Modules\Invites\Http\Controllers',
    ],
    function() {
        // rotas autenticadas

        Route::get('/', 'InvitesController@index')->name('invites');
        Route::post('/sendinvitation', 'InvitesController@sendInvitation')->name('send.invitation');
    }
);

/*
Route::group(['middleware' => ['web','auth'], 'prefix' => 'convites', 'namespace' => 'Modules\Invites\Http\Controllers'], function()
{
    Route::get('/', [
        'uses' => 'InvitesController@index',
        'as' => 'invites'
    ]);

    Route::post('/enviarconvite', [
        'uses' => 'InvitesController@sendInvitation',
        'as' => 'invites.sendinvitation'
    ]);

});

*/

/*
Route::group(['prefix' => 'convites', 'namespace' => 'Modules\Invites\Http\Controllers'], function() {

    Route::post('/obterconvite', [
        'uses' => 'InvitesController@getInvitation',
    ]);

    Route::post('/obterconvitehubsmart', [
        'uses' => 'InvitesController@getHubsmartInvitation',
    ]);
});

*/

Route::group(['middleware' => 'auth:api', 'prefix' => 'api/convites', 'namespace' => 'Modules\Invites\Http\Controllers'], function() {
    Route::get('/', [
        'uses' => 'InvitesApiController@convites',
    ]);

    Route::post('/', [
        'uses' => 'InvitesApiController@enviarConvite',
    ]);
});

