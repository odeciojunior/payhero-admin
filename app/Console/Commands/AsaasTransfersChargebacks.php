<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Core\Entities\Gateway;
use Modules\Core\Entities\Transfer;

class AsaasTransfersChargebacks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:asaas-transfers-chargebacks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $transfers = Transfer::doesnthave('asaasTransfer')->where('reason','chargedback')
        ->where('gateway_id',foxutils()->isProduction() ? Gateway::ASAAS_PRODUCTION_ID : Gateway::ASAAS_SANDBOX_ID)
        ->get();

        // foreach($transfers as $transfer){
            
        // }
    }
}
