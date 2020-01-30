<?php

use Modules\Core\Entities\Withdrawal;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateTaxColumnOnWithdrawalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        foreach(Withdrawal::where('value', '<', 50000)->get() as $withdrawal){
            $withdrawal->update([
                'tax' => 1000
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        foreach(Withdrawal::where('value', '<', 50000)->get() as $withdrawal){
            $withdrawal->update([
                'tax' => 0
            ]);
        }
    }
}
