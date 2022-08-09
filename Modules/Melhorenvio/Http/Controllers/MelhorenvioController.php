<?php

namespace Modules\Melhorenvio\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class MelhorenvioController extends Controller
{
    public function index(Request $request)
    {
        return view("melhorenvio::index");
    }
}
