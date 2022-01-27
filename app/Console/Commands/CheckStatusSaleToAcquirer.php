<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Core\Entities\Gateway;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\Transaction;
use Modules\Core\Services\Asaas\AsaasService;
use Modules\Core\Services\CieloService;
use Modules\Core\Services\Gerencianet\GerencianetService;
use Modules\Core\Services\Getnet\GetnetBackOfficeService;

class CheckStatusSaleToAcquirer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:status-sale-acquirer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = ' CheckStatusSaleToAcquirer';

    public $gateways = [Gateway::GETNET_PRODUCTION_ID];

    /**
     * start getnet 2020-09
     * start asaas 2019-10
     * start gerencianet 2021-02 2021-05
     */
    public $dateStart = '2020-09-01';
    public $dateEnd = '2020-09-30';

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

            $salesQuery = Sale::
            with('transactions')
                ->whereHas('transactions', function ($query) {
                    $query->where('type', Transaction::TYPE_PRODUCER);
                })
                ->where('id', 1472286);

            $total = $salesQuery->whereIn('gateway_id', $this->gateways)->count();
            $bar = $this->output->createProgressBar($total);
            $bar->start();


            foreach ($this->gateways as $gateway) {

                $sales = $salesQuery->where('gateway_id',$gateway);

//                $sales = Sale::with('transactions')
//                    ->whereHas('transactions', function ($query) {
//                        $query->where('type', Transaction::TYPE_INVITATION);
//                        $query->where('gateway_id', Gateway::GETNET_PRODUCTION_ID);
//                        $query->whereIn('status_enum', [Transaction::STATUS_PAID, Transaction::STATUS_TRANSFERRED]);
//                        $query->whereDate('created_at', '>=', '2022-01-01');
//                        $query->whereDate('created_at', '<=', '2022-01-31');
//                    });

                switch ($gateway) {
                    case Gateway::GETNET_PRODUCTION_ID:
                        $this->checkSaleGetnet($bar, $sales);
                        break;
                }
            }
            $bar->finish();
            dd('checkSaleGetnet');
            return 0;
        } catch (Exception $e) {
            dd($e);
        }
    }

    public function checkSaleGetnet($bar, $sales) {
//        $total = $sales->count();
//        $bar = $this->output->createProgressBar($total);
//        $bar->start();
        foreach ($sales->cursor() as $sale) {
            $bar->advance();
        }

        //dd($sales->count());
        //dd('checkSaleGetnet');

        //$bar->finish();
        return null;
    }
}
