<?php

use Illuminate\Support\Facades\Route;

Route::prefix("checkouteditor")->group(function () {
    Route::get("/", "CheckoutEditorController@index");
});
