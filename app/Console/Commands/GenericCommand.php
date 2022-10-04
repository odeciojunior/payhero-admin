<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\Gateway;
use Modules\Core\Entities\GatewayFlagTax;
use Modules\Core\Services\FoxUtils;

class GenericCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "generic {name?}";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Command description";

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

            for ($i = 1; $i <= 12; $i++) {
                // $installmentTax =2.25;
                // if($i > 6){
                //     $installmentTax =3.16;
                // }elseif($i>1){
                //     $installmentTax =2.73;
                // }

                $installmentTax = ($i * 2.05);
                $this->line($i.'=>'.$installmentTax);
            }
    }

    public function updateTaxes(){
        $gatewayFlags = DB::table("gateway_flags")->whereIn('gateway_id',[Gateway::IUGU_PRODUCTION_ID,Gateway::IUGU_SANDBOX_ID])->get();

        foreach ($gatewayFlags as $flag)
        {
            for ($i = 1; $i <= 12; $i++) {
                $installmentTax =2.25;
                if($i > 6){
                    $installmentTax =3.16;
                }elseif($i>1){
                    $installmentTax =2.73;
                }

                $installmentTax+= $i==1? ($i * 2.05) : ($i * 2.05)/2;
                $this->line($i.'=>'.$installmentTax);

                $taxFlag = GatewayFlagTax::where('gateway_flag_id',$flag->id)->where('installments',$i)->where('type_enum',1)->first();
                if(!empty($taxFlag)){
                    $taxFlag->update([
                        "percent" => round($installmentTax,2)
                    ]);
                }
            }
        }
    }
}
