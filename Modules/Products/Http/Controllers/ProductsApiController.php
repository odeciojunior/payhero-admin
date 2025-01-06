<?php

namespace Modules\Products\Http\Controllers;

use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Modules\Core\Entities\Category;
use Modules\Core\Entities\Product;
use Modules\Core\Entities\ProductPlan;
use Modules\Core\Entities\ProductPlanSale;
use Modules\Core\Entities\Project;
use Modules\Core\Services\AmazonFileService;
use Modules\Core\Services\CacheService;
use Modules\Core\Services\FoxUtils;
use Modules\Core\Services\ProductService;
use Modules\Products\Http\Requests\CreateProductRequest;
use Modules\Products\Http\Requests\IndexProductRequest;
use Modules\Products\Http\Requests\UpdateProductRequest;
use Modules\Products\Transformers\CreateProductResource;
use Modules\Products\Transformers\EditProductResource;
use Modules\Products\Transformers\ProductsResource;
use Modules\Products\Transformers\ProductsSaleResource;
use Modules\Products\Transformers\ProductsSelectResource;
use Modules\Products\Transformers\ProductVariantResource;
use Spatie\Activitylog\Models\Activity;
use Vinkla\Hashids\Facades\Hashids;

/**
 * Class ProductsApiController
 * @package Modules\Products\Http\Controllers
 */
class ProductsApiController extends Controller
{
    private $amazonFileService;

    /**
     * @return Application|mixed
     */
    private function getAmazonFileService()
    {
        if (!$this->amazonFileService) {
            $this->amazonFileService = app(AmazonFileService::class);
        }

        return $this->amazonFileService;
    }

    /**
     * Monta o select com opção Produtos Shopify e Meus Produtos
     * @param IndexProductRequest $request
     * @return JsonResponse|AnonymousResourceCollection
     */
    public function index(IndexProductRequest $request)
    {
        try {
            $productsModel = new Product();

            $filters = $request->validated();

            activity()
                ->on($productsModel)
                ->tap(function (Activity $activity) {
                    $activity->log_name = "visualization";
                })
                ->log("Visualizou tela todos os produtos");

            $productsSearch = $productsModel->with("productsPlans")->where("user_id", auth()->user()->account_owner_id);

            // Shopify products
            if (isset($filters["shopify"]) && $filters["shopify"] == 1) {
                $productsSearch->where("shopify", $filters["shopify"]);
            }
            // Woocommerce products
            elseif (isset($filters["shopify"]) && $filters["shopify"] == 2) {
                $productsSearch
                    ->where("shopify", 0)
                    ->whereHas("project", function ($query) {
                        $query->whereNotNull("woocommerce_id");
                    })
                    ->where(function ($query) {
                        $query->whereRaw(
                            "(shopify_id IS NOT NULL AND shopify_variant_id IS NOT NULL) OR
                            (shopify_id IS NULL AND shopify_variant_id IS NOT NULL) OR
                            (shopify_id IS NOT NULL AND shopify_variant_id IS NULL)",
                        );
                    });
            }
            // Nuvemshop products
            elseif (isset($filters["shopify"]) && $filters["shopify"] == 3) {
                $productsSearch
                    ->where("shopify", 0)
                    ->whereHas("project", function ($query) {
                        $query->whereNotNull("nuvemshop_id");
                    })
                    ->where(function ($query) {
                        $query->whereRaw(
                            "(shopify_id IS NOT NULL AND shopify_variant_id IS NOT NULL) OR
                            (shopify_id IS NULL AND shopify_variant_id IS NOT NULL) OR
                            (shopify_id IS NOT NULL AND shopify_variant_id IS NULL)",
                        );
                    });
            }
            // Sirius products
            else {
                $productsSearch
                    ->where("shopify", 0)
                    ->whereNull("shopify_id")
                    ->whereNull("shopify_variant_id");
            }

            if (isset($filters["name"])) {
                $productsSearch->where("name", "LIKE", "%" . $filters["name"] . "%");
            }

            if (
                isset($filters["project"]) &&
                ($filters["shopify"] == 1 || $filters["shopify"] == 2 || $filters["shopify"] == 3)
            ) {
                $projectId = current(Hashids::decode($filters["project"]));
                $productsSearch->where("project_id", $projectId);
            }

            return ProductsResource::collection($productsSearch->orderBy("id", "desc")->paginate(8));
        } catch (Exception $e) {
            report($e);

            return response()->json(["message" => "Erro ao tentar buscar produtos, tente novamente mais tarde"], 400);
        }
    }

