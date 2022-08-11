<?php

namespace Modules\Products\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\View\View;

use Illuminate\Support\Str;
use Modules\Core\Entities\Product;
use Vinkla\Hashids\Facades\Hashids;

class ProductsController extends Controller
{
    /**
     * @return Factory|View
     */
    public function index()
    {
        return view("products::index");
    }

    /**
     * @return Factory|View
     */
    public function create(string $type)
    {
        if ($type == "digital") {
            return view("products::create-digital");
        }

        if ($type == "physical") {
            return view("products::create-physical");
        }

        return view("products::index");
    }

    /**
     * @return Factory|View
     */
    public function edit()
    {
        $id = Str::between(url()->current(), "products/", "/edit");
        if (empty($id)) {
            return view("products::index");
        }

        $product = (new Product())->find(current(Hashids::decode($id)));
        if (isset($product->type_enum) && $product->type_enum === Product::TYPE_DIGITAL) {
            return view("products::edit-digital");
        }

        if (isset($product->type_enum) && $product->type_enum === Product::TYPE_PHYSICAL) {
            return view("products::edit-physical");
        }

        return view("products::index");
    }
}
