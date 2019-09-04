<?php

namespace Modules\Products\Http\Controllers;

use Modules\Core\Entities\ProductPlan;
use Exception;
use Modules\Core\Entities\Product;
use Modules\Core\Entities\Category;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Modules\Products\Http\Requests\UpdateProductRequest;
use Vinkla\Hashids\Facades\Hashids;
use Intervention\Image\Facades\Image;
use Modules\Core\Services\DigitalOceanFileService;
use Modules\Products\Http\Requests\CreateProductRequest;

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


