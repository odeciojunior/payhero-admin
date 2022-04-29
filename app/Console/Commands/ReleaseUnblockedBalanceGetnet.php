<?php

namespace App\Console\Commands;

use Exception;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Modules\Core\Entities\BlockReasonSale;
use Modules\Core\Entities\Gateway;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\SaleContestation;

class ReleaseUnblockedBalanceGetnet extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'getnet:release-unblocked-balance';

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

        try {

            $dataSixMonthAgo = Carbon::create(date('Y-m-d'))->subMonths(6)->format('Y-m-d');

            $contestations = SaleContestation::where('gateway_id',Gateway::GETNET_PRODUCTION_ID)->where('is_contested',true)
                ->whereHas('sale',function($qr){
                    $qr->where('status',Sale::STATUS_APPROVED);
                })
                ->where('status',SaleContestation::STATUS_IN_PROGRESS)->whereDate('created_at','<=',$dataSixMonthAgo)->get();

            foreach($contestations as $contestation){

                $blockSales = BlockReasonSale::where('status',BlockReasonSale::STATUS_BLOCKED)->where('sale_id',$contestation->sale_id)->first();
                if(!empty($blockSales)){
                    $blockSales->update([
                        'status' => BlockReasonSale::STATUS_UNLOCKED
                    ]);
                }

                $contestation->update([
                    'status'=>SaleContestation::STATUS_WIN
                ]);
            }

        } catch (Exception $e) {
            report($e);
        }

    }
}
