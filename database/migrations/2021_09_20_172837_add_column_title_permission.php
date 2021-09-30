<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnTitlePermission extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        DB::statement("ALTER TABLE permissions ADD COLUMN title VARCHAR(100) NULL DEFAULT NULL AFTER name;");

        DB::statement("UPDATE `permissions` SET title = 'Vendas - Gerenciar' WHERE id = 1");
        DB::statement("UPDATE `permissions` SET title = 'Dashboard' WHERE id = 2");
        DB::statement("UPDATE `permissions` SET title = 'Vendas' WHERE id = 3");
        DB::statement("UPDATE `permissions` SET title = 'Recuperação' WHERE id = 4");
        DB::statement("UPDATE `permissions` SET title = 'Rastreamento' WHERE id = 5");
        DB::statement("UPDATE `permissions` SET title = 'Rastreamento - Gerenciar' WHERE id = 6");
        DB::statement("UPDATE `permissions` SET title = 'Contestações' WHERE id = 7");
        DB::statement("UPDATE `permissions` SET title = 'Contestações - Gerenciar' WHERE id = 8");
        DB::statement("UPDATE `permissions` SET title = 'Projetos' WHERE id = 9");
        DB::statement("UPDATE `permissions` SET title = 'Projetos - Gerenciar' WHERE id = 10");
        DB::statement("UPDATE `permissions` SET title = 'Produtos' WHERE id = 11");
        DB::statement("UPDATE `permissions` SET title = 'Produtos - Gerenciar' WHERE id = 12");
        DB::statement("UPDATE `permissions` SET title = 'Atendimento' WHERE id = 13");
        DB::statement("UPDATE `permissions` SET title = 'Atendimento - Gerenciar' WHERE id = 14");
        DB::statement("UPDATE `permissions` SET title = 'Finanças' WHERE id = 15");
        DB::statement("UPDATE `permissions` SET title = 'Finanças - Gerenciar' WHERE id = 16");
        DB::statement("UPDATE `permissions` SET title = 'Relatório - Vendas' WHERE id = 17");
        DB::statement("UPDATE `permissions` SET title = 'Relatório - Acessos' WHERE id = 18");
        DB::statement("UPDATE `permissions` SET title = 'Relatório - Cupons de desconto' WHERE id = 19");
        DB::statement("UPDATE `permissions` SET title = 'Relatório - Saque pendente' WHERE id = 20");
        DB::statement("UPDATE `permissions` SET title = 'Relatório - Saldo bloqueado' WHERE id = 21");
        DB::statement("UPDATE `permissions` SET title = 'Afiliados' WHERE id = 22");
        DB::statement("UPDATE `permissions` SET title = 'Afiliados - Gerenciar' WHERE id = 23");
        DB::statement("UPDATE `permissions` SET title = 'Apps' WHERE id = 24");
        DB::statement("UPDATE `permissions` SET title = 'Apps - Gerenciar' WHERE id = 25");
        DB::statement("UPDATE `permissions` SET title = 'Convites' WHERE id = 26");
        DB::statement("UPDATE `permissions` SET title = 'Convites - Gerenciar' WHERE id = 27");
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
