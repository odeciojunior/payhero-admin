<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Core\Entities\GatewayFlag;
use Modules\Core\Entities\GatewayFlagTax;

class NewRegisterGatewayFlagsBilletAsaas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $rowFlagProduction = GatewayFlag::create([
            'name'=>'Boleto',
            'slug'=>'boleto',
            'gateway_id'=>8,
            'card_flag_enum'=>'10',
            'active_flag'=>1
        ]);
        GatewayFlagTax::create([            
            'gateway_flag_id'=>$rowFlagProduction->id,
            'installments'=>1,
            'type_enum'=>1,
            'percent'=>1.59,
            'active_flag'=>1            
        ]);

        $rowFlagSandbox= GatewayFlag::create([
            'name'=>'Boleto',
            'slug'=>'boleto',
            'gateway_id'=>20,
            'card_flag_enum'=>'10',
            'active_flag'=>1
        ]);
        GatewayFlagTax::create([            
            'gateway_flag_id'=>$rowFlagSandbox->id,
            'installments'=>1,
            'type_enum'=>1,
            'percent'=>1.59,
            'active_flag'=>1            
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
