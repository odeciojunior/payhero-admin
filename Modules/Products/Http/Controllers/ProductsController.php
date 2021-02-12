<?php

namespace Modules\Products\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\View\View;

class ProductsController extends Controller
{
    /**
     * @return Factory|View
     */
    public function index()
    {
        return view('products::index');
    }

    /**
     * @return Factory|View
     */
    public function create()
    {
        return view('products::create');
    }

    /**
     * @return Factory|View
     */
    public function edit()
    {
        return view('products::edit');
    }
}


