<?php

namespace Modules\PostBack\Http\Controllers;

use App\Entities\Plan;
use App\Entities\Product;
use App\Entities\Project;
use Illuminate\Http\Request;
use App\Entities\PostbackLog;
use App\Entities\ProductPlan;
use App\Entities\UserProject;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Vinkla\Hashids\Facades\Hashids;
use App\Entities\ShopifyIntegration;
use Modules\Core\Services\ShopifyService;

/**
 * Class PostBackShopifyController
 * @package Modules\PostBack\Http\Controllers
 */
class PostBackShopifyController extends Controller
{
    /**
     * @var ShopifyService
     */
    private $shopifyService;

    /**
     * @param string|null $urlStore
     * @param string|null $token
     * @return ShopifyService
     */
    private function getShopifyService(string $urlStore = null, string $token = null)
    {
        if (!$this->shopifyService) {
            $this->shopifyService = new ShopifyService($urlStore, $token);
        }

        return $this->shopifyService;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function postBackListener(Request $request)
    {
        $requestData = $request->all();

        PostbackLog::create([
                                'origin'      => 3,
                                'data'        => json_encode($requestData),
                                'description' => 'shopify',
                            ]);

        $project = Project::find(Hashids::decode($request->project_id)[0]);

        if (!$project) {
            Log::write('error', 'projeto não encontrado no retorno do shopify, projeto = ' . $project->id);
            return 'error';
        }

        foreach ($requestData['variants'] as $variant) {

            $plan = Plan::with('products')->where([
                                                      ['shopify_variant_id', $variant['id']],
                                                      ['project', $project->id],
                                                  ])->first();

            $description = '';
            try {
                $description = $variant['option1'];
                if ($description == 'Default Title') {
                    $description = '';
                }
                if ($variant['option2'] != '') {
                    $description .= ' - ' . $$variant['option2'];
                }
                if ($variant['option3'] != '') {
                    $description .= ' - ' . $$variant['option3'];
                }
            } catch (\Exception $e) {
                //report($e);
            }

            if ($plan) {
                $plan->update([
                                  'name'        => substr($requestData['title'], 0, 100),
                                  'price'       => $variant['price'],
                                  'description' => $description,
                                  'code'        => Hashids::encode($plan->id),
                              ]);

                $product = $plan->getRelation('products')[0];

                try {
                    $shopIntegration = ShopifyIntegration::where('project', $project->id)->first();

                    $shopify = $this->getShopifyService($shopIntegration->url_store, $shopIntegration->token);
                } catch (\Exception $e) {
                    return response()->json(['message' => 'Dados do shopify inválidos, revise os dados informados'], 400);
                }

                $variant = $shopify->getProductVariant($plan->shopify_variant_id);

                $image = $shopify->getImage($variant->getProductId(),$variant->getImageId());

                $product->update([
                    'cost'  => $shopify->getShopInventoryItem($variant->getInventoryItemId())->getCost(),
                    'photo' => $image->getSrc()
                ]);

            } else {
                $userProject = UserProject::where([
                                                      ['project', $project['id']],
                                                      ['type', 'producer'],
                                                  ])->first();

                $product = Product::create([
                                               'user'        => $userProject->user,
                                               'name'        => substr($requestData['title'], 0, 100),
                                               'description' => $description,
                                               'guarantee'   => '0',
                                               'available'   => true,
                                               'amount'      => '0',
                                               'format'      => 1,
                                               'category'    => 11,
                                               'cost'        => '',
                                               'shopify'     => '1',
                                           ]);

                $plan = Plan::create([
                                         'shopify_id'         => $requestData['id'],
                                         'shopify_variant_id' => $variant['id'],
                                         'project'            => $project['id'],
                                         'name'               => substr($requestData['title'], 0, 100),
                                         'description'        => $description,
                                         'price'              => $variant['price'],
                                         'status'             => '1',
                                     ]);

                $plan->update([
                                  'code' => Hashids::encode($plan->id),
                              ]);

                try {
                    $shopIntegration = ShopifyIntegration::where('project', $project->id)->first();

                    $shopify = $this->getShopifyService($shopIntegration->url_store, $shopIntegration->token);
                } catch (\Exception $e) {
                    return response()->json(['message' => 'Dados do shopify inválidos, revise os dados informados'], 400);
                }

                $variant = $shopify->getProductVariant($plan->shopify_variant_id);

                $image = $shopify->getImage($variant->getProductId(),$variant->getImageId());

                $product->update([
                    'cost'  => $shopify->getShopInventoryItem($variant->getInventoryItemId())->getCost(),
                    'photo' => $image->getSrc()
                ]);

                ProductPlan::create([
                                        'product' => $product->id,
                                        'plan'    => $plan->id,
                                        'amount'  => '1',
                                    ]);
            }
        }

        return response()->json(['message' => 'success'], 200);
    }
}
