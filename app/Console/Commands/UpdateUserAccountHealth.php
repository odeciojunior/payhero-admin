<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\User;
use Modules\Core\Services\AccountHealthService;
use Modules\Core\Services\AttendanceService;
use phpDocumentor\Reflection\DocBlock\Tags\Link;

class UpdateUserAccountHealth extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'account-health:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command updates user\'s account health stats';

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
        $accountHealthService = new AccountHealthService();
        $users = [
            26, 557, 577, 42, 109, 152, 153, 154,
            178, 271, 526, 534, 557, 717, 1542, 1829,
            1837, 2073, 2100, 2159, 2174, 2239, 2366, 2367,
            2387, 2498, 2588, 2877, 3155, 3195, 3227, 3241,
            3301, 3420,
        ];

        foreach ($users as $id) {
            if (Sale::where('gateway_id', 15)
                    ->where('payment_method', Sale::PAYMENT_TYPE_CREDIT_CARD)
                    ->whereIn('status', [
                        Sale::STATUS_APPROVED,
                        Sale::STATUS_CHARGEBACK,
                        Sale::STATUS_REFUNDED,
                        Sale::STATUS_IN_DISPUTE
                    ])->where('owner_id', $id)->count() < 100) {
                continue;
            }

            $user = User::find($id);
            $this->line('user: ' . $user->name);
            $this->line('attendance score: ' . $att = $accountHealthService->getAttendanceScore($user));
            $this->line('tracking score: ' . $trck = $accountHealthService->getTrackingScore($user));
            $this->line('chargeback score: ' . $cb = $accountHealthService->getChargebackScore($user));
            $this->line('partial account health: ' . round(($att + $trck + $cb) / 3, 2));
            $this->line('------------------------------------------------------------------');
        }

        dd('it\'s the end...');

        foreach (User::all() as $user) {
            $accountHealthService->updateAccountScore($user);
        }
        return 0;
    }
}
