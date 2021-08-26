<?php

namespace Modules\Core\Services;

use Exception;
use Illuminate\Support\Facades\Log;
use Laracasts\Presenter\Exceptions\PresenterException;
use Modules\Core\Entities\Plan;
use Modules\Core\Entities\Product;
use Modules\Core\Entities\ProductPlan;
use Modules\Core\Entities\Project;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\User;
use PHPHtmlParser\Dom;
use PHPHtmlParser\Dom\HtmlNode;
use PHPHtmlParser\Dom\TextNode;
use PHPHtmlParser\Exceptions\ChildNotFoundException;
use PHPHtmlParser\Exceptions\CircularException;
use PHPHtmlParser\Exceptions\CurlException;
use PHPHtmlParser\Exceptions\LogicalException;
use PHPHtmlParser\Exceptions\NotLoadedException;
use PHPHtmlParser\Exceptions\StrictException;
use PHPHtmlParser\Exceptions\UnknownChildTypeException;
use PHPHtmlParser\Selector\Parser;
use PHPHtmlParser\Selector\Selector;
use Vinkla\Hashids\Facades\Hashids;
use App\Jobs\ImportWooCommerceProduct;
use App\Jobs\ImportWooCommerceProductVariation;
use App\Jobs\CreateWooCommerceWebhooks;
use App\Jobs\ImportWooCommerceProducts;
use Automattic\WooCommerce\Client;
use Modules\Core\Entities\WooCommerceIntegration;
use App\Jobs\ProcessWooCommercePostbackTracking;
use App\Jobs\ImportWooCommerceOrders;
use App\Jobs\ProcessWooCommerceOrderNotes;
use App\Jobs\ProcessWooCommerceSaveProductSku;

class WooCommerceService
{

    private $project = "admin";
    private $url;
    private $user;
    private $pass;
    private $endPoint = "/wp-json/wc/v3/";
    public $woocommerce;

    /**
     * constructor.
     * @param string $urlStore
     * @param string $token
     */
    public function __construct(string $urlStore, string $tokenUser, string $tokenPass)
    {
        $this->url = $urlStore;
        $this->user = $tokenUser;
        $this->pass = $tokenPass;

        if(!$this->woocommerce){
            $this->verifyPermissions();
        }
    }

    public function test_url()
    {

        $file = $this->url;
        $file_headers = @get_headers($file);
        if(!$file_headers || $file_headers[0] == 'HTTP/1.1 404 Not Found') {
            $exists = false;
        }
        else {
            $exists = true;
        }
        return $exists;
    }

    public function verifyPermissions($testWrite=null)
    {
        try{
            $this->woocommerce = new Client(
                $this->url,
                $this->user,
                $this->pass,
                [
                    'version' => 'wc/v3',
                ]
            );

            $product = $this->woocommerce->get('products', ['per_page'=>1]);

            //write test
            if($testWrite){

                if(!empty($product)){
    
                    $data = [
                        'name' => $product[0]->name
                    ];
    
                    $this->woocommerce->post('products/'.$product[0]->id, $data);
    
                    return true;
    
    
                }else{
    
                    return false;
    
                }
            }else{
                return true;
            }


        }catch(Exception $e){

            //report($e);

            return false;
        }
    }

    public function fetchProducts($projectId, $userId)
    {
        // Will loop through pages until it finishes;
        $page = 1;

        ImportWooCommerceProducts::dispatch($projectId, $userId, $page);
        
    }

    public function importProducts($projectId, $userId, $products)
    {
        
        foreach($products as $_product){

            if($_product->status != 'publish') continue;

            ImportWooCommerceProduct::dispatch($projectId, $userId, $_product);

        }

        return;

    }

    public function importProduct($projectId, $userId, $_product)
    {
        try{

            $hashedProjectId = Hashids::encode($projectId);


            $description = '';
            if(empty($_product->variations)){

                $this->createProduct($projectId, $userId, $_product, $description);

                $data = [
                        'sku' => $_product->id.'-'.$hashedProjectId.'-'
                    ];
                    $this->woocommerce->post('products/'.$_product->id, $data);
                    
                


            }else{

                $variations = $this->woocommerce->get('products/'.$_product->id.'/variations');

                foreach($variations as $variation){

                    ImportWooCommerceProductVariation::dispatch($projectId, $userId, $_product, $variation);

                }

            }

        }catch(Exception $e){
            //Log::debug($e);
            report($e);
        }
    }

