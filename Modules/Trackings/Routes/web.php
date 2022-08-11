<?php
// role:account_owner|admin|attendance
Route::group(
    [
        "middleware" => ["web", "auth", "permission:trackings"],
    ],
    function () {
        Route::get("/trackings/download/{filename}", "TrackingsController@download");
        Route::resource("/trackings", "TrackingsController")->only("index");
    }
);
