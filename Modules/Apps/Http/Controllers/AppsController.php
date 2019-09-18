<?php

namespace Modules\Apps\Http\Controllers;

use Illuminate\Routing\Controller;

/**
 * Class AppsController
 * @package Modules\Apps\Http\Controllers
 */
class AppsController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view('apps::index');
    }
}
