<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Gateway;
use Modules\Core\Entities\GatewayPostback;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\SaleContestation;
use Modules\Core\Entities\SaleGatewayRequest;
use Modules\Core\Entities\SaleLog;
use Modules\Core\Entities\Transaction;
use Modules\Core\Entities\Transfer;
use Modules\Core\Services\Gateways\AsaasService;
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
        $this->verifyAsaasBalance();        
    }

    public function verifyAsaasBalance(){
        $companies =  Company::whereHas('gatewayCompanyCredential',function($qr){
            $qr->where('gateway_id',8)->whereNotNull('gateway_api_key');
        })->get();

        $gatewayService = new AsaasService();
        $dtNow = Date('d/m/Y');
        foreach($companies as $company)
        {
            $this->line('avaliando empresa '.$company->id);
            $gatewayService->setCompany($company);

            $filters = [
                'date_type'=> 'transfer_date',
                'date_range'=> '01/01/2018 - '.$dtNow,
                'reason'=>'',
                'transaction'=>'',
                'type'=>'',
                'value'=>'',
            ];
            $balance = ($gatewayService->getPeriodBalance($filters)??0)*100;

            if($balance <> $company->asaas_balance){
                Log::info(
                    str_pad($company->id,5,' ',STR_PAD_RIGHT).
                    str_pad($company->fantasy_name,80,' ',STR_PAD_RIGHT).
                    str_pad($balance,10,' ',STR_PAD_RIGHT).
                    str_pad($company->asaas_balance,10,' ',STR_PAD_RIGHT).
                    'Divergente'
                );
                $this->error('Divergencia na empresa '.$company->id);
            }
        }



    }

    public function revertSaleChargeback(){
        $sales = Sale::where('gateway_id',8)->where('status',Sale::STATUS_CHARGEBACK)->whereDate('updated_at',now())
        ->doesnthave('contestations')->doesnthave('saleRefundHistory')->get();

        $output = new ConsoleOutput();
        $progress = new ProgressBar($output, count($sales));
        $progress->start();

        foreach($sales as $sale)
        {
            Log::info('======= VENDA '.$sale->id.' ============');
            $refundTransactions = $sale->transactions;
            foreach ($refundTransactions as $refundTransaction) {
                $company = $refundTransaction->company;
                $transfers = Transfer::where('transaction_id',$refundTransaction->id)->whereDate('created_at',now())->get();
                $transfered = false;
                foreach($transfers as $transfer){
                    $devolverSaldo =0;
                    if($transfer->type=='out'){
                        $devolverSaldo = $company->asaas_balance + $transfer->value;
                    }else{
                        $devolverSaldo = $company->asaas_balance - $transfer->value;
                        $transfered = true;
                    }
                    Log::info('Devolvendo '.$transfer->value.' empresa '.$company->id.' - '.$company->fantasy_name);
                    $company->update([
                        'asaas_balance' => $devolverSaldo
                    ]);

                    Log::info('Deletando transfer id '.$transfer->id);
                    $transfer->delete();
                }

                if($transfered && $refundTransaction->type==Transaction::TYPE_PRODUCER){
                    $refundTransaction->status = 'transfered';
                    $refundTransaction->status_enum = Transaction::STATUS_TRANSFERRED;
                    Log::info('alterando status transaction id '.$refundTransaction->id.' para transfered');
                }else{
                    $refundTransaction->status = 'paid';
                    $refundTransaction->status_enum = Transaction::STATUS_PAID;
                    Log::info('alterando status transaction id '.$refundTransaction->id.' para paid');
                }
                $refundTransaction->save();
            }

            $sale->update(
                [
                    'status' => Sale::STATUS_APPROVED,
                    'gateway_status' => 'CONFIRMED',
                ]
            );
            SaleLog::where('sale_id',$sale->id)->whereDate('created_at',now())->orderBy('id','DESC')->first()->delete();

            Log::info('atualizando status venda '.$sale->id.' para approved');
            $progress->advance();
        }

        $progress->finish();
    }

    public function getSalesPreChargeback(){
        $invoiceNumbers= [
            80297363,
            76304547,
            79650783,
            79931354,
            80899036,
            80298989,
            79491765,
            79973765,
            78440274,
            4997741,
            80818811,
            77419199,
            78231891,
            80543085,
            80980853,
            80312641,
            76164696,
            76719392,
            77394909,
            80337331,
            80664179,
            78332161,
            77242187,
            77066520,
            76399878,
            80887485,
            81114007,
            80969476,
            77776087,
            79454739,
            80552655,
            78331442,
            78283252,
            79906786,
            80677834,
            80940497,
            79722516,
            79974279,
            79937150,
            77436200,
            80989780,
            79545255,
            80687598,
            78147686,
            81157560,
            77437289,
            80028032,
            79229260,
            81302734,
            80369827,
            81006230,
            81005389,
            81005375,
            81005319,
            80016539,
            80251295,
            80251162,
            80778596,
            80399178,
            80364263,
            80363523,
            80362684,
            80360116,
            77980026,
            80926627,
            80895049,
            80056692,
            79860589,
            78741419,
            79545117,
            79533828,
            4975567,
            80624609,
            78195240,
            78187018,
            77372203,
            79458258,
            79179231,
            80904370,
            79515248,
            79963036,
            76319591,
            80230023,
            80251342,
            79492719,
            79722457,
            80293288,
            76935918,
            80937461,
            79534482,
            79971889,
            79727079,
            79854835,
            80240793,
            78086172,
            78522196,
        ];
        return $invoiceNumbers;
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

    public function reprocessRefundPostback(){
        $postbacks = GatewayPostback::where('gateway_id',8)->whereHas('sale',function($qr){
            $qr->where('status',Sale::STATUS_APPROVED);
        })
        ->where('data', 'like','%"event": "PAYMENT_REFUNDED"%')->where('processed_flag',1)->get();
        $salesArray = [];
        foreach($postbacks as $postback){
            if(in_array($postback->sale_id,$salesArray)){
                continue;
            }
            $this->line($postback->sale_id);
            $salesArray[$postback->sale_id] = $postback->sale_id;
            $postback->update([
                'processed_flag'=>false
            ]);
        }
        $this->info('total postbacks '.count($salesArray));
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
