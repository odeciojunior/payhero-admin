<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGatewayFlagTaxesTable extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::create('gateway_flag_taxes', function(Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('card_flag_id');
            $table->integer('installments');
            $table->tinyInteger('type_enum')->comment('1 - credit  2 - debit');
            $table->decimal('percent', 8, 2);
            $table->boolean('active_flag')->default(1);
            $table->timestamps();
        });
        Schema::table('gateway_flag_taxes', function(Blueprint $table) {
            $table->foreign('card_flag_id')->references('id')->on('card_flags');
        });
        //Zoop - Card(Padrão)
        $sql = "INSERT INTO gateway_flag_taxes (card_flag_id,installments,percent,type_enum,created_at, updated_at) ";
        $sql .= "VALUES(1,1,3.35,1,CURRENT_DATE, CURRENT_DATE),
                        (1,2,3.87,1,CURRENT_DATE, CURRENT_DATE),
                        (1,3,3.87,1,CURRENT_DATE, CURRENT_DATE),
                        (1,4,3.87,1,CURRENT_DATE, CURRENT_DATE),
                        (1,5,3.87,1,CURRENT_DATE, CURRENT_DATE),
                        (1,6,3.87,1,CURRENT_DATE, CURRENT_DATE),
                        (1,7,4.47,1,CURRENT_DATE, CURRENT_DATE),
                        (1,8,4.47,1,CURRENT_DATE, CURRENT_DATE),
                        (1,9,4.47,1,CURRENT_DATE, CURRENT_DATE),
                        (1,10,4.47,1,CURRENT_DATE, CURRENT_DATE),
                        (1,11,4.47,1,CURRENT_DATE, CURRENT_DATE),
                        (1,12,4.47,1,CURRENT_DATE, CURRENT_DATE),
                        (1,1,2.85,2,CURRENT_DATE, CURRENT_DATE)";
        DB::select($sql);
        //Zoop - Master Card
        $sql = "INSERT INTO gateway_flag_taxes (card_flag_id,installments,percent,type_enum,created_at, updated_at) ";
        $sql .= "VALUES(2,1,2.10,1,CURRENT_DATE, CURRENT_DATE),
                        (2,2,2.43,1,CURRENT_DATE, CURRENT_DATE),
                        (2,3,2.43,1,CURRENT_DATE, CURRENT_DATE),
                        (2,4,2.43,1,CURRENT_DATE, CURRENT_DATE),
                        (2,5,2.43,1,CURRENT_DATE, CURRENT_DATE),
                        (2,6,2.43,1,CURRENT_DATE, CURRENT_DATE),
                        (2,7,2.69,1,CURRENT_DATE, CURRENT_DATE),
                        (2,8,2.69,1,CURRENT_DATE, CURRENT_DATE),
                        (2,9,2.69,1,CURRENT_DATE, CURRENT_DATE),
                        (2,10,2.69,1,CURRENT_DATE, CURRENT_DATE),
                        (2,11,2.69,1,CURRENT_DATE, CURRENT_DATE),
                        (2,12,2.69,1,CURRENT_DATE, CURRENT_DATE),
                        (2,1,1.60,2,CURRENT_DATE, CURRENT_DATE)";
        DB::select($sql);

        //Zoop - Visa
        $sql = "INSERT INTO gateway_flag_taxes (card_flag_id,installments,percent,type_enum,created_at, updated_at) ";
        $sql .= "VALUES(3,1,2.10,1,CURRENT_DATE, CURRENT_DATE),
                        (3,2,2.43,1,CURRENT_DATE, CURRENT_DATE),
                        (3,3,2.43,1,CURRENT_DATE, CURRENT_DATE),
                        (3,4,2.43,1,CURRENT_DATE, CURRENT_DATE),
                        (3,5,2.43,1,CURRENT_DATE, CURRENT_DATE),
                        (3,6,2.43,1,CURRENT_DATE, CURRENT_DATE),
                        (3,7,2.69,1,CURRENT_DATE, CURRENT_DATE),
                        (3,8,2.69,1,CURRENT_DATE, CURRENT_DATE),
                        (3,9,2.69,1,CURRENT_DATE, CURRENT_DATE),
                        (3,10,2.69,1,CURRENT_DATE, CURRENT_DATE),
                        (3,11,2.69,1,CURRENT_DATE, CURRENT_DATE),
                        (3,12,2.69,1,CURRENT_DATE, CURRENT_DATE),
                        (3,1,1.60,2,CURRENT_DATE, CURRENT_DATE)";
        DB::select($sql);
        //Zoop - Elo
        $sql = "INSERT INTO gateway_flag_taxes (card_flag_id,installments,percent,type_enum,created_at, updated_at) ";
        $sql .= "VALUES(4,1,3.35,1,CURRENT_DATE, CURRENT_DATE),
                        (4,2,3.87,1,CURRENT_DATE, CURRENT_DATE),
                        (4,3,3.87,1,CURRENT_DATE, CURRENT_DATE),
                        (4,4,3.87,1,CURRENT_DATE, CURRENT_DATE),
                        (4,5,3.87,1,CURRENT_DATE, CURRENT_DATE),
                        (4,6,3.87,1,CURRENT_DATE, CURRENT_DATE),
                        (4,7,4.47,1,CURRENT_DATE, CURRENT_DATE),
                        (4,8,4.47,1,CURRENT_DATE, CURRENT_DATE),
                        (4,9,4.47,1,CURRENT_DATE, CURRENT_DATE),
                        (4,10,4.47,1,CURRENT_DATE, CURRENT_DATE),
                        (4,11,4.47,1,CURRENT_DATE, CURRENT_DATE),
                        (4,12,4.47,1,CURRENT_DATE, CURRENT_DATE),
                        (4,1,2.85,2,CURRENT_DATE, CURRENT_DATE)";
        DB::select($sql);
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('gateway_flag_taxes');
    }
}
