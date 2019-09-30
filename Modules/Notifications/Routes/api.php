<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        'middleware' => ['auth:api'],
    ],
    function() {

        Route::post('/notifications/markasread', 'NotificationsApiController@markasread');
        Route::get('/notifications/unreadamount', 'NotificationsApiController@getUnreadNotificationsCount');
        Route::get('/notifications/unread', 'NotificationsApiController@getUnreadNotifications');
    }
);
