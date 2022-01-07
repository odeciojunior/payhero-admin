<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Core\Entities\Task;
use Modules\Core\Entities\User;
use Modules\Core\Services\TaskService;

class GenericCommand extends Command
{
    protected $signature = 'generic';

    protected $description = 'Command description';

    public function handle()
    {
        $users = User::where('status', 1)
            ->where('account_is_approved', 1)
            ->whereNull('deleted_at')
            ->whereIn(
                'id',
                [
                    24,
                    207,
                    501,
                    1094,
                    2060,
                    2959,
                    3460,
                    3819,
                    4157,
                    4164,
                    4345,
                    4436,
                    4610,
                    4742,
                    4756,
                    4846,
                    5032,
                    5135,
                    5269,
                    5357,
                    5531,
                    5609,
                    5655,
                    5673,
                    5766,
                    5775,
                    5783,
                    5786,
                    5820,
                    5823,
                    5910,
                    5911,
                    5963,
                    5966,
                    5988,
                    6053,
                    6073,
                    6074,
                    6094,
                    6095
                ]
            )->get();

        $task = Task::find(Task::TASK_FIRST_WITHDRAWAL);
        foreach ($users as $user) {
            $this->line('Task set to ' . $user->name);
            TaskService::setCompletedTask($user, $task);
        }
    }

}
