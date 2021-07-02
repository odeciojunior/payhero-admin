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
use Modules\Core\Entities\Sale;
use App\Jobs\ProcessWooCommercePostbackTracking;
use App\Jobs\ProcessWooCommerceOrderNotes;

use function GuzzleHttp\json_decode;

/**
 * Class PostBackWooCommerceController
 * @package Modules\PostBack\Http\Controllers
 */
class PostBackWooCommerceController extends Controller
{
    public function postBackProductCreate(Request $request)
    {

        
        return $this->createProduct($request);
    }

    public function createProduct($request){

        if (empty($request->project_id) || !empty($request->variations)) {
            return response()->json(
                [
                    'message' => 'invalid data',
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
                    'message' => 'process fail',
                ],
                200
            );
        }

        if (empty($product->name) || empty($product->id) || empty($product->price) ) {
            return response()->json(
                [
                    'message' => 'invalid data',
                ],
                200
            );
        }

        $description = '';
        if (!empty($product['attributes'])) {
            foreach ($product['attributes'] as $attribute) {
                if(!empty($attribute['option']))
                    $description .= $attribute['option'] . ' ';
            }
        }

        $user = UserProject::with('user')
                ->where('type_enum', UserProject::TYPE_PRODUCER_ENUM)
                ->where('project_id', hashids_decode($request->project_id))
                ->first()->user;

        $tmpSku = ($product->parent_id?$product->parent_id:$product->id).'-'.$request->project_id.'-'.str_replace(' ','',strtoupper($description));

        $ifProductExists = Product::where("user_id", $user->id)
                ->where('project_id', hashids_decode($request->project_id))
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
        
        $variationId = !empty($product->parent_id) ? $product->id : null;

        $sku = $wooCommerceService->createProduct(
            $wooCommerceIntegration->project_id,
            $wooCommerceIntegration->user_id,
            $product,
            $description,
            $variationId
        );

        if(!empty($sku)){

            $data = [
                'sku' => $sku
            ];
            if (empty($product->parent_id) && empty($product->variations)) {
                $wooCommerceService->woocommerce->put('products/' . $product->id, $data);
            } else {
                $wooCommerceService->woocommerce->put(
                    'products/' . $product->parent_id . '/variations/' . $product->id,
                    $data
                );
            }

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

        if (empty($request->project_id)) {
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

            
            $productExists = Product::where('shopify_variant_id', $request['sku'])
                ->where('project_id', hashids_decode($request->project_id))
                ->where("user_id", $user->id)
                ->first();

            if(!empty($productExists)){

                Product::where("user_id", $user->id)
                    ->where('shopify_variant_id', $request['sku'])
                    ->first()
                    ->update($newValues);


                unset($newValues['photo']);

                $planExists = Plan::where('project_id', hashids_decode($request->project_id))
                    ->where('shopify_variant_id', $request['sku'])
                    ->first();
                
                if(!empty($planExists)){
                    $planExists->update($newValues);
                }else{
                    $newValues['shopify_variant_id'] = $request['sku'];
                    
                    if(!empty($request['parent_id'])){
                        $newValues['shopify_id'] = $request['parent_id'];
                    }
                    
                    $newValues['project_id'] = hashids_decode($request->project_id);

                    $description = '';

                    if(!empty($request['attributes'])){
                        foreach($request['attributes'] as $attribute){
                            $description .= $attribute['option'].' ';
                        }
                    }
                    $newValues['description'] = $description;
                    
                    $newValues['code'] = '';
                    $newValues['status'] = '1';
                    
                    Plan::create($newValues);
                }

            }else{
                
                // Criar produto
                return $this->createProduct($request);

                
            }


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
            
            
            if (empty($request->shipping['company']) ){
                return response()->json(
                    [
                        'message' => 'invalid data',
                    ],
                    200
                );
            }

            $projectModel = new Project();


            $projectId = current(Hashids::decode($request->project_id));

            $project = $projectModel->find($projectId)->first();

            if (!empty($project)){


                foreach($request->line_items as $item){
                    $line_items[] = [
                        'sku'=> $item['sku'],
                        'name'=> $item['name'],
                        'quantity'=> $item['quantity'],
                    ];
                }


                if(!empty($request->correios_tracking_code)) {
                    
                    
                    $data = [
                        'id'=>$request->id,
                        'correios_tracking_code' => $request->correios_tracking_code,
                        'line_items' => $line_items
                    ];
                    
                    ProcessWooCommercePostbackTracking::dispatch($projectId, $data);
    

                }else{

                    $data = [
                        'id'=>$request->id,
                        'correios_tracking_code' => '?',
                        'line_items' => $line_items
                    ];

                    // Process Aliexpress trackingcodes on notes
                    ProcessWooCommerceOrderNotes::dispatch($projectId, $data);

                }

                return response()->json(
                    [
                        'message' => 'success',
                    ],
                    200
                );


            } else {

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


