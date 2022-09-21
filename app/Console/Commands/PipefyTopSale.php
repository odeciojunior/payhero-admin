<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Modules\Core\Entities\Transaction;
use Modules\Core\Entities\User;
use Modules\Core\Services\Pipefy\PipefyService;

class PipefyTopSale extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "pipefy:top-sale";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Pipefy Card - 100k in 60 days";

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            $date = Carbon::today()->subDays(60);

            $transactionModel = new Transaction();
            $transactionPresent = $transactionModel->present();
            $transactions = User::join("transactions", "users.id", "transactions.user_id")
                ->join("companies", "companies.id", "transactions.company_id")
                ->whereIn("transactions.status_enum", [
                    $transactionPresent->getStatusEnum("paid"),
                    $transactionPresent->getStatusEnum("transfered"),
                ])
                ->whereNotNull("users.pipefy_card_id")
                ->where("users.created_at", ">", $date)
                ->groupBy("companies.user_id")
                ->selectRaw("companies.user_id, SUM(transactions.value) as value");

            foreach ($transactions->cursor() as $transaction) {
                if ($transaction->value >= 10000000) {
                    $user = User::where("id", $transaction->user_id)->first();
                    (new PipefyService())->updateCardLabel($user, PipefyService::LABEL_SALES_BETWEEN_100k_1M);
                }
            }
        } catch (\Exception $e) {
            report($e);
        }
        return 0;
    }
}
