<?php

namespace App\Console\Commands;

use Exception;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Modules\Core\Entities\Gateway;
use Modules\Core\Entities\Task;
use Modules\Core\Entities\Transaction;
use Modules\Core\Entities\User;
use Modules\Core\Entities\Withdrawal;
use Modules\Core\Services\CompanyService;
use Modules\Core\Services\GetnetBackOfficeService;
use Modules\Core\Services\TaskService;

class CheckWithdrawalsLiquidated extends Command
{
    protected $signature = 'getnet:check-withdrawals-liquidated';

    protected $description = 'Command description';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        try {
            $withdrawals = Withdrawal::with('transactions', 'transactions.sale', 'transactions.company')
                ->where('gateway_id', Gateway::GETNET_PRODUCTION_ID)
                ->where('automatic_liquidation', true)
                ->whereIn('status', [Withdrawal::STATUS_LIQUIDATING, Withdrawal::STATUS_PARTIALLY_LIQUIDATED])
                ->orderBy('id');

            $withdrawals->chunk(500, function ($withdrawals) {
                foreach ($withdrawals as $withdrawal) {
                    $getnetService = new GetnetBackOfficeService();

                    $withdrawalTransactionsCount = $withdrawal->transactions->count();
                    $countTransactionsLiquidated = 0;

                    foreach ($withdrawal->transactions as $transaction) {
                        $sale = $transaction->sale;

                        if (!empty($transaction->gateway_transferred_at)) {
                            $countTransactionsLiquidated++;
                            continue;
                        }

                        $orderId = $sale->gateway_order_id;

                        $response = $getnetService->setStatementSaleHashId(hashids_encode($sale->id, 'sale_id'))
                            ->setStatementSubSellerId(CompanyService::getSubsellerId($transaction->company))
                            ->getStatement($orderId);

                        $gatewaySale = json_decode($response);

                        if (
                            !empty($gatewaySale->list_transactions[0]) &&
                            !empty($gatewaySale->list_transactions[0]->details[0]) &&
                            !empty($gatewaySale->list_transactions[0]->details[0]->subseller_rate_confirm_date)
                        ) {
                            $countTransactionsLiquidated++;

                            $date = Carbon::parse($gatewaySale->list_transactions[0]->details[0]->subseller_rate_confirm_date);

                            if (empty($transaction->gateway_transferred_at)) {
                                $transaction->update([
                                    'status' => 'transfered',
                                    'status_enum' => Transaction::STATUS_TRANSFERRED,
                                    'gateway_transferred' => true,
                                    'gateway_transferred_at' => $date
                                ]);
                            }
                        }
                    }

                    if ($countTransactionsLiquidated == $withdrawalTransactionsCount) {
                        $withdrawal->update(['status' => Withdrawal::STATUS_TRANSFERRED]);

                        TaskService::setCompletedTask(
                            User::find($sale->owner_id),
                            Task::find(Task::TASK_FIRST_WITHDRAWAL)
                        );
                    } elseif ($countTransactionsLiquidated > 0) {
                        $withdrawal->update(['status' => Withdrawal::STATUS_PARTIALLY_LIQUIDATED]);
                    }
                }
            });
        } catch (Exception $e) {
            report($e);
        }
    }
}
