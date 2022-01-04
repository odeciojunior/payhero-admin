<?php

namespace App\Console\Commands;

use Illuminate\Auth\Access\Gate;
use Illuminate\Console\Command;
use Modules\Core\Entities\Gateway;
use Modules\Core\Entities\Transfer;
use Modules\Core\Services\Gateways\CheckoutGateway;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;

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
        $transfers = Transfer::whereDoesntHave('asaasTransfer',function($qr){
            $qr->where('status','DONE');
        })
        ->whereHas('transaction',function($q){
            $q->where('gateway_id',Gateway::GETNET_PRODUCTION_ID);
        })
        ->where('reason','chargedback')
        ->where('gateway_id',$this->gatewayId)
        ->get();

        $total = count($transfers);

        $output = new ConsoleOutput();
        $progress = new ProgressBar($output, $total);
        $progress->start();

        $checkoutGateway = new CheckoutGateway($this->gatewayId);

        foreach($transfers as $transfer){

            $progress->advance();

            $response = $checkoutGateway->transferSubSellerToSeller(
                $transfer->company_id, $transfer->value, $transfer->id
            );
            
            if(empty($response) || empty($response->status) || $response->status=='error'){                
                $this->error(str_pad($transfer->id,10,'.',STR_PAD_RIGHT).' Error');
                continue;
            }

            $this->line(str_pad($transfer->id,10,'.',STR_PAD_RIGHT).' Done');                           
        }

        $progress->finish();
    }
}



