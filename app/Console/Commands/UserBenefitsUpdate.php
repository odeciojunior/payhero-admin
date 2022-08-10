<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Modules\Core\Entities\User;
use Modules\Core\Services\BenefitsService;

class UserBenefitsUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "user:benefits:update";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Command to monitorize user benefits conditioned by account health score";

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
            $benefitsService = new BenefitsService();
            $now = now();
            $users = User::with("benefits")
                ->whereRaw("id = account_owner_id")
                ->get();

            foreach ($users as $user) {
                $this->line($user->id . " - " . $user->name);
                $benefitsService->updateUserBenefits($user);
            }

            $this->line($now);
            $this->line(now());
        } catch (Exception $e) {
            report($e);
        }

        return 0;
    }
}
