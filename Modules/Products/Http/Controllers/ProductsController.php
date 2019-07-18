<?php

namespace Modules\Products\Http\Controllers;

use App\Entities\ProductPlan;
use Exception;
use App\Entities\Product;
use App\Entities\Category;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Vinkla\Hashids\Facades\Hashids;
use Intervention\Image\Facades\Image;
use Modules\Core\Services\DigitalOceanFileService;
use Modules\Products\Http\Requests\CreateProductRequest;

class ProductsController extends Controller
{
    /**
     * @var Product
     */
    private $productModel;
    /**
     * @var Category
     */
    private $categoryModel;
    /**
     * @var DigitalOceanFileService
     */
    private $digitalOceanFileService;
    /**
     * @var ProductPlan
     */
    private $productsPlansModel;

    /**
     * ProductsController constructor.
     * @param Product $product
     * @param Category $category
     * @param ProductPlan $productPlan
     */
    function __construct(Product $product, Category $category, ProductPlan $productPlan)
    {
        $this->productModel       = $product;
        $this->categoryModel      = $category;
        $this->productsPlansModel = $productPlan;
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|mixed
     */
    private function getDigitalOceanFileService()
    {
        if (!$this->digitalOceanFileService) {
            $this->digitalOceanFileService = app(DigitalOceanFileService::class);
        }

        return $this->digitalOceanFileService;
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
            return view('products::create', [
                'categories' => $this->categoryModel->all(),
            ]);
        } catch (Exception $e) {
            Log::warning('Erro ao tenta acessar pagina de cadastro de produto (ProductsController - create)');
            report($e);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(CreateProductRequest $request)
    {
        try {
            $data            = $request->validated();
            $data['shopify'] = '0';
            $data['user']    = auth()->user()->id;
            $data['price']   = preg_replace("/[^0-9]/", "", $data['price']);
            $data['cost']    = preg_replace("/[^0-9]/", "", $data['cost']);
            $product         = $this->productModel->create($data);

            $productPhoto = $request->file('product_photo');

            if ($productPhoto != null) {

                try {
                    $img = Image::make($productPhoto->getPathname());
                    $img->crop($data['photo_w'], $data['photo_h'], $data['photo_x1'], $data['photo_y1']);
                    $img->resize(200, 200);
                    $img->save($productPhoto->getPathname());

                    $digitalOceanPath = $this->getDigitalOceanFileService()
                                             ->uploadFile('uploads/user/' . Hashids::encode(auth()->user()->id) . '/public/products', $productPhoto);

                    $product->update([
                                         'photo' => $digitalOceanPath,
                                     ]);
                } catch (Exception $e) {
                    Log::warning('ProductController - store - Erro ao enviar foto do product');
                    report($e);
                }
            }

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
            if ($product) {
                return view('products::edit', [
                    'product'    => $product,
                    'categories' => $this->categoryModel->all(),
                ]);
            }

            return redirect()->back();
        } catch (Exception $e) {
            Log::error('Erro ao tentar acessar tela de editar produto (ProductsController - edit)');
            report($e);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update($id, Request $request)
    {
        try {
            $data    = $request->all();
            $product = $this->productModel->findOrFail(Hashids::decode($id))->first();
            if (isset($data['price'])) {
                $data['price'] = preg_replace("/[^0-9]/", "", $data['price']);
            }

            if (isset($data['cost'])) {
                $data['cost'] = preg_replace("/[^0-9]/", "", $data['cost']);
            }
            $product->update($data);

            $productPhoto = $request->file('product_photo');

            if ($productPhoto != null) {

                try {
                    $this->getDigitalOceanFileService()->deleteFile($product->photo);

                    $img = Image::make($productPhoto->getPathname());
                    $img->crop($data['photo_w'], $data['photo_h'], $data['photo_x1'], $data['photo_y1']);
                    $img->resize(200, 200);
                    $img->save($productPhoto->getPathname());

                    $digitalOceanPath = $this->getDigitalOceanFileService()
                                             ->uploadFile('uploads/user/' . Hashids::encode(auth()->user()->id) . '/public/products', $productPhoto);

                    $product->update([
                                         'photo' => $digitalOceanPath,
                                     ]);
                } catch (Exception $e) {
                    Log::warning('ProfileController - update - Erro ao enviar foto do profile');
                    report($e);
                }
            }

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

            $productPlan = $this->productsPlansModel->where('product', $product->id)->count();
            if ($productPlan == 0) {
                $this->getDigitalOceanFileService()->deleteFile($product->photo);
                $product->delete();

                return response()->json([
                                            'message' => 'Produto excluido com sucesso',
                                        ], 200);
            }

            return response()->json([
                                        'message' => 'Impossivel excluir, existem planos associados a este produto!',
                                    ], 400);
        } catch (Exception $e) {
            Log::error('Erro ao tentar excluir produto (ProductsController - delete)');
            report($e);
        }
    }
}


