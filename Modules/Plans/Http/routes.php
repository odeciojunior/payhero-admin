<?php
Route::group(['middleware' => ['web', 'auth'], 'prefix' => '', 'namespace' => 'Modules\Plans\Http\Controllers'], function() {
    Route::Resource('/plans', 'PlansController')
         ->only('index', 'create', 'store', 'show', 'edit', 'update', 'destroy');
});

