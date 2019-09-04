<?php

namespace Modules\Products\Http\Controllers;

use Illuminate\Support\Facades\Gate;
use Intervention\Image\Facades\Image;
use Modules\Core\Entities\Category;
use Modules\Core\Entities\Plan;
use Modules\Core\Entities\Product;
use Modules\Core\Entities\ProductPlan;
use Modules\Core\Entities\Project;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Modules\Products\Http\Requests\CreateProductRequest;
use Modules\Products\Transformers\CreateProductResource;
use Modules\Products\Transformers\EditProductResource;
use Modules\Products\Transformers\GetTypeProductsResource;
use Modules\Products\Transformers\ProductsResource;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Modules\Core\Services\DigitalOceanFileService;
use Modules\Products\Http\Requests\UpdateProductRequest;
use Vinkla\Hashids\Facades\Hashids;

class ProductsApiController extends Controller
{
    /**
     * @var DigitalOceanFileService
     */
    private $digitalOceanFileService;

    /**
     * @return Application|mixed|DigitalOceanFileService
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
     * @return GetTypeProductsResource
     * Monta o select com opção Produtos Shopify e Meus Produtos
     */
    public function index(Request $request)
    {
        try {

            $productsModel = new Product();
            $projectModel  = new Project();

            $productShopify   = $productsModel->where('user_id', auth()->user()->id)
                                              ->where('shopify', 1)->first();
            $productsOriginal = $productsModel->where('user_id', auth()->user()->id)
                                              ->where('shopify', 0)->first();

            if (!empty($productShopify)) {
                $projects    = $projectModel->whereHas('usersProjects', function($query) {
                    $query->where('user_id', auth()->user()->id);
                })->whereNotNull('shopify_id')->get();
                $projectsArr = [];
                foreach ($projects as $project) {
                    $projectsArr[] = [
                        'id_code' => $project->id_code,
                        'name'    => $project->name,
                    ];
                }
            }

            return GetTypeProductsResource::make([
                                                     'shopify'         => $productShopify,
                                                     'productOriginal' => $productsOriginal,
                                                     'projects'        => $projectsArr ?? '',
                                                 ]);
        } catch (Exception $e) {
            Log::warning('Erro ao tentar buscar produtos - (ProductsApiController - index)');
            report($e);

            return response()->json(['message' => 'Erro ao tentar buscar produtos, tente novamente mais tarde'], 400);
        }
    }

    /**
     * @return JsonResponse|CreateProductResource
     */
    public function create()
    {
        try {
            $categoryModel = new Category();

            $categories = $categoryModel->all();
            if (!empty($categories)) {
                return CreateProductResource::make(['categories' => $categories]);
            } else {
                return response()->json([
                                            'message' => 'Ocorreu um erro, tente novamente  mais tarde!',
                                        ], 400);
            }
        } catch (Exception $e) {
            Log::warning('Erro ao buscar categorias (ProductsApiController - store)');
            report($e);

            return response()->json([
                                        'message' => 'Ocorreu um erro, tente novamente  mais tarde!',
                                    ], 400);
        }
    }

