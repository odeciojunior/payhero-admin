<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Core\Entities\BlockReason;

class BlockReasonsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        BlockReason::create([
            "reason" => "Em disputa",
            "reason_enum" => 1,
        ]);

        BlockReason::create([
            "reason" => "Sem rastreio",
            "reason_enum" => 2,
        ]);

        BlockReason::create([
            "reason" => "Rastreio sem movimentação",
            "reason_enum" => 3,
        ]);

        BlockReason::create([
            "reason" => "O código não foi reconhecido por nenhuma transportadora",
            "reason_enum" => 4,
        ]);

        BlockReason::create([
            "reason" => "A data de postagem da remessa é anterior a data da venda",
            "reason_enum" => 5,
        ]);

        BlockReason::create([
            "reason" => "Já existe uma venda com o código de rastreio informado",
            "reason_enum" => 6,
        ]);

        BlockReason::create([
            "reason" => "Outros",
            "reason_enum" => 7,
        ]);

        BlockReason::create([
            "reason" => "Chamado aberto",
            "reason_enum" => 8,
        ]);
    }
}
