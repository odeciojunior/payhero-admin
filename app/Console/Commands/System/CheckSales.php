<?php

namespace App\Console\Commands\System;

use Illuminate\Support\Facades\Log;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\Gateway;
use Modules\Core\Entities\Sale;
use Modules\Core\Services\Gateways\Safe2payGateway;
use Vinkla\Hashids\Facades\Hashids;
use Modules\Core\Entities\Transaction;
use Modules\Core\Entities\ShopifyIntegration;
use Modules\Core\Entities\WooCommerceIntegration;
use Modules\Core\Services\SaleService;
use Modules\Core\Services\ShopifyService;
use Modules\Core\Services\WooCommerceService;

class CheckSales extends Command
{
    protected $signature = "check:sales";

    protected $description = "Command description";

    protected $fileLog;

    public function handle()
    {
        $this->fileLog = "storage/logs/laravel-" . Date("Y-m-d") . ".log";

        //$this->getTransactions(1);
        //$this->getTransactions(6);

        //$this->getTransactionsApprovedCloudfox();

        //$this->verifySaleSafeDevolvidoByIdTransaction();
        //$this->verifySaleSafeChargebackByIdTransaction();

        //$this->changeSaleFromRefused();
        //$this->changeSaleFromRefusedToIntegration();

        $this->verifySaleSafePendingByIdTransaction(false);
    }

    public function getBalanceCloudfox()
    {
        $totalSalesIn = DB::select(
            "SELECT SUM(value) as total FROM transactions WHERE gateway_id = 21 and status_enum IN (1,2)  AND company_id is not null AND deleted_at is NULL;"
        );
        $totalFoxIn = DB::select(
            "SELECT SUM(value) as total FROM transactions WHERE gateway_id = 21 and status_enum IN (1,2)  AND company_id is null AND deleted_at is NULL;"
        );

        $totalIn = $totalSalesIn["0"]->total + $totalFoxIn["0"]->total;
        $this->line(
            str_pad("Total Sale In:", 20, " ", STR_PAD_RIGHT) .
                str_pad($totalSalesIn["0"]->total, 20, " ", STR_PAD_LEFT)
        );
        $this->line(
            str_pad("Total Fox In:", 20, " ", STR_PAD_RIGHT) .
                str_pad($totalFoxIn["0"]->total, 20, " ", STR_PAD_LEFT)
        );
        $this->line(
            str_pad("Total In:", 20, " ", STR_PAD_RIGHT) .
                str_pad($totalIn, 20, " ", STR_PAD_LEFT)
        );
    }

    public function calcTotalApproved()
    {
        $json = file_get_contents("storage/logs/safe_autorizado.json");
        $rows = json_decode($json);

        $total = 0;
        foreach ($rows as $row) {
            $total += $row->value;
            $this->line($row->value);
        }
        $this->comment($total);
    }

    public function verifySaleSafeAutorizado()
    {
        $json = file_get_contents("storage/logs/safe_autorizado.json");
        $rows = json_decode($json);

        $this->warn("safe: " . count($rows));

        $total = count($rows);
        $bar = $this->output->createProgressBar($total);
        $bar->start();

        foreach ($rows as $row) {
            $sale = DB::table("sales")
                ->select(
                    "id",
                    "status",
                    "payment_method",
                    "original_total_paid_value"
                )
                ->where("gateway_id", 21)
                ->where("status", "!=", Sale::STATUS_APPROVED)
                ->where("id", $row->SaleId)
                ->first();

            if ($sale) {
                \Log::info(
                    json_encode([
                        "SaleId" => $sale->id,
                        "StatusSafe" => $row->Status,
                        "StatusCloudfox" => $sale->status,
                        "IdTransaction" => $row->IdTransaction,
                        "ValueSafe" => $row->value,
                        "ValueCloudfox" => $sale->original_total_paid_value,
                    ])
                );
            }

            $bar->advance();
        }
        $bar->finish();

        // foreach($sales->cursor() as $sale){
        //     $exists = false;
        //     foreach($rows as $key=>$row)
        //     {
        //         if($sale->id == $row->SaleId){
        //             unset($rows[$key]);
        //             $exists = true;
        //             break;
        //         }
        //     }

        //     if(!$exists){
        //         \Log::info(json_encode(['id'=>$sale->id, 'payment_method'=>$sale->payment_method]));
        //     }
        // }
    }

