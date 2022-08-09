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
        $this->checkPaidBoletos();
        // dd(env('DB_HOST'));

        //$this->testSms(['message'   => 'teste','telephone' => '5555996931098']);

        // $this->restartShopifyWebhooks();

        // $this->createProjectNotifications();

        // $this->checkPaidBoletos();
    }

    public function checkPaidBoletos()
    {
        $sales = Sale::where("status", 20)
            ->whereNotNull("upsell_id")
            ->get();

        echo "<table>";
        echo "<thead>";
        echo "<th>Transação upsell</th>";
        echo "<th>Transação original</th>";
        echo "</thead>";
        echo "<tbody>";
        foreach ($sales as $sale) {
            echo "<tr>";
            echo "<td>" . Hashids::encode($sale->id) . "</td>";
            echo "<td>" . Hashids::encode($sale->upsell_id) . "</td>";
            echo "</tr>";
        }
        echo "</tbody>";
        echo "</table>";

        // dd($sales->with('transactions')->limit(10)->get()->toArray());
    }

    public function checkTransactions()
    {
        $transactionModel = new Transaction();
        $transferModel = new Transfer();

        $transactions = $transactionModel
            ->where([
                [
                    "release_date",
                    ">=",
                    Carbon::now()
                        ->subDays("5")
                        ->format("Y-m-d"),
                ],
                ["status", "transfered"],
            ])
            ->whereHas("transfers", null, ">", 1);

        $totalValue = 0;
        $realValue = 0;
        $wrongValue = 0;

        foreach ($transactions->cursor() as $key => $transaction) {
            if ($key % 300 == 0) {
                dump($key);
            }

            $value = 0;
            foreach ($transaction->transfers as $key => $transfer) {
                $totalValue += $transfer->value;

                if ($key > 0) {
                    $value += $transfer->value;
                } else {
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
            number_format(intval($totalValue) / 100, 2, ",", "."),
            number_format(intval($realValue) / 100, 2, ",", "."),
            number_format(intval($wrongValue) / 100, 2, ",", ".")
        );
    }

    public function restartShopifyWebhooks()
    {
        $webHooksUpdated = 0;

        foreach (ShopifyIntegration::all() as $shopifyIntegration) {
            try {
                $shopifyService = new ShopifyService($shopifyIntegration->url_store, $shopifyIntegration->token);

                if (count($shopifyService->getShopWebhook()) != 3) {
                    $shopifyService->deleteShopWebhook();

                    $this->createShopWebhook([
                        "topic" => "products/create",
                        "address" =>
                            "https://sirius.cloudfox.net/postback/shopify/" .
                            Hashids::encode($shopifyIntegration->project_id),
                        "format" => "json",
                    ]);

                    $this->createShopWebhook([
                        "topic" => "products/update",
                        "address" =>
                            "https://sirius.cloudfox.net/postback/shopify/" .
                            Hashids::encode($shopifyIntegration->project_id),
                        "format" => "json",
                    ]);

                    $this->createShopWebhook([
                        "topic" => "orders/updated",
                        "address" =>
                            "https://sirius.cloudfox.net/postback/shopify/" .
                            Hashids::encode($shopifyIntegration->project_id) .
                            "/tracking",
                        "format" => "json",
                    ]);

                    $webHooksUpdated++;
                }
            } catch (\Exception $e) {
                // dump($e);
            }

            dump($webHooksUpdated);
        }
    }

    public function testSms($data)
    {
        event(new SendSmsEvent($data));
    }

    public function createProjectNotifications()
    {
        $projectNotificationService = new ProjectNotificationService();

        foreach (Project::whereDoesntHave("notifications")->get() as $project) {
            if (count($project->notifications) == 0) {
                $projectNotificationService->createProjectNotificationDefault($project->id);
            }
        }
    }

    public function editShpoifyStatusOrder()
    {
        // $shopifyIntegrationModel = new ShopifyIntegration();

        // $sales = Sale::whereHas('project.shopifyIntegrations', function ($query) {
        //     $query->where('status', 2);
        // })
        // ->whereBetween('end_date', ['2020-04-28 00:00:00', '2020-04-28 23:59:59'])
        // ->where('status', 1)
        // ->where('payment_method', 2)
        // ->orderBy('id','desc')
        // ->get();

        // $x = 1;

        // foreach($sales as $sale){

        //     $shopifyIntegration = $shopifyIntegrationModel->where('project_id', $sale->project_id)->first();

        //     try {
        //         $this->line($x++ . "-> Atualizando pedido no shopify " . $sale->id);

        //         $credential = new PublicAppCredential($shopifyIntegration->token);

        //         $client = new Client($credential, $shopifyIntegration->url_store, [
        //             'metaCacheDir' => '/var/tmp',
        //         ]);

        //         $client->getTransactionManager()->create($sale->shopify_order, [
        //             "kind"    => "sale",
        //             "source"  => "external",
        //             "gateway" => "Boleto",
        //         ]);
        //     } catch (Exception $e) {
        //         $this->line("Erro ao atualizar pedido no shopify " . $sale->id . ' erro ' . $e->getMessage());
        //     }

        // try{
        //     $this->line('Gerando pedido na venda ' . $sale->id);

        //     $shopifyIntegration = $sale->project->shopifyIntegrations->first();

        //     $shopifyService = new ShopifyService($shopifyIntegration->url_store, $shopifyIntegration->token);

        //     $shopifyService->newOrder($sale);
        // }
        // catch(Exception $e){
        //     $this->line('Erro ao gerar pedido na venda ' . $sale->id . ' Erro: ' . $e->getMessage());
        // }
    }

    public function updateShopifyBoletos()
    {
        $shopifyIntegrationModel = new ShopifyIntegration();

        $sales = Sale::whereBetween("end_date", ["2020-05-15 00:00:00", "2020-05-21 23:59:59"])
            ->where("project_id", 2417)
            ->where("status", 1)
            ->where("payment_method", 2)
            ->orderBy("id", "desc")
            ->get();

        $x = 1;

        foreach ($sales as $sale) {
            $shopifyIntegration = $shopifyIntegrationModel->where("project_id", $sale->project_id)->first();

            try {
                $this->line($x++ . "-> Atualizando pedido no shopify " . $sale->id);

                $credential = new PublicAppCredential($shopifyIntegration->token);

                $client = new Client($credential, $shopifyIntegration->url_store, [
                    "metaCacheDir" => "/var/tmp",
                ]);

                $client->getTransactionManager()->create($sale->shopify_order, [
                    "kind" => "sale",
                    "source" => "external",
                    "gateway" => "cloudfox",
                    "authorization" => Hashids::connection("sale_id")->encode($sale->id),
                ]);
            } catch (Exception $e) {
                $this->line("Erro ao atualizar pedido no shopify " . $sale->id . " erro " . $e->getMessage());
            }
        }
    }

    public function updateDomains()
    {
        // $cloudflareService = new CloudFlareService();

        // $domains = Domain::all();
        // $total = $domains->count();

        // foreach ($domains as $key => $domain) {
        //     $this->info($key + 1 . ' de ' . $total . '. Domínio: ' . $domain->name);
        //     try {

        //         // checkout
        //         $records = $cloudflareService->getRecords($domain->name);
        //         $domainRecord = collect($records)->first(function ($item) {
        //             if (Str::contains($item->name, 'checkout.')) {
        //                 return $item;
        //             }
        //         });
        //         if(empty($domainRecord)){
        //             $this->warn('Record não encontrado');
        //             continue;
        //         }

        //         $data = [
        //             'type'    => $domainRecord->type,
        //             'name'    => 'checkout10',
        //             'content' => $domainRecord->content,
        //             'proxied' => true,
        //         ];

        //         $updated = $cloudflareService->updateRecordDetails($domain->cloudflare_domain_id, $domainRecord->id, $data);

        //         if ($updated) {
        //             $this->line('Record checkout atualizado!');
        //             $recordId = $cloudflareService->addRecord("CNAME", 'checkout', 'CloudfoxSuit-Checkout-Balance-1912358215.us-east-1.elb.amazonaws.com');
        //             $this->line('Novo record criado: ' . $recordId);
        //         }
        //         else{
        //             $this->line('Erro ao atualizar record!');
        //         }

        // sac
        // $domainRecord = collect($records)->first(function ($item) {
        //     if (Str::contains($item->name, 'sac.')) {
        //         return $item;
        //     }
        // });
        // if(empty($domainRecord)){
        //     $this->warn('Record não encontrado');
        //     continue;
        // }

        // $data = [
        //     'type'    => $domainRecord->type,
        //     'name'    => 'sac3',
        //     'content' => $domainRecord->content,
        //     'proxied' => true,
        // ];

        // $updated = $cloudflareService->updateRecordDetails($domain->cloudflare_domain_id, $domainRecord->id, $data);

        // if ($updated) {
        //     $this->line('Record checkout atualizado!');
        //     $recordId = $cloudflareService->addRecord("CNAME", 'sac', 'CloudfoxSuit-SAC-Balance-1972915763.us-east-1.elb.amazonaws.com');
        //     $this->line('Novo record criado: ' . $recordId);
        // }
        // else{
        //     $this->line('Erro ao atualizar record!');
        // }

        //     } catch (\Exception $e) {
        //         $this->error($e->getMessage());
        //     }
        // }
        // $this->info('ACABOOOOOOOOOOOOOU!');
    }

    public function checkAntifraude()
    {
        $sales = Sale::whereHas("saleLogs", function ($query) {
            $query->where("status_enum", 20);
        })
            ->whereDate(
                "created_at",
                ">=",
                Carbon::now()
                    ->subDays(3)
                    ->toDateString()
            )
            ->get();

        $approved = 0;
        $reproved = 0;
        $total = 0;
        foreach ($sales as $sale) {
            if ($sale->status == 1) {
                $approved++;
                $total++;
            }
            if ($sale->status == 21) {
                $reproved++;
                $total++;
            }
        }

        dd("total " . $total . " aprovado " . $approved . " reprovado " . $reproved);
    }

    public function approveShopifyOrderAntifraud()
    {
        //command

        $salesModel = new Sale();

        $sales = $salesModel
            ->with(["project.shopifyIntegrations"])
            ->where("status", $salesModel->present()->getStatus("approved"))
            ->where("payment_method", $salesModel->present()->getPaymentType("credit_card"))
            ->whereNotNull("shopify_order")
            ->whereHas("saleLogs", function ($query) use ($salesModel) {
                $query->where("status_enum", $salesModel->present()->getStatus("in_review"));
            })
            ->get();

        $shopifyStores = [];

        $total = $sales->count();

        $ordersApproved = 0;
        foreach ($sales as $key => $sale) {
            $count = $key + 1;
            $this->line("Verificando venda {$count} de {$total}: {$sale->id}");

            $project = $sale->project;
            if (!empty($shopifyStores[$project->id])) {
                $shopifyService = $shopifyStores[$project->id];
            } else {
                $integration = $sale->project->shopifyIntegrations->first();
                if (!empty($integration)) {
                    $shopifyService = new ShopifyService($integration->url_store, $integration->token, false);
                    $shopifyStores[$project->id] = $shopifyService;
                } else {
                    $this->warn("Nenhuma integração encontrada para este projeto");
                    continue;
                }
            }

            try {
                $order = $shopifyService
                    ->getClient()
                    ->getOrderManager()
                    ->find($sale->shopify_order);
                if ($order->getFinancialStatus() == "pending") {
                    $data = [
                        "kind" => "capture",
                        "gateway" => "cloudfox",
                        "authorization" => Hashids::connection("sale_id")->encode($sale->id),
                    ];
                    $shopifyService
                        ->getClient()
                        ->getTransactionManager()
                        ->create($sale->shopify_order, $data);
                    $ordersApproved++;

                    $this->info("Order criada no shopify");
                }
            } catch (Exception $e) {
                $this->error($e->getMessage());
            }
        }

        $this->info("{$ordersApproved} orders aprovadas no shopify de {$total} vendas verificadas");
    }
}
