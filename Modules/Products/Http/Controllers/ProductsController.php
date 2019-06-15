<?php

namespace Modules\Products\Http\Controllers;

use App\Entities\Product;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Exception;
use Vinkla\Hashids\Facades\Hashids;

class ProductsController extends Controller
{
    /**
     * @var Product
     */
    private $productModel;

    /**
     * ProductsController constructor.
     * @param Product $product
     */
    function __construct(Product $product)
    {
        $this->productModel = $product;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        try {
            if (isset($request->nome)) {
                $productsSearch = $this->productModel->where('name', 'LIKE', '%' . $request->nome . '%')
                                                     ->where('user', auth()->user()->id)
                                                     ->where('shopify', 0);
            } else {
                $productsSearch = $this->productModel->where('user', auth()->user()->id)->where('shopify', 0);
            }

            $products = $productsSearch->orderBy('id', 'DESC')->paginate(12);

            return view('products::index', ['products' => $products]);
        } catch (Exception $e) {
            Log::warning('Erro ao buscar produtos (ProductsController - index)');
            report($e);
        }
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        try {
            return view('products::create');
        } catch (Exception $e) {
            Log::warning('Erro ao tenta acessar pagina de cadastro de produto (ProductsController - create)');
            report($e);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        try {
            $data         = $request->all();
            $data['user'] = auth()->user()->id;
            $product      = $this->productModel->create($data);

            return redirect()->route('products.index');
        } catch (Exception $e) {
            Log::error('Erro ao tentar cadastrar produto (ProductsController - store)');
            report($e);
        }
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        try {
            $product = $this->productModel->find(Hashids::decode($id))->first();

            return view('products::edit', ['product' => $product]);
        } catch (Exception $e) {
            Log::error('Erro ao tentar acessar tela de editar produto (ProductsController - edit)');
            report($e);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        try {
            $data    = $request->all();
            $product = $this->productModel->find(Hashids::decode($data['id']))->first();
            $product->update($data);

            return redirect()->route('products.index');
        } catch (Exception $e) {
            Log::warning('Erro ao tentar atualizar produto (ProductsController - update)');
            report($e);
        }
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        try {
            $product = $this->productModel->find(Hashids::decode($id))->first();
            $product->delete();

            return redirect()->route('products.index');
        } catch (Exception $e) {
            Log::error('Erro ao tentar excluir produto (ProductsController - delete)');
            report($e);
        }
    }

}


