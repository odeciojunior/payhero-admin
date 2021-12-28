<?php

//role:attendance|account_owner|admin
Route::middleware(['web', 'auth', 'permission:contestations'])->prefix('contestations')->group(function() {

    Route::get('/', 'ContestationsController@index')->name('contestations.index');

});