<?php

namespace Modules\Shopify\Http\Controllers;

use Exception;
use Modules\Core\Services\DigitalOceanFileService;
use Modules\Core\Services\ShopifyService;
use PHPHtmlParser\Dom;
use App\Entities\Plan;
use App\Entities\Company;
use App\Entities\Product;
use App\Entities\Project;
use Slince\Shopify\Client;
use PHPHtmlParser\Dom\Tag;
use Illuminate\Http\Request;
use App\Entities\ProductPlan;
use App\Entities\UserProject;
use Illuminate\Http\Response;
use Cloudflare\API\Auth\APIKey;
use PHPHtmlParser\Dom\HtmlNode;
use PHPHtmlParser\Dom\TextNode;
use Cloudflare\API\Endpoints\DNS;
use Illuminate\Routing\Controller;
use Cloudflare\API\Adapter\Guzzle;
use PHPHtmlParser\Selector\Parser;
use Cloudflare\API\Endpoints\Zones;
use Illuminate\Support\Facades\Log;
use Vinkla\Hashids\Facades\Hashids;
use PHPHtmlParser\Selector\Selector;
use App\Entities\ShopifyIntegration;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Slince\Shopify\PublicAppCredential;
use Modules\Core\Helpers\CaminhoArquivosHelper;

class ShopifyController extends Controller
{
    /**
     * @var DigitalOceanFileService
     */
    private $digitalOceanFileService;
    /**
     * @var ShopifyService
     */
    private $shopifyService;
    /**
     * @var Project
     */
    private $projectModel;
    /**
     * @var ShopifyIntegration
     */
    private $shopifyIntegrationModel;
    /**
     * @var UserProject
     */
    private $userProjectModel;
    /**
     * @var Product
     */
    private $productModel;
    /**
     * @var Plan
     */
    private $planModel;
    /**
     * @var ProductPlan
     */
    private $productPlanModel;

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
     * @return ShopifyIntegration|\Illuminate\Contracts\Foundation\Application|mixed
     */
    private function getShopifyIntegrationModel()
    {
        if (!$this->shopifyIntegrationModel) {
            $this->shopifyIntegrationModel = app(ShopifyIntegration::class);
        }

        return $this->shopifyIntegrationModel;
    }

