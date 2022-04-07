<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Modules\Core\Entities\Benefit;
use Modules\Core\Entities\Transaction;
use Modules\Core\Entities\User;
use Modules\Core\Events\NotifyUserLevelEvent;
use Modules\Core\Services\BenefitsService;
class UpdateUserLevel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:update-user-level';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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

        try {

            $transactionModel = new Transaction;
            $transactionPresent = $transactionModel->present();
            $transactions = $transactionModel->join('companies', 'companies.id', 'transactions.company_id')
                ->whereIn('transactions.status_enum', [$transactionPresent->getStatusEnum('paid'), $transactionPresent->getStatusEnum('transfered')])
                ->groupBy('companies.user_id')
                ->selectRaw('companies.user_id, SUM(transactions.value) as value');

            foreach ($transactions->cursor() as $transaction) {
                if ($transaction->value > 10000000000) {
                    $level = 6;
                } elseif ($transaction->value > 5000000000) {
                    $level = 5;
                } elseif ($transaction->value > 1000000000) {
                    $level = 4;
                } elseif ($transaction->value > 100000000) {
                    $level = 3;
                } elseif ($transaction->value > 10000000) {
                    $level = 2;
                } else {
                    $level = 1;
                }

                $user = User::with(['benefits' => function($query) {
                    $query->where('benefit_id', '!=', 2); // r+r
                }])->find($transaction->user_id);

                if (!empty($user)) {
                    $this->line("Verficando o usuário: {$user->name} ({$user->id})...");

                    $previousLevel = $user->level;

                    $user->update([
                                      'total_commission_value' => $transaction->value,
                                      'level' => $level
                                  ]);

                    if ($previousLevel != $level) {
                        $this->info("Nível {$previousLevel} -> {$level}");
                        //TODO: remove after running the first time in production
                        if ($previousLevel == 0) {
                            $benefits = $user->benefits
                                ->where('enabled', 0)
                                ->where('level', '<=', $level);
                        } else {
                            $benefits = $user->benefits
                                ->where('enabled', 0)
                                ->where('level', $level);
                        }

                        foreach ($benefits as $benefit) {
                            $benefit->enabled = 1;
                            $benefit->save();
                        }

                        BenefitsService::updateUserBenefits($user);

                        if ($user->level > 1) {
                            event(new NotifyUserLevelEvent(User::find($user->id), $user->level));
                        }
                    }
                }
            }

        } catch (Exception $e) {
            report($e);
        }

        return 0;
    }
}
