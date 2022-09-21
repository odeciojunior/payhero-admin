<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Core\Entities\User;
use Modules\Core\Services\Pipefy\PipefyService;
use function Composer\Autoload\includeFile;

class PipefyFirstSale extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "pipefy:first-sale";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Pipefy Card - First Sale";

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $users = User::whereNotNull("users.pipefy_card_id");
        
        foreach ($users->cursor() as $user) {
            if ($user->total_commission_value > 0) {
                (new PipefyService())->moveCardToPhase($user, PipefyService::PHASE_ACTIVE_AND_SELLING);
                (new PipefyService())->updateCardLabel($user, [PipefyService::LABEL_SALES_BETWEEN_0_100k]);
            }
        }

        return 0;
    }
}
