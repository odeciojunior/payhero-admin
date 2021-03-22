<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Core\Entities\Sale;

class GenericCommand extends Command
{
    protected $signature = 'generic';

    protected $description = 'Command description';

    public function handle()
    {
        try {
            /** \Vinkla\Hashids\Facades\Hashids */
            $hash = hashids()->encode(1);
            // result: v2RmA83EbZPVpYB
            $number = current(hashids()->decode('v2RmA83EbZPVpYB'));
            // result: 1

            /** \Vinkla\Hashids\Facades\Hashids with options */
            $hash = hashids()->connection('sale_id')->encode(1);
            // result: A83EbZPV
            $number = current(hashids()->connection('sale_id')->decode('A83EbZPV'));
            // result: 1

            /** \Vinkla\Hashids\Facades\Hashids direct encode & decode */
            $hash = hashids_encode(2);
            // result: n12wq7GrVGBANP4
            $number = hashids_decode('n12wq7GrVGBANP4');
            // result: 1

            /** \Vinkla\Hashids\Facades\Hashids direct encode & decode with connection */
            $hash = hashids_encode(2, 'sale_id');
            // result: q7GrVGBA
            $number = hashids_decode('q7GrVGBA', 'sale_id');
            // result: 1

            /** \Modules\Core\Services\FoxUtils */
            $production = foxutils()->isProduction();
            //result: false
            $number = foxutils()->onlyNumbers('2,436.24');
            //result: 243624


            /** Debug a Query Builder */
            $query = Sale::whereBetween('start_date', ['2021-02-17 00:00:00', '2021-03-19 23:59:59'])
                            ->where('status', Sale::STATUS_APPROVED);
            $sql = builder2sql($query);
            //result: select * from `sales` where `start_date` between '2021-02-17 00:00:00' and '2021-03-19 23:59:59'
            // and `status` = 1 and `sales`.`deleted_at` is null

        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }
}
