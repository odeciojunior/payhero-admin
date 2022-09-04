<?php

namespace Modules\DemoAccount\Http\Controllers;

use Exception;
use Illuminate\Support\Str;
use Modules\Core\Entities\Product;
use Modules\Core\Entities\Company;
use Vinkla\Hashids\Facades\Hashids;
use Modules\Core\Entities\Category;
use Illuminate\Support\Facades\Http;
use Modules\Core\Entities\User;
use Modules\Products\Transformers\ProductsResource;
use Modules\Products\Transformers\EditProductResource;
use Modules\Products\Http\Requests\IndexProductRequest;
use Modules\Products\Http\Controllers\ProductsApiController;

class ProductsApiDemoController extends ProductsApiController
{
    public function index(IndexProductRequest $request)
    {
        try {
            $filters = $request->validated();

            $productsSearch = Product::with('productsPlans')->where('user_id', User::DEMO_ID);

            if (isset($filters['shopify']) && $filters['shopify'] == 1) {
                $productsSearch->where('shopify', $filters['shopify']);
            } else {
                $productsSearch->where('shopify', 0);
            }

            if (isset($filters['name'])) {
                $productsSearch->where('name', 'LIKE', '%' . $filters['name'] . '%');
            }

            if (isset($filters['project']) && $filters['shopify'] == 1) {
                $projectId = current(Hashids::decode($filters['project']));
                $productsSearch->where('project_id', $projectId);
            }

            return ProductsResource::collection($productsSearch->orderBy('id', 'desc')->paginate(8));

        } catch (Exception $e) {
            report($e);

            return response()->json(['message' => 'Erro ao tentar buscar produtos, tente novamente mais tarde'], 400);
        }
    }

    public function edit($id)
    {
        try {
            if (empty($id)) {
                return response()->json(['message' => 'Produto não encontrado'], 400);
            }

            $product = Product::find(current(Hashids::decode($id)));

            $categories = Category::all();

            if (Str::contains($product->photo, '?v=')) {
                $productUrl = Str::before($product->photo, '?v=');
                $product->photo = Http::get($productUrl)->successful() ? $productUrl : '';
            }

            return EditProductResource::make([
                'product' => $product,
                'categories' => $categories,
            ]);
        } catch (Exception $e) {
            report($e);

            return response()->json(['message' => 'Produto não encontrado'], 400);
        }
    }
}
