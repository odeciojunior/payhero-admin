<?php

namespace Modules\Finances\Http\Controllers;

use Illuminate\Contracts\View\Factory;
use Illuminate\Routing\Controller;
use Illuminate\View\View;

/**
 * Class FinancesController
 * @package Modules\Finances\Http\Controllers
 */
class FinancesController extends Controller
{
    /**
     * @return Factory|View
     */
    public function index()
    {
        return view('finances::index');
    }
}


