<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\Sale;
use Modules\Getnet\Models\StatementTransaction;

class GetnetFixTransactionOrderIdCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'getnet:fix-transaction-order-id';

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

        $start = now();
        $this->comment(now()->format('H:i:s'));
        $this->comment('............');

        $needUpdate = [];

        $sales = StatementTransaction::select(Db::raw("
            statement_transactions.statement_sale_id, statement_transactions.hash_id, statement_transactions.order_id,
            statement_transactions.transaction_type,
            CASE statement_transactions.transaction_type
            WHEN 1 THEN 'Crédito à vista'
            WHEN 2 THEN 'Crédito Parcelado Lojista'
            WHEN 3 THEN 'Crédito Parcelamento Administradora'
            WHEN 4 THEN 'Débito'
            WHEN 5 THEN 'Cancelamento'
            WHEN 6 THEN 'Chargeback'
            WHEN 7 THEN 'Boleto'
            END AS transaction_type_presenter,
            statement_transactions.transaction_status_code,

            CASE statement_transactions.transaction_status_code
            WHEN 0  THEN 'Aprovado'
            WHEN 70 THEN 'Aguardando'
            WHEN 77 THEN 'Pendente'
            WHEN 78 THEN 'Pendente Pagamento'
            WHEN 83 THEN 'Timeout'
            WHEN 86 THEN 'Desfeita'
            WHEN 90 THEN 'Inexistente'
            WHEN 91 THEN 'Negado - Administradora'
            WHEN 92 THEN 'Estornada'
            WHEN 93 THEN 'Repetida'
            WHEN 94 THEN 'Estornada Conciliacao'
            WHEN 98 THEN 'Cancelada - Sem Confirmacao'
            WHEN 99 THEN 'Negado - MGM'
            END AS transaction_status_code_presenter,

            statement_transactions.transaction_sign, statement_transactions.reason_message, statement_transactions.payment_id
        "))
            ->orderBy('statement_sale_id');

        $limit = $sales->count() / 20;
        $count = 0;
        $percentage = 0;
        $this->comment('......' . $sales->count() . '......');

        foreach ($sales->cursor() as $sale) {

            $count++;

            if ($count >= $limit) {

                $count = 0;
                $percentage += 5;

                $this->info(' - ' . $percentage . '%');
            }

            $saleId = $sale->statement_sale_id;
            $hashId = $sale->hash_id;
            $orderId = $sale->order_id;

            $saleCloudFox = Sale::find($saleId);

            if ($saleCloudFox->gateway_order_id != $orderId) {

                $this->comment(' - ' . $saleId . ' = ' . $saleCloudFox->gateway_order_id . ' >> ' . $orderId);

                $needUpdate[] = [
                    'id' => $saleId,
                    'gateway_order_id_wrong' => $saleCloudFox->gateway_order_id,
                    'gateway_order_id_fix_to' => $orderId,
                ];
            }
        }

        $this->comment('............');
        $this->comment('$needUpdate = ' . count($needUpdate));
        $this->comment(now()->format('H:i:s'));
        $this->comment('Tempo em minutos: ' . now()->diffInMinutes($start));
        $this->comment('............');

        return 0;
    }
}
