<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Core\Entities\Withdrawal;
use Modules\Withdrawals\Services\WithdrawalService;

class GenericCommand extends Command
{
    protected $signature = 'generic';

    protected $description = 'Command description';

    public function handle()
    {
        $withdrawalService = new WithdrawalService();
        $withdrawals = Withdrawal::with('company.user')
            ->whereIn('id', [14054, 14036])
            ->get();

        foreach ($withdrawals as $withdrawal) {
            $isFirstUserWithdrawal = $withdrawalService->isFirstUserWithdrawal($withdrawal->company->user);
            $withdrawalService->processWithdrawal($withdrawal, $isFirstUserWithdrawal);
        }
    }
}
