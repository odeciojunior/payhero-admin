<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Modules\Core\Entities\User;
use Modules\Core\Services\Pipefy\PipefyService;

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
        try {
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
                $labelAd = "";
                if (!empty($user->utm_srcs)) {
                    $utmSrcs = json_decode($user->utm_srcs, true);
                    if (!empty($utmSrcs["utm_source"])) {
                        if ($utmSrcs["utm_source"] == "google_ads") {
                            $labelAd = PipefyService::LABEL_GOOGLE_ADS;
                        } elseif ($utmSrcs["utm_source"] == "facebook_ads") {
                            $labelAd = PipefyService::LABEL_FACEBOOK_ADS;
                        }
                    }
                }
                if (empty($user->total_sale)) {
                    $phase = json_decode($user->pipefy_card_data);
                    if (!empty($phase->phase) && $phase->phase == PipefyService::PHASE_ACTIVE_AND_SELLING) {
                        (new PipefyService())->updateCardLabel($user, [PipefyService::LABEL_WITHOUT_SELLING, $labelAd]); //30 dias sem vender
                    }
                } elseif ($user->total_sale > 0) {
                    (new PipefyService())->moveCardToPhase($user, PipefyService::PHASE_ACTIVE_AND_SELLING);
                    (new PipefyService())->updateCardLabel($user, [
                        PipefyService::LABEL_SALES_BETWEEN_0_100k,
                        $labelAd,
                    ]);
                }
            }

            return 0;
        } catch (Exception $e) {
            report($e);
            return false;
        }
    }
}
