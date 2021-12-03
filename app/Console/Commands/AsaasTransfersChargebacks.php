<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Core\Entities\Gateway;
use Modules\Core\Entities\Transfer;
use Modules\Core\Services\Gateways\CheckoutGateway;

class AsaasTransfersChargebacks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'asaas:transfers-chargebacks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    protected $gatewayId = 0;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->gatewayId = foxutils()->isProduction() ? Gateway::ASAAS_PRODUCTION_ID : Gateway::ASAAS_SANDBOX_ID;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $transfers = Transfer::doesnthave('asaasTransfer')
        ->where('reason','chargedback')
        ->where('gateway_id',$this->gatewayId)
        ->get();

        $total = count($transfers);

        $this->comment("{$total} transfers para processar");

        $checkoutGateway = new CheckoutGateway($this->gatewayId);

        foreach($transfers as $transfer){
            $response = $checkoutGateway->transferSubSellerToSeller(
                $transfer->company_id, $transfer->value, $transfer->id
            );
            
            if(empty($response) || empty($response->status) || $response->status=='error'){                
                $this->error(str_pad($transfer->id,10,'.',STR_PAD_RIGHT).' Error');
            }else{
                $this->line(str_pad($transfer->id,10,'.',STR_PAD_RIGHT).' Done');
            }            
        }
    }
}
