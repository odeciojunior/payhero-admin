<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Modules\Core\Entities\User;
use Modules\Core\Services\BenefitsService;
use Illuminate\Support\Facades\Log;

class UserBenefitsUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:benefits:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to monitorize user benefits conditioned by account health score';

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

            $benefitsService = new BenefitsService();
            $now = now();
            $users = User::with('benefits')
                ->whereRaw('id = account_owner_id')
                ->get();

            foreach ($users as $user) {
                $this->line($user->id . ' - ' . $user->name);
                $benefitsService->updateUserBenefits($user);
            }

            $this->line($now);
            $this->line(now());

        } catch (Exception $e) {
            report($e);
        }

        Log::debug('command . ' . __CLASS__ . ' . finalizando em ' . date("d-m-Y H:i:s"));

        return 0;
    }
}
