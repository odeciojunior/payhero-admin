<?php

namespace App\Http\Controllers\Dev;

use Exception;
use Slince\Shopify\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Modules\Core\Entities\Plan;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\User;
use Modules\Checkout\Classes\MP;
use Modules\Core\Entities\Pixel;
use Illuminate\Http\JsonResponse;
use Modules\Core\Entities\Domain;
use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Product;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\Checkout;
use Modules\Core\Entities\PlanSale;
use Modules\Core\Entities\Transfer;
use Modules\Core\Services\FoxUtils;
use Vinkla\Hashids\Facades\Hashids;
use App\Http\Controllers\Controller;
use Modules\Core\Entities\Invitation;
use Modules\Core\Entities\PostbackLog;
use Modules\Core\Entities\Transaction;
use Modules\Core\Entities\DomainRecord;
use Slince\Shopify\PublicAppCredential;
use Modules\Core\Services\NotazzService;
use Modules\Core\Services\HotZappService;
use Modules\Core\Services\ProductService;
use Modules\Core\Services\ShopifyService;
use Modules\Core\Entities\ProductPlanSale;
use Modules\Core\Services\CloudFlareService;
use Modules\Core\Entities\HotZappIntegration;
use Modules\Core\Entities\ShopifyIntegration;
use Modules\Core\Events\TrackingCodeUpdatedEvent;

class JulioController extends Controller
{

    public function julioFunction()
    {

        dd(Company::with('user')->find(Hashids::decode('n4KovG1Y8GyDEmO')));

        $transactions = Transaction::where([
            ['status', 'paid'],
            ['release_date', '2019-12-21']
        ])
        ->whereHas('sale',function($query){
            $query->where('payment_method', 2);
        })
        ->get();

        foreach($transactions as $transaction){
            $transaction->update([
                'release_date' => '2019-12-01'
            ]);
        }



        dd("foii");




        $sales = Sale::where([
            ['payment_method', 2],
            ['status', 1]
        ])
        ->with('transactions', 'client')
        ->whereHas('transactions', function($query){
            $query->where('status', 'waiting_payment');
            $query->whereNotNull('company_id');
            $query->whereNull('invitation_id');
        })
        ->orderBy('created_at', 'asc')
        ->get();

        dd(count($sales));

        $totalValue = 0;
        $count      = 0;

        foreach($sales as $sale){
            foreach($sale->transactions as $transaction){
                if(!empty($transaction->company)){
                    $transaction->update([
                        'status' => 'paid',
                        'release_date' => '2019-12-21',
                    ]);
                }
                else{
                    $transaction->update([
                        'status' => 'paid',
                    ]);
                }
            }
        }

        dd($totalValue, $count);
    }

}


