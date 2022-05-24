<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\Gateway;
use Modules\Core\Entities\SaleContestation;
use Modules\Core\Services\Gateways\Safe2payGateway;

class UpdateReasonSafe2payContestations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'safe2pay:update-reason-sale-contestations';

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
        $safe = new Safe2payGateway();
        
        $pageNumber = 1;
        $limit = 100;
        $total = 0;
        $itens = 0;

        $saleContestations = DB::table('sale_contestations as c')->select('c.id','s.gateway_transaction_id')
        ->join('sales as s','s.id','=','c.sale_id')
        ->where('c.gateway_id',Gateway::SAFE2PAY_PRODUCTION_ID)->whereNull('c.reason')->get();

        if(count($saleContestations) == 0){
            exit;
        }
       
        do {
            $response = $safe->listChargebacks([
                'PageNumber'=>$pageNumber,
                'RowsPerPage'=>$limit,
            ]);
            
            $total = 0;
            if(!empty($response->ResponseDetail)){
                $total = $response->ResponseDetail->TotalItems;
                $pageNumber++;

                foreach($response->ResponseDetail->Objects as $row){
                    
                    $this->line($row->IdTransaction);
                    $itens++;

                    foreach($saleContestations as $key=>$contestation){
                        if($contestation->gateway_transaction_id == $row->IdTransaction)
                        {
                            $saleContestation = SaleContestation::find($contestation->id);                            
                            $saleContestation->update([
                                'request_date'=>$row->ChargebackDate,
                                'reason'=>$row->ReasonMessage,
                                'nsu'=>$row->IdTransaction,
                                'gateway_case_number'=>$row->CaseNumber,
                                'data'=>json_encode($row),
                                'expiration_date'=>$row->DisputeDueDate, //Data final para defesa da contestação.
                            ]);
                            $this->comment('Atualizando sale contestation '.$contestation->id);
                            
                            unset($saleContestations[$key]);
                        }
                    }    
                    
                    if(count($saleContestations) == 0){
                        exit;
                    }
                }

                $this->line($itens.'/'.$total);
            }
            
        } while ($itens < $total);
    }
}
