<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Modules\Core\Entities\Gateway;
use Modules\Core\Entities\Sale;
use Modules\Core\Services\GetnetBackOfficeService;
use Modules\Core\Services\GetnetPaymentService;

class CheckRefunded extends Command
{
    protected $signature = 'getnet:check-refunded';

    protected $description = 'Command description';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $getnetService = new GetnetBackOfficeService();
        $getnetPaymentService = new GetnetPaymentService();

        $endDate = Carbon::yesterday()->format('Y-m-d');

        $sales = Sale::where('gateway_id', Gateway::GETNET_PRODUCTION_ID)
            ->where('status', Sale::STATUS_REFUNDED)
            ->where('payment_method', Sale::CREDIT_CARD_PAYMENT)
            ->whereDate('date_refunded', '>=', $endDate . ' 00:00:00')
            ->whereDate('date_refunded', '<=', $endDate . ' 23:59:59')
            ->get();

        foreach ($sales as $sale) {
            $response = $getnetService->setStatementSaleHashId(hashids_encode($sale->id, 'sale_id'))
                ->getStatement();
            $result = json_decode($response);

            if (isset($result->list_transactions)
                && isset($result->list_transactions[0])
                && isset($result->list_transactions[0]->details)
                && isset($result->list_transactions[0]->details[0])
                && isset($result->list_transactions[0]->details[0]->release_status)
                && isset($result->list_transactions[0]->summary)
                && isset($result->list_transactions[0]->summary->transaction_status_code)
            ) {
                $status = $result->list_transactions[0]->summary->transaction_status_code ?? null;

                if (!in_array($status, [70, 92])) {
                    if ($sale->total_paid_value > 0) {
                        $value = $sale->total_paid_value * 100;
                    } else {
                        $value = $sale->original_total_paid_value;
                    }

                    $getnetPaymentService->cancelPayment($sale->id, $sale->gateway_transaction_id, $value);
                }
            } else {
                $errorGetnet = 'Erro na estrutura da venda da Getnet. $sale->id = ' . $sale->id . ' $orderId = ' . $sale->gateway_order_id;

                $this->warn($errorGetnet);
                report(new Exception($errorGetnet));
            }
        }

        return 0;
    }
}
