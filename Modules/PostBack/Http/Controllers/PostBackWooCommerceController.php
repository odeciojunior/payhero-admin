<?php

namespace Modules\PostBack\Http\Controllers;

// use App\Jobs\ProcessWooCommercePostbackJob;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Core\Entities\Plan;
use Modules\Core\Entities\PostbackLog;
use Modules\Core\Entities\Product;
use Modules\Core\Entities\Project;
use Modules\Core\Entities\UserProject;
use Modules\Core\Entities\WooCommerceIntegration;
use Modules\Core\Services\WooCommerceService;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Support\Facades\Log;

/**
 * Class PostBackWooCommerceController
 * @package Modules\PostBack\Http\Controllers
 */
class PostBackWooCommerceController extends Controller
{
    public function postBackProductCreate(Request $request)
    {
        
        if (empty($request->project_id)) {
            return response()->json(
                [
                    'message' => 'success',
                ],
                200
            );
        }
        
        $projectId = current(Hashids::decode($request->project_id));
        $wooCommerceIntegration = WooCommerceIntegration::where('project_id', $projectId)->first();
        
        $product = (object)$request;

        
        if (empty($wooCommerceIntegration)) {
            return response()->json(
                [
                    'message' => 'fail',
                ],
                200
            );
        }

        if (empty($product->name)) {
            return response()->json(
                [
                    'message' => 'fail',
                ],
                200
            );
        }
        
        $description = '';
        if (!empty($product['attributes'])) {
            foreach ($product['attributes'] as $attribute) {
                $description .= $attribute['option'] . ' ';
            }
        }

        $user = UserProject::with('user')
                ->where('type_enum', UserProject::TYPE_PRODUCER_ENUM)
                ->where('project_id', hashids_decode($request->project_id))
                ->first()->user;

        $tmpSku = ($product->parent_id?$product->parent_id:$product->id).'-'.$request->project_id.'-'.str_replace(' ','',strtoupper($description));

        $ifProductExists = Product::where("user_id", $user->id)
                ->where('shopify_variant_id', $tmpSku)
                ->first();

        if(!empty($ifProductExists)){
            return response()->json(
                [
                    'message' => 'product already exists',
                ],
                200
            );
        }
                


        $wooCommerceService = new WooCommerceService(
            $wooCommerceIntegration->url_store,
            $wooCommerceIntegration->token_user,
            $wooCommerceIntegration->token_pass
        );
        $wooCommerceService->verifyPermissions();


        $variationId = !empty($product->parent_id) ? $product->id : null;

        $sku = $wooCommerceService->createProduct(
            $wooCommerceIntegration->project_id,
            $wooCommerceIntegration->user_id,
            $product,
            $description,
            $variationId
        );

        $data = [
            'sku' => $sku
        ];
        if (empty($product->parent_id)) {
            $wooCommerceService->woocommerce->put('products/' . $product->id, $data);
        } else {
            $wooCommerceService->woocommerce->put(
                'products/' . $product->parent_id . '/variations/' . $product->id,
                $data
            );
        }


        return response()->json(
            [
                'message' => 'success',
            ],
            200
        );
    }

    public function postBackProductUpdate(Request $request)
    {
               
        if (empty($request->project_id) || empty($request['sku'])) {
            return response()->json(
                [
                    'message' => 'invalid data',
                ],
                200
            );
        }

        if (empty($request['variations'])) {
            if (!empty($request['name'])) {
                $newValues['name'] = $request['name'];
            }

            if (!empty($request['price'])) {
                $newValues['price'] = $request['price'];
            }

            if (!empty($request['images'][0]['src'])) {
                $newValues['photo'] = $request['images'][0]['src'];
            }

            $user = UserProject::with('user')
                ->where('type_enum', UserProject::TYPE_PRODUCER_ENUM)
                ->where('project_id', hashids_decode($request->project_id))
                ->first()->user;


            Product::where("user_id", $user->id)
                ->where('shopify_variant_id', $request['sku'])
                ->first()
                ->update($newValues);

                
            unset($newValues['photo']);
                
            Plan::where('project_id', hashids_decode($request->project_id))
                ->where('shopify_variant_id', $request['sku'])
                ->first()
                ->update($newValues);
            
            
        }

        return response()->json(
            [
                'message' => 'success',
            ],
            200
        );
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function postBackTracking(Request $request)
    {
        try {
            $postBackLogModel = new PostbackLog();
            $projectModel = new Project();

            $requestData = $request->all();

            $postBackLogModel->create(
                [
                    'origin' => 5,
                    'data' => json_encode($requestData),
                    'description' => 'woocommerce-tracking',
                ]
            );

            $projectId = current(Hashids::decode($request->project_id));
            //$projectId = $request->project_id;
            $project = $projectModel->find($projectId);

            if (!empty($project)) {
                //Log::debug($requestData);

                // ProcessWooCommercePostbackJob::dispatch($projectId, $requestData)
                //     ->onQueue('high');

                return response()->json(
                    [
                        'message' => 'success',
                    ],
                    200
                );
            } else {
                //Log::warning('WooCommerce atualizar código de rastreio - projeto não encontrado');

                //projeto nao existe
                return response()->json(
                    [
                        'message' => 'project not found',
                    ],
                    200
                );
            }
        } catch (\Exception $e) {
            return response()->json(
                [
                    'message' => 'error processing postback',
                ],
                200
            );
        }
    }


}


