<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Core\Entities\User;
use Modules\Core\Services\TaskService;

class UpdateUserCompletedTasks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tasks:update-completed-tasks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command updates user\'s completed tasks';

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

        foreach ($taskService->getUserTasks(User::find(26)) as $task) {
            $this->line($task->id);
            $this->line($task->name);
            $this->line($task->status);
        }
        dd();

//        $users = [
//            26, 557, 577, 42, 109, 152, 153, 154,
//            178, 271, 526, 534, 557, 717, 1542, 1829,
//            1837, 2073, 2100, 2159, 2174, 2239, 2366, 2367,
//            2387, 2498, 2588, 2877, 3155, 3195, 3227, 3241,
//            3301, 3420,
//        ];

//        foreach ($users as $id) {
//          $user = User::find($id);
        foreach (User::all() as $user) {
            if ($user->id == $user->account_owner_id) {
                $user->account_owner_id;
                $this->line($user->id . ' - ' . $user->account_owner_id . ' - ' . $user->name);
                $taskService->checkUserCompletedTasks($user);
            }
        }

        return 0;
    }
}
