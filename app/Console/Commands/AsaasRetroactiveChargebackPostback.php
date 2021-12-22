<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Core\Entities\Gateway;
use Modules\Core\Entities\Sale;
use Modules\Core\Services\Gateways\CheckoutGateway;
use Vinkla\Hashids\Facades\Hashids;

class AsaasRetroactiveChargebackPostback extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'asaas:retroactive-chargeback-postback';

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
        $sales = Sale::where('created_at', '>', '2021-10-19 00:00:00')
                ->where('gateway_id',$this->gatewayId)
                ->where('status',Sale::STATUS_APPROVED)
                ->where('payment_method',Sale::CREDIT_CARD_PAYMENT)
                ->whereNotNull('gateway_transaction_id')
                ->get();

        $checkoutService = new CheckoutGateway($this->gatewayId);
        foreach($sales as $sale){
            $saleId = Hashids::encode($sale->id);
            $response = $checkoutService->getPaymentInfo($saleId);
            dd($response);
            if($response->status =='success'){
            }
        }
    }
}
