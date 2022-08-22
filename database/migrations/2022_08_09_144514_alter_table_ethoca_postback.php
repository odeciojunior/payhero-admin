<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableEthocaPostback extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table('ethoca_postbacks',function(Blueprint $table)
        {
            $table->unsignedBigInteger("sale_id")->nullable()->change();
            $table->foreign("sale_id")->references("id")->on("sales");

            $table->tinyInteger('is_cloudfox')->default(0)->after('data');

            $table->json('machine_result')->nullable()->after('processed_flag');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ethoca_postbacks',function(Blueprint $table)
        {
            $table->dropColumn('is_cloudfox');

            $table->dropColumn('machine_result');

            $table->dropForeign(["sale_id"]);
        });
    }
}
