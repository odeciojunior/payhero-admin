<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Sale;
use Modules\Core\Services\GetnetBackOfficeService;
use Modules\Transfers\Services\GetNetStatementService;
use Vinkla\Hashids\Facades\Hashids;

class UpdateTransactionsWithGetNet extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update-transactions-with-getnet';

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

        /*
        SELECT companies.id AS company_id, companies.fantasy_name, companies.subseller_getnet_id, companies.get_net_status, user_id, users.name AS user_name, users.email AS user_email, users.get_net_status AS user_get_net_status
        FROM companies
        JOIN users ON users.id = companies.user_id
        WHERE companies.subseller_getnet_id IS NOT NULL
        AND companies.get_net_status IN (1, 4)
        */
        $companies = Company::select(DB::raw('companies.id AS company_id, companies.fantasy_name, companies.subseller_getnet_id, companies.get_net_status, user_id, users.name AS user_name, users.email AS user_email'))
            ->join('users', 'users.id', '=', 'companies.user_id')
            ->whereNotNull('companies.subseller_getnet_id')
            ->whereIn('companies.get_net_status', [1])
            ->where('companies.id', 1521)
            ->orderBy('fantasy_name')
            ->get();

        $this->line('Total de ' . $companies->count() . ' empresas');

        /*
         SELECT * FROM sales
        JOIN transactions on sales.id = transactions.sale_id
        WHERE 1
        #AND transactions.company_id = 2965
        #AND sales.end_date >= '2019-08-09 00:00:00'
        AND gateway_id IN (15)
        AND status_enum IN(1)
        LIMIT 10000;
         * */

        foreach ($companies as $company) {

            $this->line('  - Company ' . $company->fantasy_name . ' #' . $company->company_id);

            $items = Sale::select('sales.id AS sale_id', 'transactions.id AS transaction_id', 'transactions.status', 'transactions.status_enum')
                ->join('transactions', 'sales.id', '=', 'transactions.sale_id')
                ->where('transactions.company_id', $company->company_id)
                ->whereIn('gateway_id', [15])
                ->whereIn('status_enum', [1]) //transfered
                ->get();

            $companyTransactions = [];
            $withoutGatewayResult = [];
            $orderIdsDatabase = [];
            $orderIdsGetNet = [];
            $orderIdsGetNetMissingInDatabase = [];

            $this->line('  - Transactions ' . $items->count() . '');

            foreach ($items as $transaction) {

                try {

                    #hardcode para o relacionamento
                    $transaction->id = $transaction->sale_id;

                    //$gatewayResult = json_decode($transaction->saleGatewayRequests->last()->gateway_result);
                    if ($last = $transaction->saleGatewayRequests->last()) {

                        $lastGatewayResult = json_decode($last->gateway_result);

                        //dd($lastGatewayResult, $transaction->toArray());

                        if (isset($lastGatewayResult->order_id)) {

                            $orderIdsDatabase[] = $lastGatewayResult->order_id;

                            $companyTransactions[$lastGatewayResult->order_id] = [
                                #'order_id' => $lastGatewayResult->order_id,
                                'sale_id' => $transaction->sale_id,
                                'transaction_id' => $transaction->transaction_id,
                                'status' => $transaction->status,
                                'status_enum' => $transaction->status_enum,
                            ];

                        }
                    } else {

                        $withoutGatewayResult[$transaction->sale_id] = [
                            'transaction_id' => $transaction->transaction_id,
                        ];
                    }
                } catch (Exception $exception) {

                    dd($exception);
                }
            }

            $this->line('    - $companyTransactions = ' . count($companyTransactions) . ' | $withoutGatewayResult = ' . count($withoutGatewayResult));
            //print_r($companyTransactions);
            //exit;
            /*print_r($withoutGatewayResult);*/

            if (count($companyTransactions)) {

                request()->request->add(['dateRange' => '2020-07-01 - ' . date('Y-m-d')]);

                $result = (new GetnetBackOfficeService())->getStatement($company->subseller_getnet_id);
                $result = json_decode($result);

                if (isset($result->errors)) {

                    dd($result->errors);
                }

                $transactionsGetNet = (new GetNetStatementService())->performStatement($result);
                $transactionsGetNet = collect($transactionsGetNet);
                //dd($transactionsGetNet);

                foreach ($transactionsGetNet as $transactionGetNet) {

                    $orderIdsGetNet[] = $transactionGetNet->originalOrderId;

                    if (isset($companyTransactions[$transactionGetNet->originalOrderId])) {

                        $statusInDatabase = $companyTransactions[$transactionGetNet->originalOrderId]['status'];

                        if (!empty($transactionGetNet->subSellerRateConfirmDate)) {


                        } else {


                        }
                    } else {

                        $orderIdsGetNetMissingInDatabase[] = [
                            'originalOrderId' => $transactionGetNet->originalOrderId,
                            'orderId' => $transactionGetNet->orderId,
                            'transactionDate' => $transactionGetNet->transactionDate,
                            'installmentDate' => $transactionGetNet->installmentDate,
                            'sale_id' => current(Hashids::connection('sale_id')->decode($transactionGetNet->orderId)),
                        ];
                    }

                    //dd($transactionGetNet);
                }

                dd($orderIdsDatabase, $orderIdsGetNet, $orderIdsGetNetMissingInDatabase);

            }
            print('');
        }

        return 0;
    }
}
