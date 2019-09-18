<?php

namespace Modules\Profile\Http\Controllers;

use Illuminate\Routing\Controller;
use Modules\Profile\Http\Requests\ProfileIndexRequest;

/**
 * Class ProfileController
 * @package Modules\Profile\Http\Controllers
 */
class ProfileController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view('profile::index');
    }
}
