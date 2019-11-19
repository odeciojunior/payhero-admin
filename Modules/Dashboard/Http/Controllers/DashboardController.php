<?php
 
namespace Modules\Dashboard\Http\Controllers;

use Illuminate\View\View;
use Illuminate\Routing\Controller;
use Modules\Core\Services\FoxUtils;

/**
 * Class DashboardController
 * @package Modules\Dashboard\Http\Controllers
 */
class DashboardController extends Controller
{

    /**
     * @return View
     */
    public function index()
    {
        return view('dashboard::dashboard');
    }
}

