<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("reserved_sales", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->unsignedBigInteger("sale_id")->index("reserved_sales_sale_id_foreign");
            $table->unsignedInteger("user_id")->index("reserved_sales_user_id_foreign");
            $table
                ->string("reason", 200)
                ->nullable()
                ->default(null);
            $table->timestamps();
        });

        DB::statement("UPDATE users SET contestation_penalty_tax = '5000' WHERE contestation_penalty_tax = '4000'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists("reserved_sales");
    }
};
