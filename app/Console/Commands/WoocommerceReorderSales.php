<?php

namespace App\Console\Commands;

use App\Exceptions\CommandMonitorTimeException;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\SaleWoocommerceRequests;
use Modules\Core\Entities\WooCommerceIntegration;
use Modules\Core\Services\WooCommerceService;
use Modules\Core\Services\FoxUtils;
use Vinkla\Hashids\Facades\Hashids;


class WoocommerceReorderSales extends Command
{
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'command:WoocommerceReorderSales';
    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        
        $model = new SaleWoocommerceRequests();
        $requests = $model->where('status',0)->where('method','ProcessWooCommerceOrderCreate')->get();

        foreach ($requests as $request) {
            try {
                    $integration = WooCommerceIntegration::where('project_id', $request['project_id'])->first();
                    $service = new WooCommerceService($integration->url_store, $integration->token_user, $integration->token_pass);
                    
                    $data = json_decode($request['send_data'], true);

                    $changeToPaidStatus = 0;

                    if($data['status'] == 'processing' && $data['set_paid'] == true){
                        $data['status'] = 'pending';
                        $data['set_paid'] = false;
                        $changeToPaidStatus = 1;
                    }

                    $result = $service->woocommerce->post('orders', $data);

                    if($result->id){
                        $order = $result->id;
                        $saleModel = Sale::where('id',$request['sale_id'])->first();
                        $saleModel->woocommerce_order = $order;
                        $saleModel->save();
                        
                        $result = json_encode($result);
                        $service->updatePostRequest($request['id'], 1, $result, $order);

                        $this->line('success -> order generated: ' . $order);

                        if($changeToPaidStatus == 1){
                            
                            $result = $service->approveBillet($order, $request['project_id'], $request['sale_id']);

                            if($result->status == 'processing')
                                $this->line('success -> order status changed: ' . $order);

                        }

                    }
            } catch (Exception $e) {

                $this->line('erro -> ' . $e->getMessage());
                
            }
        }

        // $saleModel     = new Sale();
        // // $salePresenter = $saleModel->present();
        // // $date          = Carbon::now()->subDay()->toDateString();
        // $sales         = $saleModel->whereNull('woocommerce_order')
        //                             ->where('status',1)
        //                            //->whereDate('created_at', $date)
        //                            ->whereHas('project.woocommerceIntegrations', function($query) {
        //                                $query->where('status', 2);
        //                            })
        //                            ->get();

        // foreach ($sales as $sale) {
        //     try {
        //         $integration = WooCommerceIntegration::where('project_id', $sale->project_id)->first();

        //         $service = new WooCommerceService($integration->url_store, $integration->token_user, $integration->token_pass);
                
        //         $preOrder = $this->prepareOrder($sale);
        //         $data = $this->saveOrder($preOrder);

                
                
                

        //         $result = $service->woocommerce->post('orders', $data);

        //         $saleModel = Sale::where('id',$sale->id)->first();
        //         $saleModel->woocommerce_order = $result->id;
        //         $saleModel->save();
        //         $this->line('gateway_status: '.$sale->gateway_status);

        //         $this->line('sucesso: '.$sale->id.' - '.$integration->url_store);

        //         //dd($result->id);
        //     } catch (Exception $e) {

        //         $this->line('erro -> ' . $e->getMessage());
        //         if(stristr($e->getMessage(),'Error')){
        //             $saleModel = Sale::where('id',$sale->id)->first();