    public function verifySaleSafeDevolvido()
    {
        $json = file_get_contents("storage/logs/safe_devolvido.json");
        $rows = json_decode($json);

        $this->warn("safe: " . count($rows));

        $saleSafe2pay = [];

        foreach ($rows as $row) {
            $saleSafe2pay[] = $row->SaleId;
        }

        $sales = Sale::select(
            "id",
            "status",
            "payment_method",
            "original_total_paid_value"
        )
            ->whereIn("id", $saleSafe2pay)
            ->get()
            ->toArray();

        foreach ($sales as $key => $sale) {
            if (
                !in_array($sale["status"], [
                    Sale::STATUS_REFUNDED,
                    Sale::STATUS_CANCELED_ANTIFRAUD,
                    Sale::STATUS_SYSTEM_ERROR,
                ])
            ) {
                \Log::info(
                    json_encode([
                        "SaleId" => $sale["id"],
                        "StatusCloudfox" => $sale["status"],
                    ])
                );
            }
        }
    }

    /**
     * @param string|null $syncSales
     */
    public function verifySaleSafePendingByIdTransaction($syncSales = null)
    {
        if ($syncSales) {
            $this->getTransactions(1);

            $this->createFileJson("storage/logs/pending_safe.json");
        } else {
            $this->deleteOrCreateLog();
        }

        $json = file_get_contents("storage/logs/pending_safe.json");
        $rows = json_decode($json);

        $this->warn("safe: " . count($rows));
        $this->warn("pending_safe_not_pending_cloudfox");

        $total = count($rows);
        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $saleCloudfox = [];
        $saleSafe2pay = [];
        $i = 0;
        $aux = 0; //
        $aux2 = 0; //
        foreach ($rows as $row) {
            $bar->advance();
            if (empty($row->IdTransaction)) {
                $aux2++;
            } //

            $sales = Sale::select("id", "status", "gateway_transaction_id")
                ->where("gateway_id", Gateway::SAFE2PAY_PRODUCTION_ID)
                ->where("gateway_transaction_id", $row->IdTransaction)
                ->first();
            if (!$sales) {
                $aux2++;
                Log::info(
                    json_encode([
                        "SaleId" => $row->IdTransaction,
                        "IdTransaction" => $row->IdTransaction,
                    ])
                );
            }

            continue;

            $saleSafe2pay[] = $row->IdTransaction;
            $i++;
            if ($i < 10000) {
                continue;
            }

            $sales = Sale::select("id", "status", "gateway_transaction_id")
                ->where("gateway_id", Gateway::SAFE2PAY_PRODUCTION_ID)
                ->whereIn("gateway_transaction_id", $saleSafe2pay);

            foreach ($sales->cursor()->toArray() as $key => $sale) {
                $saleCloudfox[] = $sale["gateway_transaction_id"];
                $aux++; //
                if (
                    !in_array($sale["status"], [
                        Sale::STATUS_PENDING,
                        Sale::STATUS_CANCELED,
                    ])
                ) {
                    Log::info(
                        json_encode([
                            "SaleId" => $sale["id"],
                            "IdTransaction" => $sale["gateway_transaction_id"],
                            "StatusCloudfox" => $sale["status"],
                        ])
                    );
                }
            }

            unset($saleSafe2pay);
            $i = 0;
        }
        //$this->createFileJson('storage/logs/pending_safe_not_pending_cloudfox.json');
        $this->createFileJson(
            "storage/logs/temp_pending_safe_not_exists_cloudfox.json"
        );
        $bar->finish();
        $this->warn("Not exists sale: " . $aux2);
        dd("Fim");
        $this->warn("Exists sale: " . $aux);
        $this->warn("Not IdTransaction: " . $aux2);
        $this->warn("Cloudfox: " . count($saleCloudfox));

        $this->warn("pending_safe_not_exists_cloudfox");
        $total = count($rows);
        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $aux3 = 0; //
        $aux4 = 0; //
        $aux5 = 0; //
        foreach ($rows as $row) {
            $aux3++; //
            if (!in_array($row->IdTransaction, $saleCloudfox)) {
                $aux4++; //
                Log::info(
                    json_encode([
                        "SaleId" => $row->SaleId,
                        "IdTransaction" => $row->IdTransaction,
                    ])
                );
                // Log::info(json_encode([
                //     'SaleId'=>$rowSafe
                // ]));
            } else {
                //
                $aux5++; //
            } //
            $bar->advance();
        }

        $this->warn("aux3: " . $aux3); //
        $this->warn("aux4: " . $aux4); //
        $this->warn("aux5: " . $aux5); //

        $this->createFileJson(
            "storage/logs/pending_safe_not_exists_cloudfox.json"
        );
        $bar->finish();

        //$this->verifySaleCloudfoxPendingByIdTransaction();
    }

