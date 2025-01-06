<?php

declare(strict_types=1);

namespace Modules\AdooreiCheckout\Http\Controllers;

use Illuminate\Routing\Controller;

class AdooreiCheckoutController extends Controller
{
    public function index()
    {
        return view("adooreicheckout::index");
    }
}
