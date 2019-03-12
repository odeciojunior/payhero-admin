<?php

namespace Modules\Core\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

class LayoutController extends Controller {

    public function getMenuLateral() {

        return response()->json([
            'menu' => 'Menu principal'
        ]); 
    }

}