    public function create()
    {
        try {
            activity()
                ->tap(function (Activity $activity) {
                    $activity->log_name = "visualization";
                })
                ->log("Visualizou tela cadastro de produto");

            $categories = (new Category())->all();

            if (empty($categories)) {
                return response()->json(["message" => "Ocorreu um erro, tente novamente  mais tarde!"], 400);
            }

            return CreateProductResource::make(["categories" => $categories]);
        } catch (Exception $e) {
            report($e);

            return response()->json(["message" => "Ocorreu um erro, tente novamente  mais tarde!"], 400);
        }
    }

    public function store(CreateProductRequest $request)
    {
        try {
            $productModel = new Product();

            $data = $request->validated();
            $data["shopify"] = 0;
            $data["user"] = auth()->user()->account_owner_id;
            $data["user_id"] = auth()->user()->account_owner_id;
            $data["name"] = FoxUtils::removeSpecialChars($data["name"]);
            $data["description"] = FoxUtils::removeSpecialChars($data["description"]);

            $data["status_enum"] =
                $data["type_enum"] == "digital" ? $productModel->present()->getStatus("approved") : null;

            $data["type_enum"] = $productModel->present()->getType($data["type_enum"]);

            $product = $productModel->create($data);

            $productPhoto = $request->file("product_photo");

            if ($productPhoto != null) {
                try {
                    $img = Image::make($productPhoto->getPathname());
                    $img->save($productPhoto->getPathname());

                    $amazonPath = $this->getAmazonFileService()->uploadFile("uploads/public/products", $productPhoto);

                    $product->update([
                        "photo" => $amazonPath,
                    ]);
                } catch (Exception $e) {
                    Log::warning("ProductController - store - Erro ao enviar foto do product");
                    report($e);
                }
            }

            if (!empty($data["digital_product_url"])) {
                try {
                    $this->getAmazonFileService()->changeDisk("s3_digital_product");
                    $amazonPath = $this->getAmazonFileService()->uploadFile(
                        "products/" . Hashids::encode($product->id),
                        $data["digital_product_url"],
                        null,
                        false,
                        "private",
                    );

                    $product->update([
                        "digital_product_url" => $amazonPath,
                    ]);
                } catch (Exception $e) {
                    report($e);
                }
            }

            return response()->json(["message" => "Produto salvo com sucesso!"], 200);
        } catch (Exception $e) {
            report($e);

            return response()->json(["message" => "Ocorreu um erro, tente novamente mais tarde!"], 400);
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

            $productsSearch = $productsModel
                ->where("user_id", auth()->user()->account_owner_id)
                ->where("shopify", $request->input("shopify"));

            if ($request->has("name") && !empty($request->input("name"))) {
                $productsSearch->where("name", "LIKE", "%" . $request->nome . "%");
            }

            if ($request->has("project") && !empty($request->input("project") && $request->input("shopify") == 1)) {
                $projectId = current(Hashids::decode($request->input("project")));
                $productsSearch->where("project_id", $projectId);
            }

            return ProductsResource::collection($productsSearch->orderBy("id", "desc")->paginate(8));
        } catch (Exception $e) {
            report($e);

            return response()->json(["message" => "Ocorreu um erro, tente novamente mais tarde"], 400);
        }
    }

