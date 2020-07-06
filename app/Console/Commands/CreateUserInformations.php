<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Core\Entities\Project;
use Modules\Core\Entities\User;
use Modules\Core\Entities\UserInformation;
use Modules\Core\Services\ProjectNotificationService;
use Modules\Core\Services\UserService;

class CreateUserInformations extends Command
{
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'CreateUserInformations';
    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     * @return mixed
     */
    public function handle()
    {
        try {
            $userModel = new User();

            $users = $userModel->get();

            $userService = new UserService();

            foreach ($users as $user) {
                $userService->createUserInformationDefault($user->id);
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}
