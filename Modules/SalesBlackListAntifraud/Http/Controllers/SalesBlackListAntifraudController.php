<?php

namespace Modules\SalesBlackListAntifraud\Http\Controllers;

use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\View\View;

/**
 * Class SalesBlackListAntifraudController
 * @package Modules\SalesBlackListAntifraud\Http\Controllers
 */
class SalesBlackListAntifraudController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Factory|Response|View
     */
    public function index()
    {
        return view('salesblacklistantifraud::index');
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Factory|Response|View
     */
    public function show($id)
    {
        return view('salesblacklistantifraud::show');
    }
}
