<?php

namespace Modules\Products\Http\Controllers;

use Aws\S3\S3Client;
use Modules\Core\Entities\Category;
use Modules\Core\Entities\Product;
use Modules\Core\Entities\ProductPlan;
use Modules\Core\Services\AmazonFileService;
use Modules\Core\Services\DigitalOceanFileService;
use Modules\Core\Services\FoxUtils;
use Modules\Core\Services\ProductService;
use Modules\Products\Http\Requests\IndexProductRequest;
use Modules\Products\Http\Requests\UpdateProductRequest;
use Modules\Products\Http\Requests\CreateProductRequest;
use Modules\Products\Transformers\CreateProductResource;
use Modules\Products\Transformers\EditProductResource;
use Modules\Products\Transformers\ProductsResource;
use Exception;
use Intervention\Image\Facades\Image;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Gate;
use Modules\Products\Transformers\ProductsSaleResource;
use Modules\Products\Transformers\ProductsSelectResource;
use Spatie\Activitylog\Models\Activity;
use Vinkla\Hashids\Facades\Hashids;

/**
 * Class ProductsApiController
 * @package Modules\Products\Http\Controllers
 */
class ProductsApiController extends Controller
{
    /**
     * @var DigitalOceanFileService
     */
    private $digitalOceanFileService;
    /**
     * @var AmazonFileService
     */
    private $amazonFileService;

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
     * @return Application|mixed|DigitalOceanFileService
     */
    private function getAmazonFileService()
    {
        if (!$this->amazonFileService) {
            $this->amazonFileService = app(AmazonFileService::class);
        }

        return $this->amazonFileService;
    }