    public function edit($id)
    {
        try {
            if (empty($id)) {
                return response()->json(["message" => "Produto não encontrado"], 400);
            }

            $productModel = new Product();
            $categoryModel = new Category();

            $product = $productModel->find(current(Hashids::decode($id)));

            if (!Gate::allows("edit", [$product])) {
                return response()->json(["message" => "Sem permissão para editar este produto"], 400);
            }

            activity()
                ->on($productModel)
                ->tap(function (Activity $activity) use ($id) {
                    $activity->log_name = "visualization";
                    $activity->subject_id = current(Hashids::decode($id));
                })
                ->log("Visualizou tela editar produto " . $product->name);

            $categories = $categoryModel->all();

            if (Str::contains($product->photo, "?v=")) {
                $productUrl = Str::before($product->photo, "?v=");
                $product->photo = Http::get($productUrl)->successful() ? $productUrl : "";
            }

            $productService = new ProductService();
            $product->hasSales = $productService->verifyIfProductHasSales(current(Hashids::decode($id)));

            return EditProductResource::make([
                "product" => $product,
                "categories" => $categories,
            ]);
        } catch (Exception $e) {
            report($e);

            return response()->json(["message" => "Produto não encontrado"], 400);
        }
    }

    public function update($id, UpdateProductRequest $request)
    {
        try {
            $data = $request->validated();
            $productModel = new Product();

            $productId = current(Hashids::decode($id));

            if (empty($productId) && empty($data["category"])) {
                return response()->json(
                    [
                        "message" => "Ocorreu um erro produto não encontrado, tente novamente mais tarde",
                    ],
                    400,
                );
            }

            $product = $productModel->find($productId);
            if (!Gate::allows("update", [$product])) {
                return response()->json(["message" => "Sem permissão para atualizar este produto!"], 400);
            }

            $data["name"] = FoxUtils::removeSpecialChars($data["name"]);
            $data["description"] = FoxUtils::removeSpecialChars($data["description"]);

            $data["status_enum"] =
                $data["type_enum"] == "digital" ? $productModel->present()->getStatus("analyzing") : null;

            $data["type_enum"] = $productModel->present()->getType($data["type_enum"]);

            $product->update($data);

            $productPhoto = $request->file("product_photo");

            if ($productPhoto == null && $request->query("product_photo_remove", null) == "true") {
                $product->update(["photo" => null]);
            }

            if ($productPhoto != null) {
                try {
                    if ($product->photo) {
                        $this->getAmazonFileService()->deleteFile($product->photo);
                    }

                    $img = Image::make($productPhoto->getPathname());
                    $img->save($productPhoto->getPathname());

                    $productPath = $this->getAmazonFileService()->uploadFile("uploads/public/products", $productPhoto);

                    $product->update(["photo" => $productPath]);
                } catch (Exception $e) {
                    report($e);

                    return response()->json(["message" => "Ocorreu um erro, tente novamente mais tarde"], 400);
                }
            }

            if (!empty($data["digital_product_url"])) {
                try {
                    $this->getAmazonFileService()->changeDisk("s3_digital_product");
                    $amazonPath = $this->getAmazonFileService()->uploadFile(
                        "products/" . Hashids::encode($product->id),
                        $data["digital_product_url"],
                        null,
                        false,
                        "private",
                    );

                    $product->update(["digital_product_url" => $amazonPath]);
                } catch (Exception $e) {
                    report($e);
                }
            }

            if (!empty($product->shopify_variant_id)) {
                CacheService::forget(CacheService::CHECKOUT_CART_PRODUCT, $product->shopify_variant_id);
            }

            return response()->json(
                [
                    "message" => "Produto Atualizado com sucesso!",
                    "digital_product_url" => $product->digital_product_url,
                ],
                200,
            );
        } catch (Exception $e) {
            report($e);

            return response()->json(["message" => "Ocorreu um erro, tente novamente mais tarde"], 400);
        }
    }

    public function updateProductType($id)
    {
        try {
            $productModel = new Product();
            $productId = current(Hashids::decode($id));

            if (empty($productId) && empty($data["category"])) {
                return response()->json(
                    [
                        "message" => "Ocorreu um erro produto não encontrado, tente novamente mais tarde",
                    ],
                    400,
                );
            }

            $product = $productModel->find($productId);
            if (!Gate::allows("update", [$product])) {
                return response()->json(["message" => "Sem permissão para atualizar este produto!"], 400);
            }

            $data["type_enum"] = $productModel->present()->getType("digital");
            $data["status_enum"] = $productModel->present()->getStatus("analyzing");
            $product->update($data);

            foreach ($product->productsPlans as $productsPlan) {
                $productsPlan->delete();
                $productsPlan->plan->delete();
            }

            return response()->json(["message" => "Produto convertido para digital com sucesso!"], 200);
        } catch (Exception $e) {
            report($e);

            return response()->json(["message" => "Ocorreu um erro, tente novamente mais tarde"], 400);
        }
    }

