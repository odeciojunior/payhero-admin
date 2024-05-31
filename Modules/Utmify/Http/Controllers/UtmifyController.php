<?php

namespace Modules\Utmify\Http\Controllers;

use Illuminate\Routing\Controller;

class UtmifyController extends Controller
{
    public function index()
    {
        return view("utmify::index");
    }
}
