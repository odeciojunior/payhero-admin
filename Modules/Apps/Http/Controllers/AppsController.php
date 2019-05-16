<?php

namespace Modules\Apps\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

class AppsController extends Controller {

    public function index() {

        return view('apps::index');
    }
}
