<?php

Route::group(['middleware' => 'web', 'prefix' => 'atendimento', 'namespace' => 'Modules\Attendance\Http\Controllers'], function()
{
    Route::get('/', [
        'uses' => 'AttendanceController@index',
        'as' => 'attendance'
    ]);
    
});
