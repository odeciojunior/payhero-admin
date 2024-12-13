<?php

declare(strict_types=1);

namespace Modules\VegaCheckout\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Routing\Controller;

class VegaCheckoutController extends Controller
{
    public function index(): View
    {
        return view('vegacheckout::index');
    }
}