    public function updateCustom($id, Request $request)
    {
        try {
            $productId = current(Hashids::decode($id));
            $planId = current(Hashids::decode($request->plan));

            $productPlanModel = new ProductPlan();
            $productPlanModel
                ->where("plan_id", $planId)
                ->where("product_id", $productId)
                ->update([
                    "is_custom" => $request->productCustom,
                ]);

            return response()->json(["message" => "Configurações atualizadas com sucesso"], 200);
        } catch (Exception $e) {
            report($e);

            return response()->json(["message" => "Ocorreu um erro, tente novamente mais tarde!"], 400);
        }
    }

    public function destroy($id)
    {
        try {
            $productModel = new Product();
            $productPlanModel = new ProductPlan();

            if (empty($id)) {
                return response()->json(["message" => "Ocorreu um erro, tente novamente mais tarde!"], 400);
            }

            $product = $productModel->find(current(Hashids::decode($id)));

            if (!Gate::allows("destroy", [$product])) {
                return response()->json(["message" => "Ocorreu um erro, tente novamente mais tarde!"], 400);
            }

            $productPlan = $productPlanModel->where("product_id", $product->id)->count();

            if ($productPlan != 0) {
                return response()->json(
                    [
                        "message" => "Impossivel excluir, existem planos associados a este produto!",
                    ],
                    400,
                );
            }

            if (!empty($product->photo)) {
                $this->getAmazonFileService()->deleteFile($product->photo);
            }

            $product->delete();

            return response()->json(["message" => "Produto excluido com sucesso"], 200);
        } catch (Exception $e) {
            report($e);

            return response()->json(["message" => "Ocorreu um erro, tente novamente mais tarde!"], 400);
        }
    }

    public function getProductsVariants(Request $request)
    {
        try {
            $data = $request->all();

            $projectId = current(Hashids::decode($data["project_id"]));
            $products = Product::query();
            $projectModel = new Project();
            $project = $projectModel->find($projectId);

            $products
                ->with("productsPlanSales")
                ->with("productsPlans")
                ->where(
                    "user_id",
                    auth()
                        ->user()
                        ->getAccountOwnerId(),
                );

            if (!empty($projectId) && (!empty($project->shopify_id) || !empty($project->woocommerce_id))) {
                $products->where("project_id", $projectId);

                $groupByVariants = $data["variants"];

                if ($groupByVariants) {
                    $products
                        ->select(
                            "name",
                            DB::raw("min(id) as id"),
                            DB::raw(
                                "if(shopify_id is not null, concat(count(*), ' variantes'), group_concat(description)) as description",
                            ),
                        )
                        ->groupBy("name", "shopify_id", DB::raw("if(shopify_id is null, id, 0)"));
                }
            } else {
                $products->where("shopify", 0)->whereNull("shopify_variant_id");
            }

            if (!empty($data["search"])) {
                $products->where("name", "like", "%" . $data["search"] . "%");
            }

            if (!empty($data["description"])) {
                $products->where("description", "like", "%" . $data["description"] . "%");
            }

            $products = $products
                ->get()
                ->sortByDesc(function ($query) {
                    return $query->productsPlanSales->count();
                })
                ->take(10);

            foreach ($products as $p) {
                $product = Product::find($p->id);
                $p->photo = $product->photo;
                $p->type_enum = $product->type_enum;
                $p->status_enum = $product->status_enum;
                $p->cost = $product->cost;
            }

            return ProductVariantResource::collection($products);
        } catch (Exception $e) {
            Log::warning("Erro ao buscar dados dos produtos (ProductsApiController - getProductsVariants)");
            report($e);

            return response()->json(
                [
                    "message" => "Ocorreu um erro, ao buscar dados dos produtos",
                ],
                400,
            );
        }
    }

