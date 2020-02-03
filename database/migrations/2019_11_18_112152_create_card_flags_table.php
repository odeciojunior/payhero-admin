<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCardFlagsTable extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::create('card_flags', function(Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('gateway_id')->nullable();
            $table->string('name');
            $table->string('slug');
            $table->tinyInteger('card_flag_enum');
            $table->boolean('active_flag')->default(1);
            $table->timestamps();
        });
        Schema::table('card_flags', function(Blueprint $table) {
            $table->foreign('gateway_id')->references('id')->on('gateways');
        });
        $sql = "INSERT INTO card_flags (name, slug,card_flag_enum, created_at, updated_at) ";
        $sql .= "VALUES('Card','card',1,CURRENT_DATE, CURRENT_DATE)";
        DB::select($sql);

        $sql = "INSERT INTO card_flags (name,gateway_id, slug,card_flag_enum, created_at, updated_at) ";
        $sql .= "VALUES('Visa',3,'visa',2,CURRENT_DATE, CURRENT_DATE)";
        DB::select($sql);

        $sql = "INSERT INTO card_flags (name,gateway_id, slug,card_flag_enum, created_at, updated_at) ";
        $sql .= "VALUES('Marter Card',3,'mastercard',3,CURRENT_DATE, CURRENT_DATE)";
        DB::select($sql);

        $sql = "INSERT INTO card_flags (name,gateway_id, slug,card_flag_enum, created_at, updated_at) ";
        $sql .= "VALUES('Elo',3,'elo',4,CURRENT_DATE, CURRENT_DATE)";
        DB::select($sql);
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('card_flags');
    }
}