    public function verifySaleCloudfoxPendingByIdTransaction()
    {
        $sales = Sale::select("id", "status", "gateway_transaction_id")
            ->where("gateway_id", Gateway::SAFE2PAY_PRODUCTION_ID)
            ->whereIn("status", [Sale::STATUS_PENDING]);

        if ($sales->count() == 0) {
            dd("Sem vendas pendentes na cloudfoox.");
        }

        // $this->getTransactions(7);
        // $this->createFileJson('storage/logs/temp_downloaded_safe.json');
        // $jsonDownloaded = file_get_contents('storage/logs/temp_downloaded_safe.json');
        // $rowsDownloaded = json_decode($jsonDownloaded);

        $total = $sales->count();
        $bar = $this->output->createProgressBar($total);
        $bar->start();

        //$safe = new Safe2payGateway();
        $json = file_get_contents("storage/logs/pending_safe.json");
        $rows = json_decode($json);

        foreach ($sales->cursor()->toArray() as $sale) {
            $exists = false;
            foreach ($rows as $key => $row) {
                if ($row->IdTransaction == $sale["gateway_transaction_id"]) {
                    $exists = true;
                    unset($rows[$key]);
                    break;
                }
            }
            if (!$exists) {
                // $existsDownloaded = false;
                // foreach($rowsDownloaded as $key=>$rowDownloaded) {
                //     if($rowDownloaded->IdTransaction == $sale['gateway_transaction_id'])
                //     {
                //         $existsDownloaded = true;
                //         unset($rowsDownloaded[$key]);
                //         break;
                //     }
                // }

                // if(!$existsDownloaded) {
                Log::info(
                    json_encode([
                        "SaleId" => $sale["id"],
                        "IdTransaction" => $sale["gateway_transaction_id"],
                        "StatusCloudfox" => $sale["status"],
                    ])
                );
                // }

                // $gatewaySale = $safe->getTransaction($sale['gateway_transaction_id']);

                // if(!empty($gatewaySale->ResponseDetail)){

                //     if($gatewaySale->ResponseDetail->Status != 7) {

                //         Log::info(json_encode([
                //             'SaleId'=>$sale['id'],
                //             'StatusCloudfox'=>$sale['status']
                //         ]));

                //     }
                // } else {
                //     Log::info(json_encode([
                //         'SaleId'=>$sale['id'],
                //         'StatusCloudfox'=>$sale['status'],
                //         'getSafe'=>false
                //     ]));
                // }

                // Log::info(json_encode([
                //     'SaleId'=>$sale['id'],
                //     'StatusCloudfox'=>$sale['status']
                // ]));
            }

            $bar->advance();
        }

        $this->createFileJson("storage/logs/pending_cloudfox_result.json");
        $bar->finish();
    }

    public function verifySaleSafeDevolvidoByIdTransaction()
    {
        $this->getTransactions(6);

        $this->createFileJson("storage/logs/safe_devolvido.json");

        $json = file_get_contents("storage/logs/safe_devolvido.json");
        $rows = json_decode($json);

        $this->warn("safe: " . count($rows));

        $saleSafe2pay = [];

        foreach ($rows as $row) {
            $saleSafe2pay[] = $row->IdTransaction;
        }

        $sales = Sale::select(
            "id",
            "status",
            "payment_method",
            "original_total_paid_value",
            "gateway_transaction_id"
        )
            ->where("gateway_id", Gateway::SAFE2PAY_PRODUCTION_ID)
            ->whereIn("gateway_transaction_id", $saleSafe2pay);

        foreach ($sales->cursor()->toArray() as $key => $sale) {
            if (
                !in_array($sale["status"], [
                    Sale::STATUS_REFUNDED,
                    Sale::STATUS_CANCELED_ANTIFRAUD,
                    Sale::STATUS_SYSTEM_ERROR,
                ])
            ) {
                //if($sale['status'] == Sale::STATUS_IN_REVIEW) {

                Log::info(
                    json_encode([
                        "SaleId" => $sale["id"],
                        "StatusCloudfox" => $sale["status"],
                    ])
                );
            }
        }

        $this->createFileJson("storage/logs/safe_devolvido_result.json");
    }

