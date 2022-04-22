<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableWithdrawalAddPixColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('withdrawals',function(Blueprint $table){
            $table->string('transfer_type',15)->nullable()->default(null)->after('currency');
            $table->string('type_key_pix',15)->nullable()->default(null)->after('transfer_type');
            $table->string('key_pix',50)->nullable()->default(null)->after('type_key_pix');
        });
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
