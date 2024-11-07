<?php

namespace Modules\Nuvemshop\Http\Controllers;

use Illuminate\Routing\Controller;

class NuvemshopController extends Controller
{
    public function index()
    {
        return view("nuvemshop::index");
    }

    public function finalizeIntegration()
    {
        return view("nuvemshop::finalize-integration");
    }
}
