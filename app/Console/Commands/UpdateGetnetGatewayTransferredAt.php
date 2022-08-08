<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\Gateway;
use Modules\Core\Entities\Withdrawal;
use Modules\Core\Services\FoxUtils;
use Modules\Core\Services\GetnetBackOfficeService;
use Vinkla\Hashids\Facades\Hashids;

class UpdateGetnetGatewayTransferredAt extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "update:getnet_gateway_transferred_at";

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
     * @return void     */
    public function handle()
    {
        try {
            $withdrawals = Withdrawal::with("transactions")
                ->whereHas("transactions", function ($q) {
                    $q->whereNull("gateway_transferred_at")->whereIn("gateway_id", [
                        Gateway::GETNET_SANDBOX_ID,
                        Gateway::GETNET_PRODUCTION_ID,
                        Gateway::GERENCIANET_PRODUCTION_ID,
                    ]);
                })
                ->groupBy("withdrawals.id")
                ->orderBy("withdrawals.id", "desc");

            foreach ($withdrawals->cursor() as $withdrawal) {
                $this->info("Começando withdrawal id: " . $withdrawal->id);
                $this->updateTransaction($withdrawal);
                $this->info("Fim withdrawal id: " . $withdrawal->id);
            }
        } catch (Exception $e) {
            report($e);
        }
    }

    private function updateTransaction($withdrawal)
    {
        $transactions = $withdrawal
            ->transactions()
            ->whereNull("gateway_transferred_at")
            ->whereIn("gateway_id", [
                Gateway::GETNET_SANDBOX_ID,
                Gateway::GETNET_PRODUCTION_ID,
                Gateway::GERENCIANET_PRODUCTION_ID,
            ])
            ->orderBy("id", "desc");

        DB::transaction(function () use ($transactions) {
            $getnetService = new GetnetBackOfficeService();
            $i = 0;
            foreach ($transactions->cursor() as $transaction) {
                if ($i == 4) {
                    sleep(1);
                    $i = 0;
                }
                try {
                    $this->line(" Atualizando a transação: " . $transaction->id . " Count: " . $i);

                    if (empty($transaction->company_id)) {
                        continue;
                    }
                    $sale = $transaction->sale;
                    $saleIdEncoded = Hashids::connection("sale_id")->encode($sale->id);

                    if ($transaction->gateway_id == Gateway::GERENCIANET_PRODUCTION_ID) {
                        if ($transaction->gateway_transferred === 1) {
                            $transaction->update([
                                "gateway_transferred_at" => $transaction->gateway_released_at, //date transferred
                            ]);
                        }
                    } else {
                        $i++;
                        if (FoxUtils::isProduction()) {
                            $subsellerId = $transaction->company->getGatewaySubsellerId(Gateway::GETNET_PRODUCTION_ID);
                        } else {
                            $subsellerId = $transaction->company->getGatewaySubsellerId(Gateway::GETNET_SANDBOX_ID);
                        }

                        $getnetService->setStatementSubSellerId($subsellerId)->setStatementSaleHashId($saleIdEncoded);

                        $result = json_decode($getnetService->getStatement());

                        if (
                            !empty($result->list_transactions[0]) &&
                            !empty($result->list_transactions[0]->details[0]) &&
                            !empty($result->list_transactions[0]->details[0]->subseller_rate_confirm_date)
                        ) {
                            $date = Carbon::parse(
                                $result->list_transactions[0]->details[0]->subseller_rate_confirm_date
                            );

                            $this->line($date);
                            $transaction->update([
                                "gateway_transferred_at" => $date, //date transferred
                                "gateway_transferred" => 1,
                            ]);
                        }
                    }
                } catch (Exception $e) {
                    report($e);
                }
            }
        });
    }
}
