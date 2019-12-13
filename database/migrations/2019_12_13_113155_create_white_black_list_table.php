<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWhiteBlackListTable extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::create('white_black_list', function(Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger("type_enum")->comment("Tipo (1 - White/ 2 - Black)");
            $table->string("rule")->comment("Regra");
            $table->unsignedInteger("rule_enum")->comment("Enum da regra");
            $table->string("rule_type")->comment("Tipo de regra (Equals, More, Less)")->default(1);
            $table->unsignedInteger("rule_type_enum")->comment("Enum do tipo da regra (1 - Igual, 2 - Maior/Menor)");
            $table->string("value")->comment("Valor a verificar na regra");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('white_black_list');
    }
}
