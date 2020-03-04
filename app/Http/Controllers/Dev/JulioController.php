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
use Modules\Core\Entities\Project;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\Checkout;
use Modules\Core\Entities\PlanSale;
use Modules\Core\Entities\Transfer;
use Modules\Core\Services\FoxUtils;
use Vinkla\Hashids\Facades\Hashids;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Redis;
use Modules\Core\Entities\Invitation;
use Modules\Core\Events\SendSmsEvent;
use Modules\Core\Entities\PostbackLog;
use Modules\Core\Entities\Transaction;
use Modules\Core\Entities\DomainRecord;
use Slince\Shopify\PublicAppCredential;
use Modules\Core\Services\NotazzService;
use Modules\Core\Services\HotZappService;
use Modules\Core\Services\ProductService;
use Modules\Core\Services\ShopifyService;
use Modules\Sales\Exports\Reports\Report;
use Modules\Core\Entities\ProductPlanSale;
use Modules\Core\Events\SaleRefundedEvent;
use Modules\Core\Services\CloudFlareService;
use Modules\Core\Entities\HotZappIntegration;
use Modules\Core\Entities\ShopifyIntegration;
use Modules\Core\Services\RemessaOnlineService;
use Modules\Core\Events\TrackingCodeUpdatedEvent;
use Modules\Core\Services\ProjectNotificationService;

class JulioController extends Controller
{

    public function julioFunction()
    {

        dd(env('DB_HOST'));

        //$this->testSms(['message'   => 'teste','telephone' => '5555996931098']);

        // $this->restartShopifyWebhooks();

        // $this->createProjectNotifications();

        $this->checkPaidBoletos();
    }

    public function checkPaidBoletos(){

        $saleModel = new Sale();

        $sales = $saleModel->where([
            ['status',1],
            ['payment_method', 1],
            ['start_date', '>',  '2019-09-01']
        ])
        ->whereHas('transactions', function($query){
            $query->whereNotIn('status_enum', [1,2]);
            $query->whereNotNull('company_id');
        })->get();

        echo "<table>";
        echo "<thead>";
        echo "<th>ID</th>";
        echo "<th>Code</th>";
        echo "<th>Data</th>";
        echo "<th>Status</th>";
        echo "<th>Usuario</th>";
        echo "<th>Transações</th>";
        echo "</thead>";
        foreach($sales as $sale){
            echo "<tr>";
            echo "<td>" . $sale->id . "</td>";
            echo "<td>" . Hashids::connection('sale_id')->encode($sale->id) . "</td>";
            echo "<td>" . $sale->start_date . "</td>";
            echo "<td>" . $saleModel->present()->getStatus($sale->status) . "</td>";
            echo "<td>" . $sale->user->name . "</td>";
            echo "<td>";
            foreach($sale->transactions as $transaction){
                if(!empty($transaction->company_id)){
                    echo $transaction->status . ' - ';
                }
            }
            echo "</td>";
            echo "</tr>";
        }
        echo "</table>";

        // dd($sales->with('transactions')->limit(10)->get()->toArray());
    }

    public function checkTransactions(){

        $transactionModel = new Transaction();
        $transferModel    = new Transfer();

        $transactions = $transactionModel->where([
            ['release_date', '>=', Carbon::now()->subDays('5')->format('Y-m-d')],
            ['status', 'transfered']
        ])
        ->whereHas('transfers', null, '>', 1);

        $totalValue = 0;
        $realValue = 0;
        $wrongValue = 0;

        foreach($transactions->cursor() as $key => $transaction){

            if($key % 300 == 0){
                dump($key);
            }

            $value = 0;
            foreach($transaction->transfers as $key => $transfer){
                $totalValue += $transfer->value;

                if($key > 0){
                    $value += $transfer->value;
                }
                else{
                    $realValue += $transfer->value;
                }
            }

            $wrongValue += $value;

            $company = $transaction->company;

            //            $company->update([
            //                'balance' => intval($company->balance) - intval($value),
            //            ]);
            //
            //            $transfer = $transferModel->create([
            //                'user_id'        => $company->user_id,
            //                'company_id'     => $company->id,
            //                'type_enum'      => $transferModel->present()->getTypeEnum('out'),
            //                'value'          => $value,
            //                'type'           => 'out',
            //                'reason'         => 'Múltiplas transferências da transação #' . Hashids::connection('sale_id')->encode($transaction->sale_id)
            //            ]);

        }

        dd(
            number_format(intval($totalValue) / 100, 2, ',', '.'),
            number_format(intval($realValue) / 100, 2, ',', '.'),
            number_format(intval($wrongValue) / 100, 2, ',', '.')
           );

    }

    public function restartShopifyWebhooks(){

        $webHooksUpdated = 0;

        foreach(ShopifyIntegration::all() as $shopifyIntegration){

            try{
                $shopifyService = new ShopifyService($shopifyIntegration->url_store,$shopifyIntegration->token);

                if(count($shopifyService->getShopWebhook()) != 3){

                    $shopifyService->deleteShopWebhook();

                    $this->createShopWebhook([
                        "topic"   => "products/create",
                        "address" => 'https://app.cloudfox.net/postback/shopify/' . Hashids::encode($shopifyIntegration->project_id),
                        "format"  => "json",
                    ]);

                    $this->createShopWebhook([
                        "topic"   => "products/update",
                        "address" => 'https://app.cloudfox.net/postback/shopify/' . Hashids::encode($shopifyIntegration->project_id),
                        "format"  => "json",
                    ]);

                    $this->createShopWebhook([
                        "topic"   => "orders/updated",
                        "address" => 'https://app.cloudfox.net/postback/shopify/' . Hashids::encode($shopifyIntegration->project_id) . '/tracking',
                        "format"  => "json",
                    ]);

                    $webHooksUpdated++;
                }
            }
            catch(\Exception $e){
                // dump($e);
            }

            dump($webHooksUpdated);
        }
    }

    public function testSms($data){

        event(new SendSmsEvent($data));
    }

    public function createProjectNotifications(){

        $projectNotificationService = new ProjectNotificationService();

        foreach(Project::whereDoesntHave('notifications')->get() as $project){

            if(count($project->notifications) == 0){
                $projectNotificationService->createProjectNotificationDefault($project->id);
            }

        }
    }

}