    public function getTopSellingProducts(Request $request)
    {
        try {
            $project = $request->input("project") ?? "";
            $product = $request->input("product") ?? "";
            $description = $request->input("description") ?? "";

            if (empty($project)) {
                return response()->json(["message" => "Ocorreu um erro, tente novamente mais tarde"], 400);
            }

            $productService = new ProductService();
            $projectId = current(Hashids::decode($project));

            $products = $productService->getTopSellingProducts($projectId, $product, $description);

            return ProductsSelectResource::collection($products);
        } catch (Exception $e) {
            report($e);

            return response()->json(["message" => "Ocorreu um erro, tente novamente mais tarde"], 400);
        }
    }

    public function getProducts(Request $request)
    {
        try {
            $project = $request->input("project") ?? "";

            if (empty($project)) {
                return response()->json(["message" => "Ocorreu um erro, tente novamente mais tarde"], 400);
            }

            $productService = new ProductService();
            $projectId = current(Hashids::decode($project));

            $products = $productService->getProductsMyProject($projectId);

            return ProductsSelectResource::collection($products);
        } catch (Exception $e) {
            report($e);

            return response()->json(["message" => "Ocorreu um erro, tente novamente mais tarde"], 400);
        }
    }

    public function getProductBySale($saleId)
    {
        try {
            if (empty($saleId)) {
                return response()->json(["message" => "Erro ao tentar obter produtos"], 400);
            }

            $productService = new ProductService();
            $products = $productService->getProductsBySale($saleId);

            return ProductsSaleResource::collection($products);
        } catch (Exception $e) {
            report($e);

            return response()->json(["message" => "Erro ao tentar obter produtos"], 400);
        }
    }

    public function getSignedUrl(Request $request)
    {
        try {
            $requestData = $request->all();

            if (empty($requestData["digital_product_url"])) {
                return response()->json(["message" => "Url não encontrada"], 400);
            }

            $signedUrl = FoxUtils::getAwsSignedUrl(
                $requestData["digital_product_url"],
                $requestData["url_expiration_time"],
            );

            return response()->json(["signed_url" => $signedUrl]);
        } catch (Exception $e) {
            report($e);
            return response()->json(["message" => "Erro ao gerar url assinada do produto"], 400);
        }
    }

    public function verifyProductInPlan(Request $request)
    {
        try {
            $requestData = $request->all();
            $productPlanModel = new ProductPlan();

            $productId = current(Hashids::decode($requestData["product_id"]));

            if (empty($productId)) {
                return response()->json(["message" => "Produto não encontrado"], 400);
            }

            $productInPlan = $productPlanModel->where("product_id", $productId)->exists();

            return response()->json(["product_in_plan" => $productInPlan], 200);
        } catch (Exception $e) {
            report($e);
            return response()->json(["message" => "Erro ao verificar produto"], 400);
        }
    }

    public function verifyProductInPlanSale(Request $request)
    {
        try {
            $requestData = $request->all();
            $productPlanSaleModel = new ProductPlanSale();

            $productId = current(Hashids::decode($requestData["product_id"]));
            $plan_id = current(Hashids::decode($requestData["plan_id"]));

            if (empty($productId)) {
                return response()->json(["message" => "Produto não encontrado"], 400);
            }

            $productInPlanSale = $productPlanSaleModel
                ->where("product_id", $productId)
                ->where("plan_id", $plan_id)
                ->exists();

            return response()->json(["product_in_plan_sale" => $productInPlanSale], 200);
        } catch (Exception $e) {
            report($e);
            return response()->json(["message" => "Erro ao verificar produto"], 400);
        }
    }

    public function getProductById($id, Request $request)
    {
        try {
            $plan_id = $request->input("plan_id");

            $productModel = Product::query();

            if (!empty($plan_id)) {
                $plan_id = current(Hashids::decode($plan_id));

                $products = $productModel
                    ->whereHas("productsPlans", function (Builder $query) use ($plan_id) {
                        $query->where("plan_id", $plan_id);
                    })
                    ->find(current(Hashids::decode($id)));
            } else {
                $products = $productModel->with("productsPlans")->find(current(Hashids::decode($id)));
            }

            return new ProductsSelectResource($products);
        } catch (Exception $e) {
            report($e);
            //return response()->json(['message' => 'Erro ao tentar buscar produto'], 400);
            return response()->json(["message" => $e->getMessage()], 400);
        }
    }
}
