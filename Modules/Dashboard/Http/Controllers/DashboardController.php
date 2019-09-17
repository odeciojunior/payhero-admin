<?php

namespace Modules\Dashboard\Http\Controllers;

use Illuminate\Contracts\View\Factory;
use Illuminate\Routing\Controller;
use Illuminate\View\View;

/**
 * Class DashboardController
 * @package Modules\Dashboard\Http\Controllers
 */
class DashboardController extends Controller
{

    /**
     * @return Factory|View
     */
    public function index()
    {
        return view('dashboard::dashboard');
    }
}

