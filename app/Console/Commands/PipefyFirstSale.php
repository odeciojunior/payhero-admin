<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
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
        $date = Carbon::today()->subDays(30);

        $users = User::selectRaw("users.*")
            ->selectRaw(
                "(  SELECT SUM(t.value) FROM transactions as t
                    JOIN companies as c ON c.id = t.company_id
                    WHERE t.user_id = users.id and t.status_enum IN (1,2)
                        AND t.created_at > '{$date}'
                    GROUP BY t.user_id ) as total_sale"
            )
            ->whereNotNull("users.pipefy_card_id");

        foreach ($users->cursor() as $user) {
            if (empty($user->total_sale)) {
                $phase = json_decode($user->pipefy_card_data);
                if (!empty($phase->phase) && $phase->phase == PipefyService::PHASE_ACTIVE_AND_SELLING) {
                    (new PipefyService())->updateCardLabel($user, [PipefyService::LABEL_WITHOUT_SELLING]); //30 dias sem vender
                }
            } elseif ($user->total_sale > 0) {
                (new PipefyService())->moveCardToPhase($user, PipefyService::PHASE_ACTIVE_AND_SELLING);
                (new PipefyService())->updateCardLabel($user, [PipefyService::LABEL_SALES_BETWEEN_0_100k]);
            }
        }

        return 0;
    }
}
