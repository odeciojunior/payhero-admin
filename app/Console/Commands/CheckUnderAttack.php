<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\UnderAttack;
use Modules\Core\Services\CloudFlareService;

class CheckUnderAttack extends Command
{
    protected $signature = 'check:underattack';

    protected $description = 'Verifica domínios que não estão sob ataque e atualiza no cloudflare';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {

        try {
            $cloudFlareService = new CloudFlareService();
            $underAttacks = UnderAttack::with('domain')
                ->where('type', 'DOMAIN')
                ->where('updated_at', '<', Carbon::now()->subHour())
                ->whereNull('removed_at')
                ->get();

            foreach ($underAttacks as $underAttack) {
                $sales = Sale::where('project_id', $underAttack->domain->project_id)
                    ->where('attempts', '>=', UnderAttack::MAX_ATTEMPT)
                    ->where('updated_at', '>=', Carbon::now()->subHour())
                    ->count();

                if (
                    $sales == 0 &&
                    $cloudFlareService->setSecurityLevel($underAttack->domain->cloudflare_domain_id, 'medium')
                ) {
                    $underAttack->update(['removed_at' => Carbon::now()]);
                }
            }
        } catch (Exception $e) {
            report($e);
        }

    }
}
