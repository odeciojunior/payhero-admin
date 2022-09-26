<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Modules\Core\Entities\Transaction;
use Modules\Core\Entities\User;
use Modules\Core\Services\Pipefy\PipefyService;
use function Composer\Autoload\includeFile;

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
            $date = Carbon::today()->subDays(30);

            $transactionModel = new Transaction();
            $transactionPresent = $transactionModel->present();
            $transactions = User::join("transactions", "users.id", "transactions.user_id")
                ->join("companies", "companies.id", "transactions.company_id")
                ->whereIn("transactions.status_enum", [
                    $transactionPresent->getStatusEnum("paid"),
                    $transactionPresent->getStatusEnum("transfered"),
                ])
                ->whereNotNull("users.pipefy_card_id")
                ->where("transactions.created_at", ">", $date)
                ->groupBy("companies.user_id")
                ->selectRaw("companies.user_id, SUM(transactions.value) as value");

            foreach ($transactions->cursor() as $transaction) {
                $user = User::where("id", $transaction->user_id)->first();
                if ($transaction->value >= 10000000) {
                    (new PipefyService())->moveCardToPhase($user, PipefyService::PHASE_ACTIVE_AND_SELLING);
                    (new PipefyService())->updateCardLabel($user, [PipefyService::LABEL_SALES_BETWEEN_100k_1M]);
                }
            }
        } catch (\Exception $e) {
            report($e);
        }
        return 0;
    }
}
