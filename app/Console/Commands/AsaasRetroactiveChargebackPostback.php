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
        $this->gatewayId = foxutils()->isProduction() ? Gateway::ASAAS_PRODUCTION_ID : Gateway::ASAAS_SANDBOX_ID;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        /* VENDAS CHARGEBACK PRODUÇÃO */
        $saleIds = [1362901,1368162,1368623,1368762,1368977,1370087,1370114,1370138,1371066,1371470,1372036,1372097,1373680,1373793,1373798,1373891,
        1374053,1374214,1374272,1374874,1374880,1375402,1375460,1375468,1375471,1375481,1375513,1377568,1378070,1378074,1378687,1379013,1379360,
        1379381,1379672,1380922,1381187, 1381363, 1381395, 1382743, 1383070, 1383692, 1383695, 1383710, 1383880, 1384298, 1384884, 1385103, 1385142,
        1385306, 1385314, 1385797, 1386312, 1386550, 1387289, 1387793, 1387982, 1388084, 1388099, 1388221, 1388266, 1388271, 1388298, 1388343, 1388750,
        1388976, 1389242, 1389412, 1389441, 1389675, 1389788, 1389953, 1389960, 1390095, 1390654, 1390986, 1391088, 1391175, 1391187, 1391617, 1391977, 
        1392029, 1392038, 1392288, 1392290, 1392762, 1392804, 1392862, 1393108, 1393565, 1393726, 1393784, 1393791, 1393985, 1394185, 1393726, 1393784, 
        1393791, 1393985, 1394185, 1394239, 1394256, 1394265, 1394444, 1395154, 1395270, 1395344, 1395543, 1395559, 1395813, 1396091, 1396182, 1397296, 
        1397471, 1397479, 1398099, 1398952, 1399160, 1399184, 1399359, 1399447, 1399503, 1399524, 1399555, 1399735, 1399753, 1400142, 1400353, 1400429, 
        1400565, 1400568, 1400716, 1400719, 1400737, 1400894, 1400942, 1400974, 1401183, 1401208, 1401296, 1401339, 1401402, 1402455, 1402732, 1403100, 
        1403114, 1403131, 1403142, 1403229, 1403270, 1403327, 1403368, 1403690, 1404696, 1404701, 1404703, 1405234, 1405268, 1405304, 1408140, 1408268, 
        1417879];
        

        $output = new ConsoleOutput();
        $progress = new ProgressBar($output, count($saleIds));
        $progress->start();

        foreach($saleIds as $saleId){
            $requestExist = GatewayPostback::where('sale_id',$saleId)->where('data','like','%PAYMENT_CHARGEBACK_REQUESTED%')->exists();
            if ($requestExist) {
                continue;
            }
            Log::info('criando postback para sale id '.$saleId);
            $this->createPostback($saleId,'CHARGEBACK_REQUESTED');
            $progress->advance();
        }

        $progress->finish();
    }

    public function createPostback($saleId,$status)
    {
        $saleGatewayRequest = SaleGatewayRequest::where('sale_id',$saleId)->orderBy('id','desc')->first();
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
