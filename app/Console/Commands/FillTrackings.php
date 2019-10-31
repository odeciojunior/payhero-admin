<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FillTrackings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:fillTrackings';

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
     * @return mixed
     */
    public function handle()
    {
        $this->line(date('Y-m-d H:i:s') . ' Executando...');

        try{
            DB::beginTransaction();

            DB::statement('insert into trackings (product_plan_sale_id, sale_id, product_id, amount, delivery_id, tracking_code, tracking_status_enum, created_at, updated_at)
                                  select pps.id, pps.sale_id, pps.product_id, ((select amount from products_plans where plan_id = pps.plan_id and product_id = pps.product_id) * ps.amount )as amount, s.delivery_id, pps.tracking_code, if(pps.tracking_status_enum is null,0,pps.tracking_status_enum) as tracking_status_enum, now() as created_at, now() as updated_at
                                  from products_plans_sales pps
                                  join sales s on s.id = pps.sale_id
                                  join plans_sales ps on ps.sale_id = pps.sale_id and ps.plan_id = pps.plan_id
                                  where pps.tracking_code is not null');

            DB::commit();
            $this->line(date('Y-m-d H:i:s') . ' Funcionou paizao!');
        }catch (Exception $e){
            DB::rollBack();
            $this->line(date('Y-m-d H:i:s') . ' Error: ' . $e->getMessage());
        }
    }
}
