<?php

namespace Modules\Sales\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\Factory;
use Illuminate\View\View;

class SalesController extends Controller
{
    /**
     * @return Factory|View
     */
    public function index()
    {
        return view('sales::index');
    }
}


