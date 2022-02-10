<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Modules\Core\Entities\Sale;
use Modules\Core\Services\GetnetBackOfficeService;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Support\Facades\Log;

class CheckSalesRefunded extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:sales-refunded';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verificar as sales estornadas sem detalhes ';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Log::debug('command . ' . __CLASS__ . ' . iniciando em ' . date("d-m-Y H:i:s"));

        try {

            $getnetService = new GetnetBackOfficeService();

            $sales = Sale::where('gateway_id', Sale::GETNET_PRODUCTION_ID)
                ->where('status', Sale::STATUS_REFUNDED)
                ->where('payment_method', Sale::CREDIT_CARD_PAYMENT);

            $sales->chunk(
                500,
                function ($sales) use ($getnetService) {

                    foreach ($sales as $sale) {
                        $orderId = $sale->gateway_order_id;
                        $result = json_decode($getnetService->setStatementSaleHashId(Hashids::connection('sale_id')->encode($sale->id))->getStatement($orderId));
                        $response = $this->checkIfExistsRefundedSummary($result->list_transactions, $sale);

                        if (!$response) {
                            $this->line($sale->id . ',');
                            report(new Exception("Erro ao realizar o estorno da venda: " . $sale->id));
                        }
                    }
                });

            return 0;
        } catch (Exception $e) {
            report($e);
        }

        Log::debug('command . ' . __CLASS__ . ' . finalizando em ' . date("d-m-Y H:i:s"));
        
    }

    public function checkIfExistsRefundedSummary($listTransactions, $sale) {

        foreach ($listTransactions as $listTransaction) {
            if( empty($listTransaction->details)
                && isset($listTransaction->summary->transaction_status_code)
                && $listTransaction->summary->transaction_status_code == '70' )
            {
                $saleLogs = $sale->saleLogs->where('status_enum', Sale::STATUS_REFUNDED)->first();
                if (!empty($saleLogs) && $saleLogs->created_at->diffInDays(Carbon::now()) > 1){
                    $this->line($listTransaction->summary->transaction_status_code . ' - ' . $sale->id);
                    report(new Exception("Venda " . $sale->id . " foi estornada a mais de um dia e está com status de  aguardando."));
                }
                //$this->line($listTransaction->summary->transaction_status_code . ' - ' . $sale->id);

                return true;
            }

            if (
                isset($listTransaction->details)
                && !empty($listTransaction->details)
                && $listTransaction->summary->transaction_status_code == '92'
            ) {
                return true;
            }
        }

        return false;
    }
}
