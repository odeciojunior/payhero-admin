<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\Task;
use Modules\Core\Entities\Transaction;
use Modules\Core\Entities\User;
use Modules\Core\Services\TaskService;

class CheckUserCompletedSalesTasks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tasks:check-completed-sales-tasks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command updates user\'s completed sales tasks';

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

        Log::debug('command . ' . __CLASS__ . ' . iniciando em ' . date("d-m-Y H:i:s"));

        try {

            $now = now();
            $this->line('');
            $this->line('First sale Task');
            $this->line('--------------------------------------------------');

            $firstSaleUsers = User::with('tasks')
                ->whereDoesntHave('tasks', function ($query) {
                    $query->where('id', Task::TASK_FIRST_SALE);
                })->whereHas('sales', function ($q) {
                    $q->whereIn('status', [
                        Sale::STATUS_APPROVED,
                        Sale::STATUS_CHARGEBACK,
                        Sale::STATUS_REFUNDED,
                        Sale::STATUS_IN_DISPUTE
                    ])->limit(1);
                })
                ->whereRaw('id = account_owner_id')
                ->get();

            foreach ($firstSaleUsers as $user) {
                $this->line($user->id . ' - ' . $user->name);
                TaskService::setCompletedTask($user, Task::find(Task::TASK_FIRST_SALE));
            }

            unset($firstSaleTask);

            $this->line('');
            $this->line('First R$ 1000 revenue Task');
            $this->line('--------------------------------------------------');

            $first1000RevenueUsers = User::with('tasks')
                ->selectRaw('users.id, sum(transactions.value) as total_value')
                ->join('transactions', 'transactions.user_id', 'users.id')
                ->whereNotExists(function ($query) {
                    $query->select('*')
                        ->from('tasks_users')
                        ->whereRaw('tasks_users.task_id = ' . Task::TASK_FIRST_1000_REVENUE)
                        ->whereRaw('users.id = tasks_users.user_id')->limit(1);
                })
                ->whereRaw('users.id = account_owner_id')
                ->whereIn('transactions.status_enum', [
                    Transaction::STATUS_TRANSFERRED,
                    Transaction::STATUS_PAID
                ])
                ->havingRaw('total_value > 100000')
                ->groupBy('users.id')
                ->get();

            foreach ($first1000RevenueUsers as $user) {
                $user = User::with('tasks')->find($user->id);
                $this->line($user->id . ' - ' . $user->name);
                TaskService::setCompletedTask($user, Task::find(Task::TASK_FIRST_1000_REVENUE));
            }

            $this->line($now);
            $this->line(now());
            return 0;

        } catch (Exception $e) {
            report($e);
        }

        Log::debug('command . ' . __CLASS__ . ' . finalizando em ' . date("d-m-Y H:i:s"));

    }
}
