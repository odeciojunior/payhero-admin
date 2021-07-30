<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Core\Entities\User;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;
use Spatie\Permission\Models\Role;

class NewRoleCustomUserTableRoles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Role::create(['name' => 'custom']);        

        $userAdmin = User::create(
            [
                'name'           => "Manager custom",
                'email'          => "custom@cloudfox.net",
                'email_verified' => "1",
                'password'       => bcrypt('P0Rj7mvP0RIO@&F^mPLX#T'),
            ]
        );
        
        $userAdmin->assignRole('custom');

//dashboard
//sales_geral
//sales_geral_reverse
//sales_recovery
//sales_tracking
//sales_tracking_manage
//sales_


// vendas -> contestações
// 	enviar documentação
// projetos
// 	gerenciar
// produtos
// 	gerenciar
// atendimento
// 	gerenciar
// finanças
// 	gerenciar
// relatorios -> vendas
// relatorios -> acessos
// relatorios -> descontos
// relatorios -> saldo pendente
// relatorios -> saldo bloqueado
// afiliados
// 	gerenciar
// aplicativos
// 	gerenciar
// convites
// 	gerenciar
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
