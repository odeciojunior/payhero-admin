<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSaleContestationFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("sale_contestation_files", function (Blueprint $table) {
            $table->id();
            $table->bigInteger("contestation_sale_id")->unsigned();
            $table
                ->foreign("contestation_sale_id")
                ->references("id")
                ->on("sale_contestations");
            $table->integer("user_id")->unsigned();
            $table
                ->foreign("user_id")
                ->references("id")
                ->on("users");
            $table->enum("type", ["NOTA_FISCAL", "POLITICA_VENDA", "ENTREGA", "INFO_ACORDO", "OUTROS"]);
            $table->string("file")->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists("sale_contestation_files");
    }
}
