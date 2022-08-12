<?php

use Illuminate\Database\Migrations\Migration;
use Modules\Core\Entities\Benefit;
use Modules\Core\Entities\UserBenefit;

class PopulateBenefitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        UserBenefit::query()->delete();
        Benefit::query()->delete();

        Benefit::firstOrCreate([
            "name" => "cashback_1",
            "level" => 2,
            "description" => "Receba de 0,5% até 5,5% de cashback",
        ]);

        Benefit::firstOrCreate([
            "name" => "get_faster",
            "level" => 2,
            "description" =>
                "Receba a sua comissão mais rápido, sem a necessidade de avaliação na solicitação de saque",
        ]);

        Benefit::firstOrCreate([
            "name" => "cashback_2",
            "level" => 3,
            "description" => "Receba de 1% até 11% de cashback",
        ]);

        Benefit::firstOrCreate([
            "name" => "account_manager",
            "level" => 3,
            "description" => "Gerente de contas",
        ]);

        Benefit::firstOrCreate([
            "name" => "rate_reduction",
            "level" => 4,
            "description" => "Redução proporcional da taxa",
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Benefit::query()->delete();
    }
}
