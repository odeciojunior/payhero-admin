<?php

namespace Modules\Shopify\Http\Controllers;

use Illuminate\Routing\Controller;

/**
 * Class ShopifyController
 * @package Modules\Shopify\Http\Controllers
 */
class ShopifyController extends Controller
{
    public function index()
    {
        return view("shopify::index");
    }
}
