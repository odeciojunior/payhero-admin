<?php

namespace App\Console\Commands;

use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Console\Command;
<<<<<<< HEAD
use Illuminate\Http\Request;
=======
use Illuminate\Support\Carbon;
use Modules\Core\Entities\Sale;
use Modules\Core\Events\PixExpiredEvent;
>>>>>>> 6e114004348b1d26e4aadba7b8906eda3edbebe7
use Modules\Core\Services\CloudFlareService;
use Modules\Plans\Http\Controllers\PlansApiController;

class GenericCommand extends Command
{
    protected $signature = 'generic {user?}';

    protected $description = 'Command description';

    // private $cloudflareService;

    // public function __construct()
    // {
    //     //parent::__construct();

    //     //$this->cloudflareService = new CloudFlareService();
    // }

    public function handle()
<<<<<<< HEAD
    {        
        $this->testConfigAditionalInformation();
    }

    public function testConfigAditionalInformation(){

        // $controller = new PlansApiController();

        // $request = new Request();
        
        // dd($controller->saveConfigCustomProducts($request));

        $request = (object) 
        [            
            "products" => [
                0 => "671055",
                1 => "671055",
                2 => "671054"
            ],
            "type" => [
                671055 => [
                    0 => "image",
                    1 => "file"
                ],
                671054 => [
                    0 => "image"                    
                ]
            ],
            "label" => [
                671055 => [
                    0 => "xxx",
                    1 => "yyyy"
                ],                
                671054 => [
                    0 => "zz"                    
                ]
            ],
            "plan" => "nOzxZoM8kOgJ07o"
        ];
        $products = array_unique($request->products);        
        
        $details = [];        
        foreach($products as $product){            
            $details[$product]['type'] = !empty($request->type[$product]) ? $request->type[$product]: []; 
            $details[$product]['label'] = !empty($request->label[$product]) ? $request->label[$product]: []; 
        }

        $itens = [];
        foreach($details as $key=>$detailL1){
           foreach($detailL1 as $key2=> $detailL2){
                foreach($detailL2 as $key3=>$detailL3){
                    $itens[$key][$key3][$key2] = $detailL3; 
                }
           }
        }

        dd($itens);
=======
    {
        $lastPixSale = Sale::where('payment_method', Sale::PIX_PAYMENT)->get()->last();

        $lastPixSale->update([
            'created_at' => Carbon::now()->subHours(2)->toDateTimeString(),
            'status' => Sale::STATUS_PENDING
                             ]);

        dd(event(new PixExpiredEvent(Sale::latest()->first())));
>>>>>>> 6e114004348b1d26e4aadba7b8906eda3edbebe7
    }
}



