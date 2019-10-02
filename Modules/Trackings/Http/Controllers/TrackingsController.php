<?php

namespace Modules\Trackings\Http\Controllers;

use Illuminate\Contracts\View\Factory;
use Illuminate\Routing\Controller;
use Illuminate\View\View;

class TrackingsController extends Controller
{

    /**
     * @return Factory|View
     */
    public function index()
    {
        return view('trackings::index');
    }
}
