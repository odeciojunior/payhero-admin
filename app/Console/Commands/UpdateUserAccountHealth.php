<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Modules\Core\Entities\User;
use Modules\Core\Services\AccountHealthService;

class UpdateUserAccountHealth extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "account-health:update";

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
        try {
            $accountHealthService = new AccountHealthService();
            $users = User::whereRaw("id = account_owner_id")->get();
            foreach ($users as $user) {
                $accountHealthService->updateAccountScore($user);
            }
        } catch (Exception $e) {
            report($e);
        }
    }
}
