<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\Task;
use Modules\Core\Entities\Transaction;
use Modules\Core\Entities\User;
use Modules\Core\Services\FoxUtils;
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
        $taskService = new TaskService();
        $now = now();
        $this->line('');
        $this->line('First sale Task');
        $this->line('--------------------------------------------------');

        $firstSaleUsers = User::whereDoesntHave('tasks', function ($query) {
            $query->where('id', Task::TASK_FIRST_SALE);
        })->get();

        $firstSaleTask = Task::find(Task::TASK_FIRST_SALE);
        foreach ($firstSaleUsers as $user) {
            if ($user->id == $user->account_owner_id) {
                $this->line($user->id . ' - ' . $user->name);
                $taskService->checkCompletedTask($user, $firstSaleTask);
            }
        }

        $this->line('');
        $this->line('First R$ 1000 revenue Task');
        $this->line('--------------------------------------------------');

        $first1000RevenueUsers = User::whereDoesntHave('tasks', function ($query) {
            $query->where('id', Task::TASK_FIRST_SALE);
        })->get();

        $first1000RevenueTask = Task::find(Task::TASK_FIRST_1000_REVENUE);
        foreach ($first1000RevenueUsers as $user) {
            if ($user->id == $user->account_owner_id) {
                $this->line($user->id . ' - ' . $user->name);
                $taskService->checkCompletedTask($user, $first1000RevenueTask);
            }
        }

        $this->line($now);
        $this->line(now());
        return 0;
    }
}
