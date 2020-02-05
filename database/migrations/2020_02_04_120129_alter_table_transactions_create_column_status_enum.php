<?php

use Illuminate\Support\Facades\Schema;
use Modules\Core\Entities\Transaction;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableTransactionsCreateColumnStatusEnum extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::table('transactions', function (Blueprint $table) {
        //     $table->integer('status_enum')->default(0)->after('status');
        // });

        // $transactionModel = new Transaction();

        // foreach (Transaction::cursor() as $transaction) {

        //     if($transaction->status == 'waiting_payment' || $transaction->status == 'in_process'){
        //         $transaction->update([
        //             'status' => 'pending'
        //         ]);
        //     }

        //     $transaction->update([
        //         'status_enum' => $transactionModel->present()->getStatusEnum($transaction->status)
        //     ]);
        // }

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('status_enum');
        });
    }
}
