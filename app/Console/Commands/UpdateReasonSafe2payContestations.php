<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
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
                                      
                    $sale = DB::table('sales')->select('id','status','gateway_transaction_id')->where('gateway_transaction_id',$row->IdTransaction)->first();
                    if(!empty($sale))
                    {
                        $saleContestation = SaleContestation::where('sale_id',$sale->id)->first();
                        if(!empty($saleContestation) && empty($saleContestation->reason)){
                            $saleContestation->update([
                                'request_date'=>$row->DisputeDueDate,
                                'reason'=>$row->ReasonMessage,
                                'nsu'=>$row->IdTransaction,
                                'data'=>json_encode($row)
                            ]);
                            $this->comment('Atualizando sale contestation '.$saleContestation->id);
                        }                       
                    }
                }
                $this->line($itens.'/'.$total);
            }
            
        } while ($itens < $total);
    }
}
