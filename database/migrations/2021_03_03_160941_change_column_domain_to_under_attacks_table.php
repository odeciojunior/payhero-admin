<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeColumnDomainToUnderAttacksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("under_attacks", function (Blueprint $table) {
            $table
                ->integer("domain_id")
                ->unsigned()
                ->nullable()
                ->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("under_attacks", function (Blueprint $table) {
            $table
                ->integer("domain_id")
                ->unsigned()
                ->nullable(false)
                ->change();
        });
    }
}
