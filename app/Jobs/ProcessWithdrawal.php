<?php

namespace App\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Core\Entities\Withdrawal;
use Modules\Core\Services\Gateways\GetnetService;
use Modules\Withdrawals\Services\WithdrawalService;

class ProcessWithdrawal implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public Withdrawal $withdrawal;
    public $isFirstUserWithdrawal;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Withdrawal $withdrawal, $isFirstUserWithdrawal)
    {
        $this->withdrawal = $withdrawal;
        $this->isFirstUserWithdrawal = $isFirstUserWithdrawal;
    }

    public function tags()
    {
        return ["process-withdrawal-store"];
    }

    public function handle()
    {
        try {
            $getnetService = new GetnetService();
            $getnetService->setCompany($this->withdrawal->company);

            $getnetService->processWithdrawal($this->withdrawal, $this->isFirstUserWithdrawal);
        } catch (Exception $e) {
            report($e);
            return false;
        }
    }
}
