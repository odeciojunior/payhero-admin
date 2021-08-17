<?php

namespace App\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Core\Entities\Withdrawal;
use Modules\Withdrawals\Services\WithdrawalService;

class ProcessWithdrawals implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public Withdrawal $withdrawal;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( Withdrawal $withdrawal)
    {
        $this->withdrawal = $withdrawal;

    }

    public function tags()
    {
        return ['process-withdrawal-store'];
    }

    public function handle()
    {
        try {

            $withdrawalService = new WithdrawalService();

            $responseCreateWithdrawal = $withdrawalService->processWithdrawal($this->withdrawal);


        } catch (Exception $e) {
            report($e);
            return false;
        }
    }
}
