<?php

namespace Modules\HotZapp\Http\Controllers;

use Illuminate\Contracts\View\Factory;
use Illuminate\Routing\Controller;
use Illuminate\View\View;

class HotZappController extends Controller
{
    /**
     * @return Factory|View
     */
    public function index()
    {
        return view('hotzapp::index');
    }
}
