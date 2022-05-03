<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewCollumsDiscountTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('discount_coupons', function (Blueprint $table) {
            $table->json('progressive_rules')->after('rule_value')->nullable();
            $table->json('plans')->after('progressive_rules')->nullable();
            $table->tinyInteger('discount')->after('plans')->default(0);
            $table->dateTime('expires')->nullable()->after('code');

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
        Schema::table('discount_coupons', function (Blueprint $table) {
            $table->dropColumn(['progressive_rules']);
            $table->dropColumn(['plans']);
            $table->dropColumn(['discount']);
            $table->dropColumn(['expires']);
        });

    }
}