        //             $saleModel->woocommerce_order = -1;
        //             $saleModel->save();
        //         }
        //     }
        // }
    }



    public function prepareOrder(Sale $sale)
    {
        
        
        $saleId = $sale->id;
        $delivery     = $sale->delivery;
        $client       = $sale->customer;

        $totalValue = $sale->present()->getSubTotal();
        
        $items        = [];
        $totalItems = 0;
        
        foreach ($sale->productsPlansSale as $productsPlanSale) {
            
            $product = $productsPlanSale->product;

            $items[] = [
                "grams"             => 500,
                "id"                => $productsPlanSale->plan->id,
                "price"             => $product->price,
                "product_id"        => $product->shopify_id,
                "quantity"          => $productsPlanSale->amount,
                "requires_shipping" => true,
                "sku"               => $product->sku,
                "title"             => $product->name,
                "variant_id"        => $product->shopify_variant_id,
                "variant_title"     => $product->description,
                "name"              => $product->name,
                "gift_card"         => false,
            ];
            $itemValue = number_format(round(((float)$product->price) ,2),2);
            $totalItems += $productsPlanSale->amount * $itemValue;
            
        }
        
        

        $address = "-";
        $shippingAddress = [];
        if( !empty($delivery)) {
            $address = $delivery->street . ' - ' . $delivery->number;            
        
            if (!empty($delivery->complement)) {
                $address .= ' - ' . $delivery->complement;
            }
            $address .= ' - ' . $delivery->neighborhood;        

            $shippingAddress = [
                "address1"      => $address,
                "address2"      => "",
                "city"          => $delivery->city ?? "-",
                "company"       => $client->document,
                "country"       => "Brasil",
                "first_name"    => $client->name,
                "last_name"     => '',
                "phone"         => $client->present()->getTelephoneShopify(),
                "province"      => $delivery->state ?? "-",
                "zip"           => empty($delivery) ? "-" : FoxUtils::formatCEP($delivery->zip_code),
                "name"          => $client->name,
                "country_code"  => "BR",
                "province_code" => $delivery->state ?? "-",
            ];
        }
        // Endereço de Faturamento
        $billingAddress = [
            "first_name" => $client->name,
            "last_name"  => '',
            "address1"   => $address,
            "phone"      => $client->present()->getTelephoneShopify(),
            "city"       => $delivery->city ?? "-",
            "province"   => $delivery->state ?? "-",
            "country"    => "Brasil",
            "zip"        => empty($delivery) ? "-" : FoxUtils::formatCEP($delivery->zip_code),
            
        ];

        $shippingValue = intval(preg_replace("/[^0-9]/", "", $sale->shipment_value));
        if ($shippingValue <= 0) {
            $shippingTitle = 'Frete Grátis para Todo Brasil';
        } else {
            $shippingTitle = 'Standard Shipping';
            $totalValue    += $shippingValue;
        }
        $shipping[] = [
            "custom" => true,
            "price"  => $shippingValue <= 0 ? 0.0 : FoxUtils::floatFormat($shippingValue),
            "title"  => $shippingTitle,
        ];

        

        $orderData = [
            "accepts_marketing"       => false,
            "currency"                => "BRL",
            "email"                   => $client->email,
            "phone"                   => $client->present()->getTelephoneShopify(),
            "first_name"              => $client->name,
            "last_name"               => '',
            "buyer_accepts_marketing" => false,
            "line_items"              => $items,
            "shipping_address"        => $shippingAddress,
            "billing_address"         => $billingAddress,
            "shipping_lines"          => $shipping,
            "note_attributes"         => [
                "token_cloudfox" => Hashids::encode($sale->checkout_id),
            ],
            "total_price"             => FoxUtils::floatFormat($totalValue),
            "discount"                => (floatval($totalItems) + FoxUtils::floatFormat($shippingValue)) - FoxUtils::floatFormat($totalValue),
            "has_order_bump"          => $sale->has_order_bump,
            "project_id"              => $sale->project_id,
            "id"                      => $sale->id,
            "discount_coupon"         => $sale->shopify_discount,
            
            
        ];
        
        

        if ($sale->payment_method == 1 || $sale->payment_method == 3) {
            //cartao

            $orderData += [
                "transactions" => [
                    [
                        "gateway"       => "cloudfox",
                        "authorization" => Hashids::connection('sale_id')->encode($sale->id),
                        "kind"          => "sale",
                        "status"        => "success",
                        "amount"        => FoxUtils::floatFormat($totalValue),
                    ],
                ],
            ];
        } else if ($sale->payment_method == 2) {
            //boleto

            $orderData += [
                "financial_status" => "pending",
                "transactions"     => [
                    [
                        "gateway"       => "cloudfox",
                        "authorization" => Hashids::connection('sale_id')->encode($sale->id),
                        "kind"          => "sale",
                        "status"        => "pending",
                        "amount"        => FoxUtils::floatFormat($totalValue),
                    ],
                ],
            ];
        }
        
        return $orderData;
    }

    public function saveOrder(Array $sale)
    {
        $items = array();
        
        
        foreach($sale['line_items'] as $item){
            //
            $productId = explode('-', $item['variant_id']);
            $items[] = [
                'product_id' => $productId[0],
                'quantity' => $item['quantity'],
                'variation_id' => ($item['product_id']?$item['product_id']:''),
            ];
        };
        foreach($items as $key => $item){
            foreach($item as $key2 => $item2){
                if(empty($item2)){
                    unset($items[$key][$key2]);
                }
            }
        }
        
        if(!empty($sale['transactions'])){
            if($sale['transactions'][0]['status']=='success'){
                $paymentMethod = 'card';
                $paymentMethodTitle = 'Cartão de crédito';
                $setPaid = true;
                $status = 'processing';
            }else{
                $paymentMethod = 'billet';
                $paymentMethodTitle = 'Boleto';
                $setPaid = false;
                $status = 'pending';
            }
        }else{
            $paymentMethod = 'pix';
            $paymentMethodTitle = 'Pix';
            $setPaid = false;
            $status = 'pending';
        }
        
        $metadata[] =
        [
            "key"=> "_billing_persontype",
            "value"=> "1"
        ];
            
        $metadata[] =
        [
            "key"=> "_billing_cpf",
            "value"=> $sale['shipping_address']['company']
        ];

        

        $data = [
            
            'payment_method' => $paymentMethod,
            'payment_method_title' => $paymentMethodTitle,
            'set_paid' => $setPaid,
            'status' => $status,
            
            'billing' => [
                'first_name' =>  $sale['billing_address']['first_name'],
                'last_name' => '',
                'address_1' => $sale['billing_address']['address1'],
                'address_2' => '',
                'city' => $sale['billing_address']['city'],
                'state' => $sale['billing_address']['province'],
                'postcode' => $sale['billing_address']['zip'],
                'country' => $sale['billing_address']['country'],
                'email' => $sale['email'],
                'phone' => $sale['phone']
            ],
            'shipping' => [
                'first_name' =>  $sale['shipping_address']['first_name'],
                'last_name' => '',
                'address_1' => $sale['shipping_address']['address1'],
                'company' => $sale['shipping_address']['company'],
                
                'city' => $sale['shipping_address']['city'],
                'state' => $sale['shipping_address']['province_code'],
                'postcode' => $sale['shipping_address']['zip'],
                'country' => $sale['shipping_address']['country_code']
            ],
            'line_items' => $items,
            'meta_data' => $metadata,
            'shipping_lines' => [
                [
                    'method_id' => 'flat_rate',
                    'method_title' =>  $sale['shipping_lines'][0]['title'],
                    'total' => ''.$sale['shipping_lines'][0]['price']
                ]
            ]
        ];

        if($sale['has_order_bump'] == 1){
            $data['fee_lines'][] = [
                "title" => "Desconto",
                "total" => "-".$sale['discount'],
                "tax_class" => "",
                "tax_status" => "taxable",
                "name" => "Desconto"
            ];
        }

        if($sale['discount_coupon']){
            $data['fee_lines'][] = [
                "title" => "Desconto",
                "total" => "-".$sale['discount_coupon'],
                "tax_class" => "",
                "tax_status" => "taxable",
                "name" => "Desconto"
            ];
        }
        
        try{
            
            return $data;
            

        }catch(Exception $e){
            
            report($e);
            
        }
        
    }
}
