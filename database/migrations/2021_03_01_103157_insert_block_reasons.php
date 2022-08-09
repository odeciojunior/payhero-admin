<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Core\Entities\BlockReason;

class InsertBlockReasons extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        BlockReason::updateOrCreate(["reason_enum" => 1], ["reason" => "Em disputa"]);
        BlockReason::updateOrCreate(["reason_enum" => 2], ["reason" => "Sem rastreio"]);
        BlockReason::updateOrCreate(["reason_enum" => 3], ["reason" => "Rastreio sem movimentação"]);
        BlockReason::updateOrCreate(
            ["reason_enum" => 4],
            ["reason" => "O código não foi reconhecido por nenhuma transportadora"]
        );
        BlockReason::updateOrCreate(
            ["reason_enum" => 5],
            ["reason" => "A data de postagem da remessa é anterior a data da venda"]
        );
        BlockReason::updateOrCreate(
            ["reason_enum" => 6],
            ["reason" => "Já existe uma venda com o código de rastreio informado"]
        );
        BlockReason::updateOrCreate(["reason_enum" => 7], ["reason" => "Outros"]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
