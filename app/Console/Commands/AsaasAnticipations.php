<?php

namespace App\Console\Commands;

use Exception;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Modules\Core\Entities\Gateway;
use Modules\Core\Entities\Transaction;
use Modules\Core\Services\Gateways\AsaasService;

class AsaasAnticipations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'anticipations:asaas';

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
     * @return void
     */
    public function handle()
    {
        try {

            $day = date('D');
            $week = array(
                'Sun' => 'Domingo',
                'Mon' => 'Segunda-Feira',
                'Tue' => 'Terca-Feira',
                'Wed' => 'Quarta-Feira',
                'Thu' => 'Quinta-Feira',
                'Fri' => 'Sexta-Feira',
                'Sat' => 'Sábado'
            );

            $service = new AsaasService();

            $dayAfter = Carbon::now()->addDay();

            $transactions = Transaction::with('sale')
                ->whereHas('sale', function ($query)  {
                    $query->whereNull('anticipation_status');
                })
                ->where('gateway_id', Gateway::ASAAS_PRODUCTION_ID)
                ->where('status_enum', 2);

            if ($week["$day"] = 'Sexta-Feira'){
                $transactions->whereBetween('release_date', [$dayAfter->format("Y-m-d"), $dayAfter->addDays(2)->format("Y-m-d")]);
            } else{
                $transactions->where('release_date',  $dayAfter->format("Y-m-d"));
            }

            $transactions->get();


                foreach ($transactions->cursor() as $transaction) {
                    $sale = $transaction->sale;
                    $response = $service->makeAnticipationSale($sale);

                    if (isset($response->status)) {
                        $sale->update(['anticipation_status', $response->status]);
                    }

                    dd($response);
                }
        } catch (Exception $e) {
            report($e);
        }

//        $sales = Sale::where([
//            'status' => Sale::STATUS_APPROVED,
//            'gateway_id' => Gateway::ASAAS_PRODUCTION_ID,
//            'anticipation_status' => null,
//        ])
//            ->where('created_at', '>', '2021-10-19 00:00:00')
//            ->get();
//
//        foreach ($sales as $sale) {
//           $response = $service->makeAnticipationSale($sale);
//
//            if (isset($response->status)) {
//                $sale->update(['anticipation_status', $response->status]);
//            }
//        }
    }
}