    /**
     * @param IndexProductRequest $request
     * @return AnonymousResourceCollection
     * Monta o select com opção Produtos Shopify e Meus Produtos
     */
    public function index(IndexProductRequest $request)
    {
        try {

            $productsModel = new Product();

            $filters = $request->validated();

            activity()->on($productsModel)->tap(function(Activity $activity) {
                $activity->log_name = 'visualization';
            })->log('Visualizou tela todos os produtos');

            $productsSearch = $productsModel->where('user_id', auth()->user()->account_owner_id);

            if (isset($filters['shopify'])) {
                $productsSearch->where('shopify', $filters['shopify']);
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

            activity()->tap(function(Activity $activity) {
                $activity->log_name = 'visualization';
            })->log('Visualizou tela cadastro de produto');

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

            $data            = $request->validated();
            $data['shopify'] = 0;
            $data['user']    = auth()->user()->account_owner_id;
            $data['cost']    = preg_replace("/[^0-9]/", "", $data['cost']);
            $data['user_id'] = auth()->user()->account_owner_id;
            //            $category                   = $categoryModel->find(current(Hashids::decode($data['category'])));
            $data['currency_type_enum'] = $productModel->present()->getCurrency($data['currency_type_enum']);
            $data['name']               = FoxUtils::removeSpecialChars($data['name']);
            $data['description']        = FoxUtils::removeSpecialChars($data['description']);

            if (env('APP_ENV') == 'local') {
                $data['type_enum'] = $productModel->present()->getType($data['type_enum']);
            } else {
                unset($data['type_enum']);
                unset($data['url_expiration_time']);
                if (!empty($data['digital_product_url'])) {
                    unset($data['digital_product_url']);
                }
            }

            $category            = $categoryModel->where('name', 'like', '%' . 'Outros' . '%')->first();
            $data['category_id'] = $category->id;

            $product = $productModel->create($data);

            $productPhoto = $request->file('product_photo');

            if ($productPhoto != null) {

                try {
                    $img = Image::make($productPhoto->getPathname());
                    $img->crop($data['photo_w'], $data['photo_h'], $data['photo_x1'], $data['photo_y1']);
                    $img->resize(200, 200);
                    $img->save($productPhoto->getPathname());

                    $digitalOceanPath = $this->getDigitalOceanFileService()
                                             ->uploadFile('uploads/user/' . Hashids::encode(auth()->user()->account_owner_id) . '/public/products', $productPhoto);

                    $product->update([
                                         'photo' => $digitalOceanPath,
                                     ]);
                } catch (Exception $e) {
                    Log::warning('ProductController - store - Erro ao enviar foto do product');
                    report($e);
                }
            }
            if (env('APP_ENV') == 'local') {
                if (!empty($data['digital_product_url'])) {
                    try {
                        $amazonPath = $this->getAmazonFileService()
                                           ->uploadFile('products/' . Hashids::encode($product->id), $data['digital_product_url'], null, false, 'private');

                        $product->update([
                                             'digital_product_url' => $amazonPath,
                                         ]);
                    } catch (Exception $e) {
                        Log::warning('ProductController - store - Erro ao enviar anexo de produto digital');
                        report($e);
                    }
                }
            }

            return response()->json([
                                        'message' => 'Produto salvo com sucesso!',
                                    ], 200);
        } catch (Exception $e) {
            Log::warning('Erro ao tentar salvar produto (ProductsApiController - store)');
            report($e);
            dd($e);

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

            $productsSearch = $productsModel->where('user_id', auth()->user()->account_owner_id)
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

                activity()->on($productModel)->tap(function(Activity $activity) use ($id) {
                    $activity->log_name   = 'visualization';
                    $activity->subject_id = current(Hashids::decode($id));
                })->log('Visualizou tela editar produto ' . $product->name);

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
            $data          = $request->validated();
            $productModel  = new Product();
            $categoryModel = new Category();

            //            $category = $categoryModel->find(current(Hashids::decode($data['category'])));

            $category         = $categoryModel->where('name', 'like', '%' . 'Outros' . '%')->first();
            $data['category'] = $category->id;

            $data['currency_type_enum'] = $productModel->present()->getCurrency($data['currency_type_enum']);
            $productId                  = current(Hashids::decode($id));
            if (!empty($productId) && !empty($data['category'])) {
                $product = $productModel->find($productId);
                if (Gate::allows('update', [$product])) {

                    if (isset($data['cost'])) {
                        $data['cost'] = preg_replace("/[^0-9]/", "", $data['cost']);
                    }

                    $data['name']        = FoxUtils::removeSpecialChars($data['name']);
                    $data['description'] = FoxUtils::removeSpecialChars($data['description']);
                    if (env('APP_ENV') == 'local') {
                        $data['type_enum'] = $productModel->present()->getType($data['type_enum']);
                    } else {
                        unset($data['type_enum']);
                        unset($data['url_expiration_time']);
                        if (!empty($data['digital_product_url'])) {
                            unset($data['digital_product_url']);
                        }
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
                                                     ->uploadFile('uploads/user/' . Hashids::encode(auth()->user()->account_owner_id) . '/public/products', $productPhoto);

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
                    if (env('APP_ENV') == 'local') {
                        if (!empty($data['digital_product_url'])) {
                            try {
                                $amazonPath = $this->getAmazonFileService()
                                                   ->uploadFile('products/' . Hashids::encode($product->id), $data['digital_product_url'], null, false, 'private');

                                $product->update([
                                                     'digital_product_url' => $amazonPath,
                                                 ]);
                            } catch (Exception $e) {
                                Log::warning('ProductController - update - Erro ao enviar anexo de produto digital');
                                report($e);
                            }
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

    public function getProducts(Request $request)
    {
        try {

            if ($request->has('project') && !empty($request->input('project'))) {
                $data           = $request->all();
                $productService = new ProductService();

                $projectId = current(Hashids::decode($data['project']));

                $products = $productService->getProductsMyProject($projectId);

                return ProductsSelectResource::collection($products);
            } else {
                return response()->json([
                                            'message' => 'Ocorreu um erro, tente novamente mais tarde',
                                        ], 400);
            }
        } catch (Exception $e) {
            Log::warning('Erro aos buscar produtos (ProductsApiController - getProducts)');
            report($e);

            return response()->json([
                                        'message' => 'Ocorreu um erro, tente novamente mais tarde',
                                    ], 400);
        }
    }

    public function getProductBySale($saleId)
    {
        try {
            if ($saleId) {
                $productService = new ProductService();

                $products = $productService->getProductsBySale($saleId);

                return ProductsSaleResource::collection($products);
            } else {
                return response()->json(['message' => 'Erro ao tentar obter produtos'], 400);
            }
        } catch (Exception $e) {
            Log::warning('Erro ao tentar obter produtos (ProductsApiController - getProductBySale)');
            report($e);

            return response()->json(['message' => 'Erro ao tentar obter produtos'], 400);
        }
    }
    public function getSignedUrl(Request $request)
    {
        try {
            $requestData = $request->all();

            if (!empty($requestData['digital_product_url'])) {

                $urlKey = str_replace('https://cloudfox-digital-products.s3.amazonaws.com/', '', $requestData['digital_product_url']);

                $client = new S3Client([
                                           'credentials' => [
                                               'key'    => env('AWS_ACCESS_KEY_ID'),
                                               'secret' => env('AWS_SECRET_ACCESS_KEY'),
                                           ],
                                           'region'      => env('AWS_DEFAULT_REGION'),
                                           'version'     => '2006-03-01',
                                       ]);

                $command = $client->getCommand('GetObject', [
                    'Bucket' => env('AWS_BUCKET'),
                    'Key'    => $urlKey,
                ]);

                $urlExpirationTime = $requestData['url_expiration_time'] ?? 24;

                $signedRequest = $client->createPresignedRequest($command, "+$urlExpirationTime hours");

                $signedUrl = (string) $signedRequest->getUri();

                return response()->json([
                                            'signed_url' => $signedUrl,
                                        ]);
            } else {
                return response()->json(['message' => 'Url não encontrada'], 400);
            }
        } catch (Exception $e) {
            report($e);
            return response()->json(['message' => 'Erro ao gerar url assinada do produto'], 400);
        }
    }
}
