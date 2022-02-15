<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNewSalesInformationColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(
            'sale_informations',
            function (Blueprint $table) {
                $table->string('card_holder')->index()->after('street_number')->nullable();
                $table->string('url')->index()->after('ip')->nullable();
                $table->string('attempt_reference')->index()->after('url')->nullable();
            }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(
            'sale_informations',
            function (Blueprint $table) {
                $table->dropColumn('card_holder');
                $table->dropColumn('url');
                $table->dropColumn('attempt_reference');
            }
        );
    }
}
