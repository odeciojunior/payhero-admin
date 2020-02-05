<?php

use Modules\Core\Entities\Checkout;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableCheckoutsCreateColumnStatusEnum extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('checkouts', function (Blueprint $table) {
            $table->integer('status_enum')->default(0)->after('status');
        });

        $checkoutModel = new Checkout();

        foreach (Checkout::cursor() as $checkout) {

            $checkout->update([
                'status_enum' => $checkoutModel->present()->getStatusEnum($checkout->status)
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
        Schema::table('checkouts', function (Blueprint $table) {
            $table->dropColumn('status_enum');
        });
    }

}