    public function verifySaleSafeChargebackByIdTransaction()
    {
        $json = file_get_contents("storage/logs/safe_chargeback.json");
        $rows = json_decode($json);

        $this->warn("safe: " . count($rows));

        $saleSafe2pay = [];

        foreach ($rows as $row) {
            $saleSafe2pay[] = $row->IdTransaction;
        }

        $sales = Sale::select(
            "id",
            "status",
            "payment_method",
            "original_total_paid_value",
            "gateway_transaction_id"
        )
            ->whereIn("gateway_transaction_id", $saleSafe2pay)
            ->get()
            ->toArray();

        foreach ($sales as $key => $sale) {
            if ($sale["status"] != Sale::STATUS_CHARGEBACK) {
                \Log::info(
                    json_encode([
                        "SaleId" => $sale["id"],
                        "StatusCloudfox" => $sale["status"],
                    ])
                );
            }
        }
    }

    public function createFileJson($filePut)
    {
        $fileGet = $this->fileLog;
        //$this->line('Arquivo: ' . $filePut);

        if (file_exists($fileGet)) {
            $this->deleteOrCreateLog($filePut);

            $line = file_get_contents($fileGet);
            $line_array = explode("\n", $line);

            foreach ($line_array as $index => $line_item) {
                $newStg = trim(substr($line_item, 33));

                if (empty($line_item)) {
                    continue;
                }

                if ($index === array_key_first($line_array)) {
                    file_put_contents(
                        $filePut,
                        "[\n" . $newStg . ",\n",
                        FILE_APPEND
                    );
                    continue;
                }

                if ($index === array_key_last($line_array) - 1) {
                    file_put_contents($filePut, $newStg . "\n]", FILE_APPEND);
                    continue;
                }

                file_put_contents($filePut, $newStg . ",\n", FILE_APPEND);
            }
            $this->deleteOrCreateLog();
        } else {
            dd("Arquivo não enconttrado. ", $filePut, $fileGet);
        }
    }

    public function deleteOrCreateLog($file = null)
    {
        if (file_exists($this->fileLog) and !$file) {
            file_put_contents($this->fileLog, null);
        }
        if (file_exists($file)) {
            file_put_contents($file, "");
        }
    }

    public function verifySaleSafe_old()
    {
        $json = file_get_contents("storage/logs/safe_autorizado.json");
        $rows = json_decode($json);

        $sales = DB::table("sales")
            ->select("id", "status", "payment_method")
            ->where("gateway_id", 21)
            //->where('payment_method',Sale::PAYMENT_TYPE_CREDIT_CARD)
            ->where("status", Sale::STATUS_APPROVED);

        $this->warn("safe: " . count($rows));
        //$this->warn('sales: '.count($sales));

        $exists = false;
        foreach ($sales->cursor() as $sale) {
            $exists = false;
            foreach ($rows as $key => $row) {
                if ($sale->id == $row->SaleId) {
                    unset($rows[$key]);
                    $exists = true;
                    break;
                }
            }

            if (!$exists) {
                \Log::info(
                    json_encode([
                        "id" => $sale->id,
                        "payment_method" => $sale->payment_method,
                    ])
                );
            }
        }
    }

    /*
    VENDA CARD DIFERENTE
    1772207 - status: 7
    */

    public function verifySale()
    {
        $json = file_get_contents(
            "storage/logs/safetopay_credit_card_chargeback.json"
        );
        $rows = json_decode($json);
        $ids = [];
        foreach ($rows as $row) {
            $saleId = current(
                Hashids::connection("sale_id")->decode($row->Reference)
            );
            if (empty($saleId)) {
                continue;
            }
            $ids[] = $saleId;
            if (count($ids) == 500) {
                $sales = DB::table("sales")
                    ->select("id", "status")
                    ->whereIn("id", $ids)
                    ->get();
                foreach ($sales as $sale) {
                    \Log::info(json_encode($sale));
                    if ($sale->status != Sale::STATUS_CHARGEBACK) {
                        $this->error($sale->id . " - status: " . $sale->status);
                    }
                }
                $ids = [];
            }
        }
    }

