<?php

namespace Modules\PostBack\Http\Controllers;

// use App\Jobs\ProcessWooCommercePostbackJob;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Laracasts\Presenter\Exceptions\PresenterException;
use Modules\Core\Entities\PostbackLog;
use Modules\Core\Entities\Project;
use Modules\Core\Entities\Product;
use Modules\Core\Entities\Plan;
use Modules\Core\Entities\WooCommerceIntegration;
use Modules\Core\Entities\UserProject;
use Modules\Core\Services\WebhookService;
use Vinkla\Hashids\Facades\Hashids;
use Modules\Core\Services\WooCommerceService;

/**
 * Class PostBackWooCommerceController
 * @package Modules\PostBack\Http\Controllers
 */
class PostBackWooCommerceController extends Controller
{   
    public function postBackProductCreate(Request $request)
    {
        $projectId = current(Hashids::decode($request->project_id));
        $wooCommerceIntegration = WooCommerceIntegration::where('project_id',$projectId)->first();
        
        $product = (object)$request;

        $description = '';
        if(!empty($product['attributes'])){
           
            foreach($product['attributes'] as $attribute){
                $description .= $attribute['option'].' ';
            }
            
        }
        

        $wooCommerceService = new WooCommerceService(
            $wooCommerceIntegration->url_store,
            $wooCommerceIntegration->token_user,
            $wooCommerceIntegration->token_pass
        );
        $wooCommerceService->verifyPermissions();

        
        $variationId = !empty($product->parent_id)?$product->id:null;
        
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
        if(empty($product->parent_id)){

            $wooCommerceService->woocommerce->put('products/'.$product->id, $data);
        }else{
            $wooCommerceService->woocommerce->put('products/'.$product->parent_id.'/variations/'.$product->id, $data);

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
        

        if(empty($request['variations'])){
            $newValues = [
                'name'=>$request['name'],
                'price'=>$request['price'],
                'photo'=>$request['images'][0]['src']

            ];
            Product::where('shopify_variant_id', $request['sku'])
            ->update($newValues);
            
            unset($newValues['photo']);
            
            Plan::where('shopify_variant_id', $request['sku'])
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


