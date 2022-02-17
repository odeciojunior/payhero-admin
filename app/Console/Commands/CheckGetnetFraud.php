<?php

namespace App\Console\Commands;

use Exception;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\GetnetChargeback;
use Modules\Core\Services\GetnetBackOfficeService;

class CheckGetnetFraud extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:name';

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
        Log::debug('command . ' . __CLASS__ . ' . iniciando em ' . date("d-m-Y H:i:s"));

        try {

            /*
        subseller_id
        700324205
        700324204
        700283867
        700327872

        zgPAW8j3-1103085-1
        */
            $queryParameters = [
                'seller_id'=>'9631d48b-8ec0-40fe-92f2-0352f32a0051',
                'marketplace_transaction_id'=>14961140
                //'liquidation_date_init'=>'2021-07-02 00:00:00',
                //'liquidation_date_end'=>'2021-07-03 23:59:59'
            ];

            $getnetBackOfficeService = new GetnetBackOfficeService();

            $url = 'v1/mgm/statement?' . http_build_query($queryParameters);
            $response = json_decode($getnetBackOfficeService->sendCurl($url, 'GET', null, null, false));

            foreach($response->list_transactions as $transaction){
                $this->comment($transaction->summary->order_id);

                if(strlen($transaction->summary->order_id) > 20){

                    Log::info([
                                   'gateway_order_id'=>$transaction->summary->order_id,
                                   'marketplace_transaction_id'=>$transaction->summary->marketplace_transaction_id,
                                   'transaction_date'=>$transaction->summary->transaction_date,
                                   'item_id'=>$transaction->details['0']->transaction_id??'',
                                   'subseller_id'=>$transaction->details['0']->subseller_id??'',
                                   'reason_message'=>$transaction->summary->reason_message??''
                               ]);
                }
            }

        } catch (Exception $e) {
            report($e);
        }

        Log::debug('command . ' . __CLASS__ . ' . finalizando em ' . date("d-m-Y H:i:s"));

    }
    public function getchargeback()
    {
        $getnetBackOfficeService = new GetnetBackOfficeService();
        $latest_chargeback = GetnetChargeback::orderByDesc('id')->first();
        $start_day = Carbon::parse($latest_chargeback->created_at);

        //filtros que retorna tudo
        $startDateFilter = $start_day->subDays(5)->format('Y-m-d') . ' 00:00:00';
        $endDateFilter = $start_day->addDays(20)->format('Y-m-d') . ' 23:59:59';

        $filters = [
            'schedule_date_init' => $startDateFilter,
            'schedule_date_end' => $endDateFilter,
        ];

        //pega todos os statements da getnet
        $statements = json_decode($getnetBackOfficeService->getStatementWithoutSaveRequest($filters));

        if (isset($statements->chargeback)) {
            $queryParameters = [
                'seller_id'=>'9631d48b-8ec0-40fe-92f2-0352f32a0051',
                'marketplace_transaction_id'=>''
            ];

            foreach ($statements->chargeback as $chargeback) {
                $explode1 = explode("|", $chargeback->adjustment_reason);
                $explode2 = explode(" - ", $explode1[2]);
                $gateway_order_id = $explode2[0];
                if(strlen($gateway_order_id) > 20){
                    $this->line($gateway_order_id);
                    $queryParameters['marketplace_transaction_id'] = $chargeback->marketplace_transaction_id;
                    $url = 'v1/mgm/statement?' . http_build_query($queryParameters);
                    $response = json_decode($getnetBackOfficeService->sendCurl($url, 'GET', null, null, false));

                    $details = [];
                    if(!empty($response->list_transactions)){
                        foreach($response->list_transactions as $item){
                            $details = [
                                'item_id'=>$item->details['0']->item_id??'',
                                'subseller_id'=>$item->details['0']->subseller_id??'',
                                'reason_message'=>$item->summary->reason_message??''
                            ];
                        }
                    }

                    Log::info([
                        'gateway_order_id'=>$gateway_order_id,
                        'marketplace_transaction_id'=>$chargeback->marketplace_transaction_id,
                        'transaction_date'=>$chargeback->transaction_date,
                        'details'=>$details
                    ]);
                }
            }
        }
    }
}
