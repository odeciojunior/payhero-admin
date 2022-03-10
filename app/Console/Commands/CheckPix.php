<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Exception;
use Modules\Core\Entities\Gateway;
use Modules\Core\Entities\Sale;
use Modules\Core\Services\CheckoutService;
use Vinkla\Hashids\Facades\Hashids;

class CheckPix extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:pix';

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

            $sales = Sale::where(
                    [
                        ['payment_method', '=', Sale::PIX_PAYMENT],
                        ['status', '=', Sale::STATUS_CANCELED]
                    ]
                )->where('gateway_id',Gateway::GERENCIANET_PRODUCTION_ID)
                ->get();

            $total = count($sales);
            $bar = $this->output->createProgressBar($total);
            $bar->start();

            foreach ($sales as $sale) {

                //consultar na Gerencianet para ver se não foi pago
                $data = [
                    'sale_id' => Hashids::encode($sale->id)
                ];

                $responseCheckout = (new CheckoutService())->checkPaymentPix($data);

                if ($responseCheckout->status == 'success' and $responseCheckout->payment == true) {

                    $saleModel = Sale::where(
                        [
                            ['payment_method', '=', Sale::PIX_PAYMENT],
                            ['customer_id', $sale->customer->id]
                        ]
                    )
                    ->whereIn("status", [Sale::STATUS_APPROVED, Sale::STATUS_REFUNDED])
                    ->whereDate('start_date', Carbon::parse($sale->start_date)->format("Y-m-d"))->first();


                    if(empty($saleModel)) {
                        report(new Exception('Local command para checar as venda paga na Gerencianet e com problema no pagamento. $sale->id = ' . $sale->id . ' $gatewayTransactionId = ' . $sale->gateway_transaction_id));
                    }

                }
                $bar->advance();
            }
            $bar->finish();

        } catch (Exception $e) {
            report($e);
        }

    }
}
