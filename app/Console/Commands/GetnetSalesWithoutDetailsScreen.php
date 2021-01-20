<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\Transaction;
use Modules\Core\Services\GetnetBackOfficeService;

class GetnetSalesWithoutDetailsScreen extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'getnet:sales-without-details-screen';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {

        $transactionModel = new Transaction();

        $start = now();
        $this->comment(now()->format('H:i:s'));
        $this->comment('............');

        $sales = Sale::select('sales.id')
            ->join('transactions', 'sales.id', '=', 'transactions.sale_id')
            ->whereIn('sales.gateway_id', [15])
            ->whereNotNull('sales.gateway_order_id')
            ->where('transactions.status_enum', $transactionModel->present()->getStatusEnum('paid'))
            ->distinct();

        $limit = $sales->count() / 20;
        $count = 0;
        $percentage = 0;
        $this->comment('......' . $sales->count() . '......');

        $withoutListTransactions['id'] = [];
        $withoutListTransactions['hashId'] = [];
        $withoutListTransactions['orderId'] = [];

        $zeroListTransactions['id'] = [];
        $zeroListTransactions['hashId'] = [];
        $zeroListTransactions['orderId'] = [];

        $withoutSummary['id'] = [];
        $withoutSummary['hashId'] = [];
        $withoutSummary['orderId'] = [];

        $zeroSummary['id'] = [];
        $zeroSummary['hashId'] = [];
        $zeroSummary['orderId'] = [];

        $withoutDetails['id'] = [];
        $withoutDetails['hashId'] = [];
        $withoutDetails['orderId'] = [];

        $zeroDetails['id'] = [];
        $zeroDetails['hashId'] = [];
        $zeroDetails['orderId'] = [];

        foreach ($sales->cursor() as $sale) {

            $count++;

            if ($count >= $limit) {

                $count = 0;
                $percentage += 5;

                $this->info(' - ' . $percentage . '%');
            }

            try {

                $saleId = $sale->id;
                $hashId = $sale->hash_id;
                $orderId = $sale->gateway_order_id;

                $getNetBackOfficeService = new GetnetBackOfficeService();
                $getNetBackOfficeService->setStatementSaleHashId($hashId);

                $originalResult = $getNetBackOfficeService->getStatement();
                $result = json_decode($originalResult);

                if (isset($result->list_transactions)) {

                    if (count($result->list_transactions)) {

                        foreach ($result->list_transactions as $statement) {

                            if (!isset($statement->summary)) {

                                /*$summary = $statement->summary;
                                $details = $statement->details;
                                } else {*/

                                $withoutSummary['id'][] = $saleId;
                                $withoutSummary['hashId'][] = $hashId;
                                $withoutSummary['orderId'][] = $orderId;

                                $this->error('Nao existe summary para: ' . $saleId . '  |  ' . $hashId . '  |  ' . $orderId);
                            }

                            if (isset($statement->details)) {

                                if (!count($statement->details)) {

                                    $zeroDetails['id'][] = $saleId;
                                    $zeroDetails['hashId'][] = $hashId;
                                    $zeroDetails['orderId'][] = $orderId;

                                    $this->error('ZERO details para: ' . $saleId . '  |  ' . $hashId . '  |  ' . $orderId);
                                }

                            } else {

                                $withoutDetails['id'][] = $saleId;
                                $withoutDetails['hashId'][] = $hashId;
                                $withoutDetails['orderId'][] = $orderId;

                                $this->error('Nao existe details para: ' . $saleId . '  |  ' . $hashId . '  |  ' . $orderId);

                            }

                        }
                    } else {

                        $zeroListTransactions['id'][] = $saleId;
                        $zeroListTransactions['hashId'][] = $hashId;
                        $zeroListTransactions['orderId'][] = $orderId;

                        $this->error('ZERO list_transactions para: ' . $saleId . '  |  ' . $hashId . '  |  ' . $orderId);
                    }

                } else {

                    $withoutListTransactions['id'][] = $saleId;
                    $withoutListTransactions['hashId'][] = $hashId;
                    $withoutListTransactions['orderId'][] = $orderId;

                    $this->error('Nao existe list_transactions para: ' . $saleId . '  |  ' . $hashId . '  |  ' . $orderId);
                }
            } catch (Exception $e) {
                report($e);
            }
        }

        $this->info("");
        $this->info("");

        foreach ([
                     'withoutListTransactions',
                     'zeroListTransactions',
                     'withoutSummary',
                     'zeroSummary',
                     'withoutDetails',
                     'zeroDetails'
                 ] as $arrayToPrint) {

            if (
                count(${$arrayToPrint}['id']) > 0 ||
                count(${$arrayToPrint}['hashId']) > 0 ||
                count(${$arrayToPrint}['orderId']) > 0
            ) {


                $this->info(". . . . . . . . ");
                $this->info("");
                $this->info($arrayToPrint);
            }

            foreach (${$arrayToPrint} as $index => $values) {

                if (count($values)) {

                    $this->info("");
                    $this->info(' #### ' . $index);

                    if ($index == 'id') {

                        $content = $this->implodeInteger($values);
                    } else {

                        $content = $this->implodeString($values);
                    }
                    $this->comment($content);
                }
            }
        }
        $this->comment('............');
        $this->comment(now()->format('H:i:s'));
        $this->comment('Tempo em minutos: ' . now()->diffInMinutes($start));
        $this->comment('............');
    }

    private function implodeString($array)
    {

        return "'" . implode("', '", $array) . "'";
    }

    private function implodeInteger($array)
    {

        return implode(", ", $array);
    }
}
