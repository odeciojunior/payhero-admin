<?php

namespace Modules\CheckoutEditor\Http\Controllers;

use Illuminate\Routing\Controller;

class CheckoutEditorController extends Controller
{
    public function index()
    {
        return view('checkouteditor::index');
    }
}
