<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Artisan;
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
            'name' => 'cashback',
            'level' => 2,
            'description' => 'Receba de volta um percentual do valor de cada venda.',
        ]);

        Benefit::firstOrCreate([
            'name' => 'get_faster',
            'level' => 2,
            'description' => 'Receba a sua comissão mais rápido, sem a necessidade de avalição na solicitação de saque',
        ]);

        Benefit::firstOrCreate([
            'name' => 'account_manager',
            'level' => 3,
            'description' => 'Gerente de contas',
        ]);

        Benefit::firstOrCreate([
            'name' => 'rate_reduction',
            'level' => 4,
            'description' => 'Redução proporcional da taxa',
        ]);

        Artisan::call('command:update-user-level');
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
