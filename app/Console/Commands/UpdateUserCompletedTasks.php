<?php

namespace App\Console\Commands;

use Exception;
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
    protected $signature = "tasks:update-completed-tasks";

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
        try {
            $taskService = new TaskService();
            $now = now();
            $users = User::with("tasks")
                ->whereRaw("id = account_owner_id")
                ->get();

            foreach ($users as $user) {
                $this->line($user->id . " - " . $user->name);
                $taskService->checkUserCompletedTasks($user);
            }

            $this->line($now);
            $this->line(now());
        } catch (Exception $e) {
            report($e);
        }

        return 0;
    }
}
