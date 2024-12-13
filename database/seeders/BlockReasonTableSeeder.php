<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Core\Entities\BlockReason;

class BlockReasonTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
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
}
