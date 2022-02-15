<?php

namespace Modules\Core\Services;

use Exception;
use Carbon\Carbon;
use Modules\Core\Entities\Gateway;
use Modules\Core\Entities\PixCharge;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\SaleLog;
use Modules\Core\Entities\Transaction;
use Modules\Core\Events\PixExpiredEvent;
use Vinkla\Hashids\Facades\Hashids;

/**
 * Class PixService
 * @package Modules\Core\Services
 */
class PixService
{

    /**
     *  Pix Pending
     * @return void
     */
    public function changePixToCanceled()
    {
        try {

            $sales = Sale::where(
                [
                    ['payment_method', '=', Sale::PIX_PAYMENT],
                    ['status', '=', Sale::STATUS_PENDING],
                    ['gateway_id','=',Gateway::GERENCIANET_PRODUCTION_ID]
                ]
            )
            ->whereHas(
                'pixCharges',
                function ($querySale) {
                    $querySale->where('status', 'ATIVA');
                    $querySale->where( 'created_at', '<=', Carbon::now()->subHour()->toDateTimeString());
                }
            );

            foreach ($sales->cursor() as $sale) {

                //consultar na Gerencianet para ver se não foi pago
                $data = [
                    'sale_id' => Hashids::encode($sale->id)
                ];

                $responseCheckout = (new CheckoutService())->checkPaymentPix($data);

                if ($responseCheckout->status == 'success' and $responseCheckout->payment == true)
                {
                    foreach($responseCheckout->response->pix as $row){
                        $pixCharge = PixCharge::where('sale_id',$sale->id)->where('txid',$row->txid)->first();
                        if(!empty($row->endToEndId) && !empty($pixCharge)){                            
                            report(new Exception('Venda paga na Gerencianet e com problema no pagamento. $sale->id = ' . $sale->id . ' $gatewayTransactionId = ' . $sale->gateway_transaction_id));
                            continue 2;       
                        }
                    }
                }

                $sale->update(['status' => Sale::STATUS_CANCELED]);

                foreach ($sale->transactions as $transaction) {
                    $transaction->update(
                        [
                            'status' => 'canceled',
                            'status_enum' => Transaction::STATUS_CANCELED,
                        ]
                    );
                }

                SaleLog::create(
                    [
                        'status' => 'canceled',
                        'status_enum' => 5,
                        'sale_id' => $sale->id,
                    ]
                );

                $pix = $sale->pixCharges->where('status', 'ATIVA')->first();

                if (!FoxUtils::isEmpty($pix)) {
                    //Atualizar o e2id
                    $pix->update(['status' => 'EXPIRED']);
                }

                event(new PixExpiredEvent($sale));

            }
        } catch (Exception $e) {
            report($e);
        }
    }
}