    /**
     * @param CreateProductRequest $request
     * @return JsonResponse
     */
    public function store(CreateProductRequest $request)
    {
        try {
            $productModel  = new Product();
            $categoryModel = new Category();

            $data                = $request->validated();
            $data['shopify']     = 0;
            $data['user']        = auth()->user()->id;
            $data['price']       = preg_replace("/[^0-9]/", "", $data['price']);
            $data['cost']        = preg_replace("/[^0-9]/", "", $data['cost']);
            $data['user_id']     = auth()->user()->id;
            $category            = $categoryModel->find(current(Hashids::decode($data['category'])));
            $data['category_id'] = $category->id;
            $product             = $productModel->create($data);

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

            return response()->json([
                                        'message' => 'Produto salvo com sucesso!',
                                    ], 200);
        } catch (Exception $e) {
            Log::warning('Erro ao tentar salvar produto (ProductsApiController - store)');
            report($e);

            return response()->json([
                                        'message' => 'Ocorreu um erro, tente novamente mais tarde!',
                                    ], 400);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse|AnonymousResourceCollection
     * Traz a lista de produtos
     */
    public function show(Request $request)
    {
        try {
            $productsModel = new Product();

            $productsSearch = $productsModel->where('user_id', auth()->user()->id)
                                            ->where('shopify', $request->input('shopify'));

            if ($request->has('name') && !empty($request->input('name'))) {
                $productsSearch->where('name', 'LIKE', '%' . $request->nome . '%');
            }

            if ($request->has('project') && !empty($request->input('project') && $request->input('shopify') == 1)) {
                $projectId = current(Hashids::decode($request->input('project')));
                $productsSearch->where('project_id', $projectId);
            }

            return ProductsResource::collection($productsSearch->orderBy('id', 'desc')->paginate(8));
        } catch (Exception $e) {
            Log::warning('Erro ao buscar produtos (ProductsController - index)');
            report($e);

            return response()->json([
                                        'message' => 'Ocorreu um erro, tente novamente mais tarde',
                                    ], 400);
        }
    }

    /**
     * @param $id
     * @return JsonResponse|EditProductResource|void
     */
    public function edit($id)
    {
        try {
            $productModel  = new Product();
            $categoryModel = new Category();

            $productId = current(Hashids::decode($id));

            if ($productId) {
                $product    = $productModel->find($productId);
                $categories = $categoryModel->all();

                if (Gate::allows('edit', [$product])) {
                    return EditProductResource::make([
                                                         'product'    => $product,
                                                         'categories' => $categories,
                                                     ]);
                } else {
                    return response()->json([
                                                'message' => 'Ocorreu um erro, tente novamente mais tarde',
                                            ], 400);
                }
            } else {

                return response()->json([
                                            'message' => 'Produto não encontrado',
                                        ], 400);
            }
        } catch (Exception $e) {
            Log::warning('Erro ao tentar acessar tela de editar Produto (ProductsApiController - edit)');
            report($e);

            return response()->json([
                                        'message' => 'Produto não encontrado',
                                    ], 400);
        }
    }

    /**
     * @param $id
     * @param UpdateProductRequest $request
     * @return JsonResponse
     */
    public function update($id, UpdateProductRequest $request)
    {
        try {
            $data = $request->validated();

            $productModel  = new Product();
            $categoryModel = new Category();

            $data['category'] = $categoryModel->find(current(Hashids::decode($data['category'])));
            $productId        = current(Hashids::decode($id));
            if (!empty($productId) && !empty($data['category'])) {
                $product = $productModel->find($productId);
                if (Gate::allows('update', [$product])) {
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
                            Log::warning('ProductsApiController - update- Erro ao enviar foto do produto');
                            report($e);

                            return response()->json([
                                                        'message' => 'Ocorreu um erro, tente novamente mais tarde',
                                                    ], 400);
                        }
                    }

                    return response()->json([
                                                'message' => 'Produto Atualizado com sucesso!',
                                            ], 200);
                } else {
                    return response()->json([
                                                'message' => 'Ocorreu um erro, tente novamente mais tarde',
                                            ], 400);
                }
            } else {
                return response()->json([
                                            'message' => 'Ocorreu um erro produto não encontrado, tente novamente mais tarde',
                                        ], 400);
            }
        } catch (Exception $e) {
            Log::warning('Erro ao tentar atualizar o produto (ProductsApiController - update)');
            report($e);

            return response()->json([
                                        'message' => 'Ocorreu um erro, tente novamente mais tarde',
                                    ], 400);
        }
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        try {
            $productModel     = new Product();
            $productPlanModel = new ProductPlan();

            $productId = current(Hashids::decode($id));

            if ($productId) {
                $product = $productModel->find($productId);
                if (Gate::allows('destroy', [$product])) {
                    $productPlan = $productPlanModel->where('product_id', $product->id)->count();
                    if ($productPlan == 0) {
                        if (!empty($product->photo)) {
                            $this->getDigitalOceanFileService()->deleteFile($product->photo);
                        }
                        $product->delete();

                        return response()->json([
                                                    'message' => 'Produto excluido com sucesso',
                                                ], 200);
                    } else {
                        return response()->json([
                                                    'message' => 'Impossivel excluir, existem planos associados a este produto!',
                                                ], 400);
                    }
                } else {
                    return response()->json([
                                                'message' => 'Ocorreu um erro, tente novamente mais tarde!',
                                            ], 400);
                }
            } else {
                return response()->json([
                                            'message' => 'Ocorreu um erro, tente novamente mais tarde!',
                                        ], 400);
            }
        } catch (Exception $e) {
            Log::warning('Erro ao tentar excluir produto (ProductsApiController - destroy)');
            report($e);

            return response()->json([
                                        'message' => 'Ocorreu um erro, tente novamente mais tarde!',
                                    ], 400);
        }
    }
}
