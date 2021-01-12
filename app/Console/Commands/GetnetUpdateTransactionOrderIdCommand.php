<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Core\Entities\Sale;

class GetnetUpdateTransactionOrderIdCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'getnet:update-transaction-order-id';

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

        $sales = Sale::select('sales.id', 'sale_gateway_requests.gateway_result', 'sale_gateway_requests.send_data')
            ->where('sale_gateway_requests.gateway_id', 15)
            ->whereNull('sales.gateway_order_id')
            ->join('sale_gateway_requests', 'sale_gateway_requests.sale_id', '=', 'sales.id')
            //->where('sales.id', 832300)
            //->take(20)
            //->offset(10)
            ->get();

        $success = 0;
        $fail = 0;
        $empty = 0;
        $secondWay = 0;

        $limit = $sales->count() / 50;
        $count = 0;
        $percentage = 0;
        $this->comment('......' . $sales->count() . '......');

        foreach ($sales as $sale) {

            $count++;

            if ($count >= $limit) {

                $count = 0;
                $percentage += 2;

                $this->info(' - ' . $percentage . '%');
            }

            $sale_id = $sale->id;

            $gateway_result = json_decode($sale->gateway_result);

            if (isset($gateway_result->order_id)) {

                if (empty($gateway_result->order_id)) {
                    $empty++;
                } else {

                    $success++;
                    $order_id = $gateway_result->order_id;
                    $sale->gateway_order_id = $order_id;
                    $sale->save();
                }
            } else {

                $send_data = json_decode($sale->send_data);
                if (isset($send_data->order) && isset($send_data->order->order_id)) {
        
                    $secondWay++;
                    $order_id = $send_data->order->order_id;
                    $sale->gateway_order_id = $order_id;
                    $sale->save();

                    $this->info('    Atualizando ' . $sale_id . ' para: ' . $order_id);
                } else {
                    $fail++;
                    //$this->info('Nada para ' . $sale_id);
                }

            }
            //print_r($sale->toArray());
        }

        $this->info(' - - - - - - - - - - - - - - - -');
        $this->info('$success ' . $success);
        $this->info('$secondWay ' . $secondWay);
        $this->info('$fail ' . $fail);
        $this->info('$empty ' . $empty);
        return 0;
    }
}