    public function importProductVariation($variation, $_product, $projectId, $userId)
    {
        $hashedProjectId = Hashids::encode($projectId);

        $description = '';

        foreach($variation->attributes as $attribute){
            $description .= $attribute->option.' ';
        }


        $_product->price = $variation->price;
        $_product->images[0]->src = $variation->image->src;

        $this->createProduct($projectId, $userId, $_product, $description, $variation->id);

        

        $data = [
            'sku' => $_product->id.'-'.$hashedProjectId.'-'.str_replace(' ','',strtoupper($description))
        ];
        
        ProcessWooCommerceSaveProductSku::dispatch($projectId, $_product->id, $variation->id, $data, 3);
            
          


    }

    public function createProduct($projectId, $userId, $_product, $description, $variationId = null)
    {

        $hashedProjectId = Hashids::encode($projectId);

        $planModel = new Plan();

        $productModel = new Product();

        $productPlanModel = new ProductPlan();

        $shopifyVariantId = ($_product->parent_id?$_product->parent_id:$_product->id).'-'.$hashedProjectId.'-'.str_replace(' ','',strtoupper($description));

        $_product->price = empty($_product->price)?1:$_product->price;

        $productExists = $productModel
            ->where('project_id', $projectId)
            ->where('shopify_variant_id', $shopifyVariantId)->first();

        if(!empty($productExists)){
            
            $newValues = false;

            //sync product
            if($productExists->name != $_product->name){
                $productExists->name = $_product->name;
                $newValues = true;
            }
            
            if($productExists->description != mb_substr($description, 0, 100) ){
                $productExists->description = mb_substr($description, 0, 100);
                $newValues = true;
            }

            if($productExists->price != $_product->price){
                $productExists->price = $_product->price;
                $newValues = true;
            }
            
            if(!empty($_product->images)){
    
                if(gettype($_product->images[0])=='array'){
                    $src = $_product->images[0]['src'];
                }else{
                    $src = $_product->images[0]->src;
                }
                
                if($productExists->photo != $src){            
                    $productExists->photo = $src;
                    $newValues = true;
                }
            }

            
            if($newValues == true){
                $productExists->save();
            }

            //sync plan
            $newValues = false;

            $planExists = $planModel->where('shopify_variant_id', $shopifyVariantId)->first();
            
            if(!empty($planExists)){

                if($planExists->name != $_product->name){
                    $planExists->name = $_product->name;
                    $newValues = true;
                }
                
                if($planExists->description != mb_substr($description, 0, 100) ){
                    $planExists->description = mb_substr($description, 0, 100);
                    $newValues = true;
                }
                
                if($planExists->price != $_product->price){
                    $planExists->price = $_product->price;
                    $newValues = true;
                }
                
                if($newValues == true){
                    $planExists->save();
                }
            }

            return false;


        }else{

            $product = $productModel->create(
                [
                    'user_id' => $userId,
                    'name' => $_product->name,
                    'description' => mb_substr($description, 0, 100),
                    'guarantee' => '0',
                    'format' => 1,
                    'category_id' => '11',
    
                    'price' => $_product->price,
                    'shopify_id' => $variationId,
                    'shopify_variant_id' => $shopifyVariantId,
                    'sku' => $_product->sku,
                    'project_id' => $projectId,
                ]
            );
    
            
            $plan = $planModel->create(
                [
                    'shopify_id' => $variationId,
                    'shopify_variant_id' => $shopifyVariantId,
                    'project_id' => $projectId,
                    'name' => $_product->name,
                    'description' => mb_substr($description, 0, 100),
                    'code' => '',
                    'price' => $_product->price,
                    'status' => '1',
                ]
            );
            $plan->update(['code' => Hashids::encode($plan->id)]);
    
            $dataProductPlan = [
                'product_id' => $product->id,
                'plan_id' => $plan->id,
                'amount' => '1',
            ];
    
    
            $productPlanModel->create($dataProductPlan);
    
            if(!empty($_product->images)){
    
                if(gettype($_product->images[0])=='array'){
                    $src = $_product->images[0]['src'];
                }else{
                    $src = $_product->images[0]->src;
                }
                $product->update(['photo' => $src]);
            }

            return $shopifyVariantId;
        }

        

    }


    public function cancelOrder($sale, $note = null)
    {
        try {

            $data = [
                'status' => 'cancelled'
            ];

            $this->woocommerce->post('orders/'.$sale->woocommerce_order, $data);

            if(!empty($note)){
                $data = [
                    'note' => $note
                ];

                $this->woocommerce->post('orders/'.$sale->woocommerce_order.'/notes', $data);
            }

        } catch (Exception $e) {
            report($e);
        }
    }

