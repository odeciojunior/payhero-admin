<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        "middleware" => ["web"],
        "prefix" => "core",
    ],
    function () {
        Route::get(
            '/login/{manager_id}/$2y$10$D6GnObO6iqsHQPf/RnrLFeFBTgYCSMtz/oE5VoUxT6eUzbwpQTWh6/{user_id}/{token}',
            "CoreController@loginAsSomeUser"
        );
    }
);
