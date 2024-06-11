<?php

namespace Modules\VegaCheckout\Http\Controllers;

use Illuminate\Routing\Controller;

class VegaCheckoutController extends Controller
{
    public function index()
    {
        return view("vegacheckout::index");
    }
}
