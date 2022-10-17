<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddObservationsInSales extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("users", function (Blueprint $table) {
            $table
                ->text("observation")
                ->nullable()
                ->change();
        });

        Schema::table("sales", function (Blueprint $table) {
            $table
                ->text("observation")
                ->nullable()
                ->after("has_valid_tracking");
        });

        DB::statement("ALTER TABLE sales MODIFY COLUMN created_at timestamp AFTER observation");
        DB::statement("ALTER TABLE sales MODIFY COLUMN updated_at timestamp AFTER created_at");
        DB::statement("ALTER TABLE sales MODIFY COLUMN deleted_at timestamp AFTER updated_at");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("sales", function (Blueprint $table) {
            $table->dropColumn("observation");
        });
    }
}
