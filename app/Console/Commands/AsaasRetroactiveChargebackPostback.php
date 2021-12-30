<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\Gateway;
use Modules\Core\Entities\GatewayPostback;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\SaleGatewayRequest;
use Modules\Core\Services\Gateways\CheckoutGateway;
use Vinkla\Hashids\Facades\Hashids;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;
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
        $this->gatewayId = Gateway::ASAAS_PRODUCTION_ID; // foxutils()->isProduction() ? Gateway::ASAAS_PRODUCTION_ID : Gateway::ASAAS_SANDBOX_ID;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $sales = DB::table('sales')->select('id')->where('created_at', '>', '2021-10-19 00:00:00')
                ->where('gateway_id',$this->gatewayId)
                ->where('status',Sale::STATUS_APPROVED)
                ->where('payment_method',Sale::CREDIT_CARD_PAYMENT)
                ->whereNotNull('gateway_transaction_id')
                ->where('id','>',1426315)
                ->get(); 

        $total = count($sales);

        $output = new ConsoleOutput();
        $progress = new ProgressBar($output, $total);
        $progress->start();

        Log::info(
            str_pad("Sale",15,' ',STR_PAD_RIGHT).
            str_pad("payment Id.",25,' ',STR_PAD_RIGHT).
            str_pad("Status",25,' ',STR_PAD_RIGHT).
            "paymentDate"
        ); 
        $checkoutService = new CheckoutGateway($this->gatewayId);
        foreach($sales as $sale)
        {
            $saleId = Hashids::encode($sale->id);
            $response = $checkoutService->getPaymentInfo($saleId);
            
            if(!empty($response->status) && $response->status =='success' && str_contains($response->data->status,'CHARGEBACK')){
                Log::info(
                    str_pad($sale->id,15,' ',STR_PAD_RIGHT).
                    str_pad($response->data->id,25,' ',STR_PAD_RIGHT).
                    str_pad($response->data->status,25,' ',STR_PAD_RIGHT).
                    $response->data->paymentDate
                ); 
            }
            Log::info('SaleId: '.$sale->id);
            $progress->advance();
        }

        $progress->finish();
    }

    public function createPostback($saleId,$status)
    {
        $saleGatewayRequest = SaleGatewayRequest::where('sale_id',$saleId)->last();
        if(!empty($saleGatewayRequest)){
            $data = json_decode($saleGatewayRequest->gateway_result,true);

            if(!empty($data['status'])){

                $data['status'] = 'CHARGEBACK_REQUESTED';
                $data = ['event'=>'PAYMENT_CHARGEBACK_REQUESTED','payment'=>$data];
    
                GatewayPostback::create([
                    'data' => json_encode($data),
                    'gateway_id' => $this->gatewayId,
                    'gateway_enum' => GatewayPostback::GATEWAY_ASAAS_ENUM,
                    'processed_flag' => false,
                    'postback_valid_flag' => false,
                ]);
            }
        }        
    }
}
