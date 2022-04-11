<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Modules\Core\Entities\Sale;
use Modules\Core\Events\BilletExpiredEvent;
use Modules\Core\Entities\SaleLog;
use Modules\Core\Entities\Transaction;

class GenericCommand extends Command
{
    protected $signature = 'generic';

    protected $description = 'Command description';

    public function handle()
    {

        try {
            $boletos = Sale::with(['customer'])->where(
                [
                    ['payment_method', Sale::BOLETO_PAYMENT],
                    ['status', Sale::STATUS_IN_PROCESS],
                    ['owner_id', 6320]
                ]
            );


            foreach ($boletos->cursor() as $boleto) {


                $boleto->update(
                    [
                        'status' => Sale::STATUS_CANCELED,
                        'gateway_status' => 'canceled',
                    ]
                );

                SaleLog::create(
                    [
                        'status' => 'canceled',
                        'status_enum' => Sale::STATUS_CANCELED,
                        'sale_id' => $boleto->id,
                    ]
                );

                foreach ($boleto->transactions as $transaction) {
                    $transaction->update(
                        [
                            'status' => 'canceled',
                            'status_enum' => Transaction::STATUS_CANCELED,
                        ]
                    );
                }

                if (!$boleto->api_flag) {
                    event(new BilletExpiredEvent($boleto));
                }
            }

        } catch (Exception $e) {
            report($e);
        }

    }
}
