<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\Gateway;
use Modules\Core\Entities\GatewayPostback;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\SaleContestation;
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
        $this->gatewayId = Gateway::ASAAS_PRODUCTION_ID; // : Gateway::ASAAS_SANDBOX_ID;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {        
        $this->updateNsuContestation();        
    }

    public function updateNsuContestation(){
        $contestations = SaleContestation::where('gateway_id',8)->where('status',SaleContestation::STATUS_IN_PROGRESS)
        ->whereNull('nsu')->get();
       
        foreach($contestations as $contestation){
            if(!empty($contestation->sale_id)){
                $request = DB::table('sale_gateway_requests')->select('id','gateway_result')
                ->where('sale_id',$contestation->sale_id)->where('gateway_result','like','%CONFIRMED%CREDIT_CARD%')->orderBy('id','DESC')->first();
                if(!empty($request)){
                    $data = json_decode($request->gateway_result);  
                    if(!empty($data->installment) || !empty($data->invoiceNumber))
                    {                        
                        $contestation->nsu = $data->installment??$data->invoiceNumber;
                        $contestation->update();
                        $this->line('Atualizando contestation '.$contestation->id);                        
                    }else{
                        $this->error('sale_request id: '.$request->id);
                    }
                }
            }
        }
    }

    public function reprocessGatewayPostback(){
        $postbacks = GatewayPostback::where('gateway_id',8)->where('data', 'like','%PAYMENT_CHARGEBACK_REQUESTED%')->whereDate('created_at',now())->get();        

        $output = new ConsoleOutput();
        $progress = new ProgressBar($output, count($postbacks));
        $progress->start();

        $sales = [];
        foreach($postbacks as $postback){
            if(!in_array($postback->sale_id,$sales) && $postback->id <> 1247278){
                $postback->processed_flag = false;
                $this->line($postback->id);
                $postback->update();        
                $sales[] = $postback->sale_id;
            }
            $progress->advance();
        }

        $progress->finish();

        $this->comment("processados:".count($sales));
    }

    public function createPostbackRetroactive(){
        $gateway_postbacks = array(           
            array(
                "sale_id" => 1376233,
            ),
            array(
                "sale_id" => 1379013,
            )           
        );
        

        $output = new ConsoleOutput();
        $progress = new ProgressBar($output, count($gateway_postbacks));
        $progress->start();

        foreach($gateway_postbacks as $row){
            $requestExist = GatewayPostback::where('sale_id',$row['sale_id'])->where('data','like','%PAYMENT_CHARGEBACK_REQUESTED%')->exists();
            if ($requestExist) {
                continue;
            }
            Log::info('criando postback para sale id '.$row['sale_id']);
            $this->createPostback($row['sale_id'],'CHARGEBACK_REQUESTED');
            $progress->advance();
        }

        $progress->finish();
    }
    
    public function createPostback($saleId,$status)
    {
        $saleGatewayRequest = SaleGatewayRequest::where('sale_id',$saleId)->where('send_data','like','%split%')->orderBy('id','desc')->first();
        
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
                    'created_at'=>'2021-12-20 14:49:57'
                ]);
            }
        }        
    }

    public function collectChargeback()
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
}
