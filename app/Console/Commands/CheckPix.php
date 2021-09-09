<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Modules\Core\Entities\Gateway;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\SaleLog;
use Modules\Core\Entities\Transaction;
use Modules\Core\Entities\WooCommerceIntegration;
use Modules\Core\Events\PixExpiredEvent;
use Modules\Core\Services\CheckoutService;
use Modules\Core\Services\FoxUtils;
use Modules\Core\Services\ShopifyService;
use Modules\Core\Services\WooCommerceService;
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
            )->get();

            foreach ($sales as $sale) {

                //consultar na Gerencianet para ver se não foi pago
                $data = [
                    'sale_id' => Hashids::encode($sale->id)
                ];

                $responseCheckout = (new CheckoutService())->checkPaymentPix($data);

                if ($responseCheckout->status == 'success' and $responseCheckout->payment == true) {
                    report(new Exception('Command para checar as venda paga na Gerencianet e com problema no pagamento. $sale->id = ' . $sale->id . ' $gatewayTransactionId = ' . $sale->gateway_transaction_id));
                }
            }
        } catch (Exception $e) {
            report($e);
        }
    }
}
