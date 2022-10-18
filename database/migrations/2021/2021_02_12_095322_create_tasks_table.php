<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("tasks", function (Blueprint $table) {
            $table->id();
            $table->string("name", 100);
            $table->integer("level");
            $table->integer("priority");
            $table->timestamps();
        });

        DB::statement("INSERT INTO tasks (id, name, level, priority, created_at)
                             VALUES (1,'Tenha seus documentos aprovados', 1, 1, now()),
                                    (2,'Cadastre sua primeira loja',      1, 2, now()),
                                    (3,'Aprove seu primeiro domínio',     1, 3, now()),
                                    (4,'Faça sua primeira venda',         1, 4, now()),
                                    (5,'Fature R$1.000,00',               1, 5, now()),
                                    (6,'Faça seu primeiro saque',         1, 6, now())");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists("tasks");
    }
}
