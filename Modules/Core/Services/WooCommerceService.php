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


use Automattic\WooCommerce\Client;


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
    }

    public function test_url()
    {
        $file = 'http://'.$this->url;
        $file_headers = @get_headers($file);
        if(!$file_headers || $file_headers[0] == 'HTTP/1.1 404 Not Found') {
            $exists = false;
        }
        else {
            $exists = true;
        }
        return $exists;
    }

    public function verifyPermissions()
    {
        try{
            $this->woocommerce = new Client(
                'http://'.$this->url, 
                $this->user, 
                $this->pass,
                [
                    'version' => 'wc/v3',
                ]
            );
            $this->woocommerce->get('products', ['per_page'=>1]);
            
            return true;
        }catch(Exception $e){
            Log::debug($e);
            return false;
        }
    }

    public function fetchProducts()
    {
        $loop = true;
        $page = 1;
        $products = array();
        while($loop){
            $result = $this->woocommerce->get('products', ['status'=>'publish', 'page'=> $page]);
            
            if(empty($result)){
                $loop = false;
            }else{
                $products = array_merge($products, $result);
                $page++;
            }
        }
        
        return $products;
    }
    
    public function importProducts($projectId, $userId, $products)
    {   
        $createdProdcts = 0;

        foreach($products as $_product){

            if($_product->status != 'publish') continue;

            $hashedProjectId = Hashids::encode($projectId);
            

            $description = '';
            if(empty($_product->variations)){
                $this->createProduct($projectId, $userId, $_product, $description);
                $data = [
                    'sku' => $_product->id.'-'.$hashedProjectId.'-'
                ];
                $this->woocommerce->put('products/'.$_product->id, $data);

            }else{
                
                $variations = $this->woocommerce->get('products/'.$_product->id.'/variations');
                
                foreach($variations as $variation){

                    

                    foreach($variation->attributes as $attribute){
                        $description .= $attribute->option.' ';
                    }
                    
                    
                    $_product->price = $variation->price;
                    $_product->images[0]->src = $variation->image->src;
                    
                    $this->createProduct($projectId, $userId, $_product, $description, $variation->id);
                    
                    $data = [
                        'sku' => $_product->id.'-'.$hashedProjectId.'-'.str_replace(' ','',strtoupper($description))
                    ];
                    
                    $this->woocommerce->put('products/'.$_product->id.'/variations/'.$variation->id.'/', $data);

                    $description = '';

                }
            }
        }

        $this->createHooks($hashedProjectId);

        return $createdProdcts;

    }

    public function createProduct($projectId, $userId, $_product, $description, $variationId = null)
    {
        
        $hashedProjectId = Hashids::encode($projectId);

        $planModel = new Plan();
    
        $productModel = new Product();

        $productPlanModel = new ProductPlan();

        $shopifyVariantId = ($_product->parent_id?$_product->parent_id:$_product->id).'-'.$hashedProjectId.'-'.str_replace(' ','',strtoupper($description));

        $exists = Product::where('shopify_variant_id', $shopifyVariantId)->first();
        //if(!empty($exists)) return;
        

        $product = $productModel->create(
            [
                'user_id' => $userId,
                'name' => $_product->name,
                'description' => mb_substr($description, 0, 100),
                'guarantee' => '0',
                'format' => 1,
                'category_id' => '11',
                //'cost' => 1,
                //'shopify' => true,
                'price' => $_product->price,
                'shopify_id' => $variationId,
                'shopify_variant_id' => $shopifyVariantId,
                'sku' => $_product->sku,
                'project_id' => $projectId,
            ]
        );

        $productsArray[] = $product->id;
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
        
        //$createdProdcts++;
        if(gettype($_product->images[0])=='array'){
            $src = $_product->images[0]['src'];
        }else{
            $src = $_product->images[0]->src;
        }
        $product->update(['photo' => $src]);

        

        return $shopifyVariantId;
    }

    public function createHooks($projectId)
    {
        //Order update
        $data = [
            'name' => "$projectId",
            'topic' => 'order.updated',
            'delivery_url' => 'http://dev.admin.com/postback/woocommerce/'.$projectId.'/tracking'
        ];
        $this->woocommerce->post('webhooks', $data);

        //Product update
        $data = [
            'name' => "$projectId",
            'topic' => 'product.updated',
            'delivery_url' => 'http://dev.admin.com/postback/woocommerce/'.$projectId.'/product/update'
        ];
        $this->woocommerce->post('webhooks', $data);

        //Product create
        $data = [
            'name' => "$projectId",
            'topic' => 'product.created',
            'delivery_url' => 'http://dev.admin.com/postback/woocommerce/'.$projectId.'/product/create'
        ];
        $this->woocommerce->post('webhooks', $data);
    }

    public function deleteHooks($projectId)
    {
        $hashedProjectId = Hashids::encode($projectId);
        
        $webhooks = $this->woocommerce->get('webhooks');

        foreach($webhooks as $webhook){
            
            if($webhook->name == ''.$hashedProjectId){
                
                //$this->woocommerce->delete('webhooks/'.$webhook->id.'?force=true');
                $ids[] = $webhook->id;
            }
        }
        $data = [
            'delete' => $ids
        ];
        
        $this->woocommerce->post('webhooks/batch', $data);


    }

}


