<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\Gateway;
use Modules\Core\Entities\Sale;
use Modules\Core\Services\Gateways\Safe2payGateway;

class ResendBankSlipWebhookSafe2pay extends Command
{
    public const SAFE2PAY_STATUS_PROCESSAMENTO = 2;
    public const SAFE2PAY_STATUS_AUTORIZADO = 3;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "safe2pay:resend-bankslip-webhook";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Command description";

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
        $this->verifyBankSlip(self::SAFE2PAY_STATUS_PROCESSAMENTO);
        $this->verifyBankSlip(self::SAFE2PAY_STATUS_AUTORIZADO);
    }

    public function verifyBankSlip($option)
    {
        $safe = new Safe2payGateway();

        $pageNumber = 1;
        $limit = 100;
        $total = 0;
        $itens = 0;

        $sales = DB::table("sales")
            ->select("id", "status", "gateway_transaction_id")
            ->where("gateway_id", Gateway::SAFE2PAY_PRODUCTION_ID)
            ->where("status", [Sale::STATUS_PENDING])
            ->get();

        if (count($sales) == 0) {
            exit();
        }

        do {
            $response = $safe->listTransactions([
                "PageNumber" => $pageNumber,
                "RowsPerPage" => $limit,
                "Object.PaymentMethod.Code" => 1,
                "Object.TransactionStatus.Code" => $option,
            ]);

            $total = 0;
            if (!empty($response->ResponseDetail)) {
                $total = $response->ResponseDetail->TotalItems;
                $pageNumber++;

                foreach ($response->ResponseDetail->Objects as $row) {
                    $this->line($row->Reference);
                    $itens++;

                    foreach ($sales as $key => $sale) {
                        if ($sale->gateway_transaction_id == $row->IdTransaction) {
                            $this->comment($sale->id);
                            $safe->resendWebhook($sale->gateway_transaction_id);

                            unset($sales[$key]);
                        }
                    }
                }

                if (count($sales) == 0) {
                    exit();
                }
            }
        } while ($itens < $total);
    }
}