    public function getTransactions($code)
    {
        //2 card, 1 boleto, 6 pix
        $this->deleteOrCreateLog();

        $safe = new Safe2payGateway();
        $rowsPerPage = 1000;
        $filters = [
            "PageNumber" => 0,
            "RowsPerPage" => $rowsPerPage,
            "Object.TransactionStatus.Code" => $code,
        ];

        $total = 0;
        $counter = 0;
        $pagesNumber = 0;
        $maxPages = 1;

        do {
            $rows = $safe->listTransactions($filters);

            if (empty($rows->ResponseDetail)) {
                break;
            }

            $total = $rows->ResponseDetail->TotalItems;
            $this->warn("total " . $rows->ResponseDetail->TotalItems);
            $pagesNumber++;
            $maxPages = ceil($total / $rowsPerPage) + 1;
            foreach ($rows->ResponseDetail->Objects as $row) {
                Log::info(
                    json_encode([
                        "SaleId" => hashids_decode($row->Reference, "sale_id"),
                        "Reference" => $row->Reference,
                        "Status" => $row->Status,
                        "IdTransaction" => $row->IdTransaction,
                        "value" => $row->Amount,
                    ])
                );
                $counter++;
            }

            $filters["PageNumber"] = $pagesNumber;
            $this->line($pagesNumber - 1 . "/" . $maxPages);
        } while ($pagesNumber <= $maxPages);
    }

    public function getTransaction()
    {
        $safe = new Safe2payGateway();
        $sales = DB::table("sales")
            ->select("id", "gateway_transaction_id")
            ->where("gateway_id", 21)
            ->where("status", Sale::STATUS_APPROVED);

        foreach ($sales->cursor() as $sale) {
            $this->line("sale " . $sale->id);
            \Log::info(
                (array) $safe->getTransaction($sale->gateway_transaction_id)
            );
        }
    }

    public function changeSaleFromRefused()
    {
        $saleIds = [
            1825424,
            1825586,
            1825647,
            1825742,
            1825870,
            1825887,
            1825913,
            1825967,
            1825996,
            1826006,
            1826017,
            1826071,
            1826143,
            1826156,
            1826306,
            1826309,
            1826360,
            1826362,
            1826366,
            1826407,
            1826477,
            1826546,
            1826562,
            1826591,
            1826619,
            1826675,
            1826735,
            1826756,
            1826780,
            1826781,
            1826782,
            1826806,
            1826826,
            1826833,
            1826858,
            1826925,
            1826985,
            1829673,
        ];

        $sales = Sale::select(
            "id",
            "status",
            "payment_method",
            "original_total_paid_value",
            "gateway_transaction_id"
        )
            ->with("transactions")
            ->whereIn("id", $saleIds)
            ->where("status", Sale::STATUS_REFUNDED)
            ->get(); //->toArray();

        //dd(count($sales));
        // 1748799, 1805631, 1805643, 1805662, 1805787, 1806082, 1806126, 1808877, 1808882, 1808904, 1808971, 1808987, 1809015, 1809030, 1809033, 1810971,

        foreach ($sales as $key => $sale) {
            //if(!in_array($sale['status'], [Sale::STATUS_REFUNDED, Sale::STATUS_CANCELED_ANTIFRAUD] )) {
            if ($sale->status == Sale::STATUS_REFUNDED) {
                $this->line($sale["id"] . ",");
                // \Log::info(json_encode([
                //     'SaleId'=>$sale['id'],
                //     'StatusCloudfox'=>$sale['status']
                // ]));

                //update status gateway devolvido

                $sale->update([
                    "status" => Sale::STATUS_CANCELED_ANTIFRAUD,
                    "gateway_status" => "REFUNDED",
                ]);

                foreach ($sale->transactions as $transaction) {
                    $transaction->update([
                        "status" => "canceled_antifraud",
                        "status_enum" => Transaction::STATUS_CANCELED_ANTIFRAUD,
                    ]);
                }

                SaleService::createSaleLog($sale->id, "canceled_antifraud");
            }
        }
    }