    /**
     * @return Project|\Illuminate\Contracts\Foundation\Application|mixed
     */
    private function getProjectModel()
    {
        if (!$this->projectModel) {
            $this->projectModel = app(Project::class);
        }

        return $this->projectModel;
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|mixed
     */
    private function getPlanModel()
    {
        if (!$this->planModel) {
            $this->planModel = app(Plan::class);
        }

        return $this->planModel;
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|mixed
     */
    private function getProductPlanModel()
    {
        if (!$this->productPlanModel) {
            $this->productPlanModel = app(ProductPlan::class);
        }

        return $this->productPlanModel;
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|mixed
     */
    private function getProductModel()
    {
        if (!$this->productModel) {
            $this->productModel = app(Product::class);
        }

        return $this->productModel;
    }

    /**
     * @return UserProject|\Illuminate\Contracts\Foundation\Application|mixed
     */
    private function getUserProjectModel()
    {
        if (!$this->userProjectModel) {
            $this->userProjectModel = app(UserProject::class);
        }

        return $this->userProjectModel;
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|mixed|ShopifyService
     */
    private function getShopifyService(string $urlStore = null, string $token = null)
    {
        if (!$this->shopifyService) {
            $this->shopifyService = new ShopifyService($urlStore, $token);
        }

        return $this->shopifyService;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $companies = Company::where('user_id', \Auth::user()->id)->get()->toArray();

        $shopifyIntegrations = ShopifyIntegration::where('user', \Auth::user()->id)->get()->toArray();

        $projects = [];

        foreach ($shopifyIntegrations as $shopifyIntegration) {

            $project = Project::find($shopifyIntegration['project']);

            if ($project) {
                $projects[] = $project;
            }
        }

        return view('shopify::index', [
            'companies' => $companies,
            'projects'  => $projects,
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function adicionarIntegracao(Request $request)
    {
        try {
            $dados = $request->all();

            $shopifyIntegration = $this->getShopifyIntegrationModel()
                                       ->where('token', $dados['token'])
                                       ->first();

            if (!$shopifyIntegration) {

                try {

                    $shopify = $this->getShopifyService($dados['url_store'] . '.myshopify.com', $dados['token']);
                } catch (\Exception $e) {
                    return response()->json(['message' => 'Dados do shopify inválidos, revise os dados informados'], 400);
                }

                try {

                    $shopifyName = $shopify->getShopName();
                    $project     = $this->getProjectModel()->create([
                                                                        'name'                  => $shopifyName,
                                                                        'status'                => $this->getProjectModel()
                                                                                                        ->getEnum('status', 'approved'),
                                                                        'visibility'            => 'private',
                                                                        'percentage_affiliates' => '0',
                                                                        'description'           => $shopifyName,
                                                                        'invoice_description'   => $shopifyName,
                                                                        'url_page'              => 'https://' . $shopify->getShopDomain(),
                                                                        'automatic_affiliation' => false,
                                                                        'shopify_id'            => $shopify->getShopId(),
                                                                    ]);
                } catch (\Exception $e) {
                    return response()->json(['message' => 'Dados do shopify inválidos, revise os dados informados'], 400);
                }

                $shopifyIntegration = $this->getShopifyIntegrationModel()->create([
                                                                                      'token'     => $dados['token'],
                                                                                      'url_store' => $dados['url_store'] . '.myshopify.com',
                                                                                      'user'      => auth()->user()->id,
                                                                                      'project'   => $project->id,
                                                                                  ]);

                $photo = $request->file('photo');

                if ($photo) {

                    try {
                        $img = Image::make($photo->getPathname());
                        $img->crop($dados['photo_w'], $dados['photo_h'], $dados['photo_x1'], $dados['photo_y1']);
                        $img->resize(200, 200);
                        $img->save($photo->getPathname());
 
                        $digitalOceanPath = $this->getDigitalOceanFileService()
                                                 ->uploadFile('uploads/user/' . Hashids::encode(auth()->user()->id) . '/public/projects/' . $project->id_code . '/main', $photo);

                        $project->update([
                                             'photo' => $digitalOceanPath,
                                         ]);
                    } catch (\Exception $e) {
                        // não cadastra imagem
                    }
                }

                $this->getUserProjectModel()->create([
                                                         'user'                 => auth()->user()->id,
                                                         'project'              => $project->id,
                                                         'company'              => $dados['company'],
                                                         'type'                 => 'producer',
                                                         'shipment_responsible' => true,
                                                         'permissao_acesso'     => true,
                                                         'permissao_editar'     => true,
                                                         'status'               => 'active',
                                                     ]);

                $products = $shopify->getShopProducts();

                foreach ($products as $shopifyProduct) {

                    foreach ($shopifyProduct->getVariants() as $variant) {

                        $description = '';

                        try {
                            $description = $variant->getOption1();
                            if ($description == 'Default Title') {
                                $description = '';
                            }
                            if ($variant->getOption2() != '') {
                                $description .= ' - ' . $variant->getOption2();
                            }
                            if ($variant->getOption3() != '') {
                                $description .= ' - ' . $variant->getOption3();
                            }
                        } catch (\Exception $e) {
                            //
                        }

                        $product = $this->getProductModel()->create([
                                                                        'user'        => auth()->user()->id,
                                                                        'name'        => substr($shopifyProduct->getTitle(), 0, 100),
                                                                        'description' => $description,
                                                                        'guarantee'   => '0',
                                                                        'format'      => 1,
                                                                        'category'    => '11',
                                                                        'cost'        => $shopify->getShopInventoryItem($variant->getInventoryItemId())
                                                                                                 ->getCost(),
                                                                        'shopify'     => true,
                                                                        'price'       => '',
                                                                    ]);

                        $plan = $this->getPlanModel()->create([
                                                                  'shopify_id'         => $shopifyProduct->getId(),
                                                                  'shopify_variant_id' => $variant->getId(),
                                                                  'project'            => $project->id,
                                                                  'name'               => substr($shopifyProduct->getTitle(), 0, 100),
                                                                  'description'        => $description,
                                                                  'code'               => '',
                                                                  'price'              => $variant->getPrice(),
                                                                  'status'             => '1',
                                                              ]);

                        $plan->update([
                            'code' => Hashids::encode($plan->id)
                        ]);

                        if (count($shopifyProduct->getVariants()) > 1) {
                            foreach ($shopifyProduct->getImages() as $image) {
 
                                foreach ($image->getVariantIds() as $variantId) {
                                    if ($variantId == $variant->getId()) {

                                        if ($image->getSrc() != '') { 
                                            $product->update([
                                                                 'photo' => $image->getSrc(),
                                                             ]);
                                        } else { 
 
                                            $product->update([
                                                                 'photo' => $shopifyProduct->getImage()->getSrc(),
                                                             ]);
                                        }
                                    }
                                }
                            }
                        } else {

                            $product->update([
                                                 'photo' => $shopifyProduct->getImage()->getSrc(),
                                             ]);
                        }

                        $this->getProductPlanModel()->create([
                                                                 'product' => $product->id,
                                                                 'plan'    => $plan->id,
                                                                 'amount'  => '1',
                                                             ]);
                    }
                }

                $shopify->createShopWebhook([
                                                "topic"   => "products/create",
                                                "address" => "https://app.cloudfox.net/postback/shopify/" . Hashids::encode($project['id']),
                                                "format"  => "json",
                                            ]);

                $shopify->createShopWebhook([
                                                "topic"   => "products/update",
                                                "address" => "https://app.cloudfox.net/postback/shopify/" . Hashids::encode($project['id']),
                                                "format"  => "json",
                                            ]);

                return response()->json(['message' => 'Integração adicionada!'], 200);
            } else {
                return response()->json(['message' => 'Projeto já integrado'], 400);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'Problema ao criar integração, tente novamente mais tarde'], 400);
        }
    }

}

