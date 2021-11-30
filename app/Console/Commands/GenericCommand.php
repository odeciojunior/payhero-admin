<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\Gateway;
use Modules\Core\Entities\Withdrawal;

class GenericCommand extends Command
{
    protected $signature = 'generic';

    protected $description = 'Command description';

    public function handle()
    {
        $withdrawals = Withdrawal::where('gateway_id', Gateway::ASAAS_PRODUCTION_ID)
                                    ->whereBetween(DB::raw('TIME(created_at)'), ['00:08:00', '00:18:59'])->get();

        foreach($withdrawals as $withdrawal) {
            $this->line($withdrawal->id);
            $this->line($withdrawal->company->fantasy_name);
            $this->line($withdrawal->company->user->name);
            $this->line(foxutils()->formatMoney($withdrawal->value / 100));
            $this->line('------------------------');
        }
    }

}