    public function changeSaleFromRefusedToIntegration()
    {
        $saleIds = [
            1502470,
            1525818,
            1536734,
            1537056,
            1547459,
            1552765,
            1556185,
            1557894,
            1590249,
            1590477,
            1590480,
            1590488,
            1590490,
            1590491,
            1590494,
            1590496,
            1590497,
            1590498,
            1590501,
            1590502,
            1590503,
            1590508,
            1590510,
            1595503,
            1598834,
            1599197,
            1600587,
            1602175,
            1607892,
            1617325,
            1620611,
            1622466,
            1626345,
            1626675,
            1627169,
            1636864,
            1637660,
            1640532,
            1641255,
            1641452,
            1651850,
            1653172,
            1653294,
            1657175,
            1660120,
            1662468,
            1662618,
            1664439,
            1665816,
            1667529,
            1668497,
            1669755,
            1671881,
            1673529,
            1674005,
            1677411,
            1677476,
            1681940,
            1681950,
            1685366,
            1694308,
            1694664,
            1703071,
            1715464,
            1715529,
            1726731,
            1732928,
            1748799,
            1805626,
            1805631,
            1805643,
            1805662,
            1805689,
            1805752,
            1805785,
            1805787,
            1805872,
            1805874,
            1805982,
            1806076,
            1806082,
            1806083,
            1806126,
            1806168,
            1808803,
            1808877,
            1808882,
            1808904,
            1808971,
            1808987,
            1809015,
            1809030,
            1809033,
            1810971,
            1826963,
            1826942,
            1826913,
            1826365,
        ];

        $sales = Sale::whereIn("id", $saleIds)
            ->where("status", Sale::STATUS_REFUNDED)
            ->get(); //->toArray();

        //dd(count($sales));
        // 1748799, 1805631, 1805643, 1805662, 1805787, 1806082, 1806126, 1808877, 1808882, 1808904, 1808971, 1808987, 1809015, 1809030, 1809033, 1810971,

        foreach ($sales as $key => $sale) {
            //if(!in_array($sale['status'], [Sale::STATUS_REFUNDED, Sale::STATUS_CANCELED_ANTIFRAUD] )) {
            if ($sale->status == Sale::STATUS_REFUNDED) {
                $this->line($sale["id"] . ",");
                //Shopify
                if (!empty($sale->shopify_order)) {
                    $shopifyIntegration = ShopifyIntegration::where(
                        "project_id",
                        $sale->project_id
                    )->first();
                    if (!empty($shopifyIntegration)) {
                        $shopifyService = new ShopifyService(
                            $shopifyIntegration->url_store,
                            $shopifyIntegration->token,
                            false
                        );
                        $shopifyService->refundOrder($sale);
                        $shopifyService->saveSaleShopifyRequest();
                    }
                }

                //WooCommerce
                if (!empty($sale->woocommerce_order)) {
                    $integration = WooCommerceIntegration::where(
                        "project_id",
                        $sale->project_id
                    )->first();
                    if (!empty($integration)) {
                        $service = new WooCommerceService(
                            $integration->url_store,
                            $integration->token_user,
                            $integration->token_pass
                        );

                        $service->cancelOrder($sale, "Estorno");
                    }
                }
            }
        }
        dd("fim");
    }

    public function getTransactionsApprovedCloudfox()
    {
        $safe = new Safe2payGateway();

        $sales = DB::table("sales")
            ->select("id", "gateway_transaction_id", "status")
            ->where("gateway_id", 21)
            ->where("status", Sale::STATUS_APPROVED);

        $total = $sales->count();
        $bar = $this->output->createProgressBar($total);
        $bar->start();

        foreach ($sales->cursor() as $sale) {
            //$this->line('sale '.$sale->id);

            $gatewaySale = $safe->getTransaction($sale->gateway_transaction_id);

            if (
                empty($gatewaySale) ||
                $gatewaySale->ResponseDetail->Status != 3
            ) {
                Log::info(
                    json_encode([
                        "SaleId" => $sale->id,
                        "StatusSafe" =>
                            $gatewaySale->ResponseDetail->Status ?? null,
                        "StatusCloudfox" => $sale->status,
                    ])
                );
            }

            $bar->advance();
        }

        $bar->finish();
    }
}

//Safe
//- Pendente
// vendas que gerou duas cobranças na safe não atualizou o gateway_transaction_id, pendente na safe, aprovado na fox e na safe tem duas cobranças uma paga e outra pendente
// cartão pendente na safe e aprovado na fox
// cartão pendente na safe e Revisão Antifraude aqui na fox (deveria estar Pré-Autorizado na safe)
// pix pendente na safe e cancelado aqui na fox mas sem o gateway_transaction_id (3x qr gerado) ex: 1509336, 1523941
// boleto não encontrado na safe mas com o gateway_transaction_id aqui na fox ex: 1520280, 1520350