    public function createHooks($projectId)
    {
        
        $decodedProjectId = Hashids::decode($projectId);

        //Order update.
        $data = [
            'name' => "cf-".$projectId,
            'topic' => 'order.updated',
            'delivery_url' => env('APP_URL').'/postback/woocommerce/'.$projectId.'/tracking'
        ];

        CreateWooCommerceWebhooks::dispatch($decodedProjectId, $data);


        //Product update
        $data = [
            'name' => "cf-".$projectId,
            'topic' => 'product.updated',
            'delivery_url' => env('APP_URL').'/postback/woocommerce/'.$projectId.'/product/update'
        ];

        CreateWooCommerceWebhooks::dispatch($decodedProjectId, $data);


        //Product create
        $data = [
            'name' => "cf-".$projectId,
            'topic' => 'product.created',
            'delivery_url' => env('APP_URL').'/postback/woocommerce/'.$projectId.'/product/create'
        ];

        CreateWooCommerceWebhooks::dispatch($decodedProjectId, $data);

    }

    public function deleteHooks($projectId=null, $anyCloudFoxProject=null)
    {
        $hashedProjectId = Hashids::encode($projectId);

        $webhooks = $this->woocommerce->get('webhooks');
        $ids = array();
        
        
        foreach($webhooks as $webhook){

            if($webhook->name == 'cf-'.$hashedProjectId){
        

                $ids[] = $webhook->id;

            }
            
            if($anyCloudFoxProject && strpos($webhook->name, 'cf-') === 0){

                $ids[] = $webhook->id;

            }
        }

        if(!empty($ids)){
            
            $data = [
                'delete' => $ids
            ];
            
            $this->woocommerce->post('webhooks/batch', $data);
        }


    }

    

    public function commitSyncProducts($projectId, $integration, $doProducts, $doTrackingCodes, $doWebhooks){       

        //starts to sync, freezes this action for 45 minutes 

        $integration->synced_at = now();
        $integration->save();

        $this->url = $integration->url_store;
        $this->user = $integration->token_user;
        $this->pass = $integration->token_pass;
        $this->verifyPermissions();
        
        if($doWebhooks == 'true'){
            $this->deleteHooks($projectId, true);
    
            $hashedProjectId = Hashids::encode($projectId);
    
            $this->createHooks($hashedProjectId);
        }

        if($doProducts == 'true'){
            
            
            $this->fetchProducts($projectId, $integration->user_id);
        }

        if($doTrackingCodes == 'true'){
            $this->fetchTrackingCodes($integration);
        }
        
    }

    public function fetchTrackingCodes($integration)
    {
        ImportWooCommerceOrders::dispatch($integration->project_id, $integration->user_id, 1);
    }

    public function importTrackingCodes($projectId, $orders)
    {
        
        foreach($orders as $order){

            $data = array();
            foreach($order->line_items as $item){
                $line_items[] = [
                    'sku'=> $item->sku,
                    'name'=> $item->name,
                    'quantity'=> $item->quantity,
                ];
            }

            if(!empty($order->correios_tracking_code)){
                
                
                $data = [
                    'id'=>$order->id,
                    'correios_tracking_code' => $order->correios_tracking_code,
                    'line_items' => $line_items
                ];
                
                ProcessWooCommercePostbackTracking::dispatch($projectId, $data);
                
            }else{

                // Check the notes for aliexpress codes
                $data = [
                    'id'=>$order->id,
                    'correios_tracking_code' => '?',
                    'line_items' => $line_items
                ];
                ProcessWooCommerceOrderNotes::dispatch($projectId, $data);

            }
        }
        return;
    }

    public function syncProducts($projectId, $integration, $doProducts, $doTrackingCodes, $doWebhooks)
    {
        
        if(empty($integration->synced_at)){

            $this->commitSyncProducts($projectId, $integration, $doProducts, $doTrackingCodes, $doWebhooks);

            return '{"status":true,"msg":""}';

        }else{
            $start_date = strtotime($integration->synced_at);
            $diff = (time() - $start_date) / 60;

            if($diff < 45){

                return '{"status":false,"msg":""}';
                // ! 

            }else{

                $this->commitSyncProducts($projectId, $integration, $doProducts, $doTrackingCodes, $doWebhooks);

                return '{"status":true,"msg":""}';

            }
            
        }


    }

}


