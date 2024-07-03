<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Adicionar um novo valor de enumeração à coluna existente
        DB::statement("ALTER TABLE api_tokens MODIFY COLUMN platform_enum ENUM('VEGA_CHECKOUT', 'GR_SOLUCOES', 'ADOOREI_CHECKOUT', 'WEBAPI') NULL DEFAULT NULL");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Remover o novo valor de enumeração (opcional)
        DB::statement("ALTER TABLE api_tokens MODIFY COLUMN platform_enum ENUM('VEGA_CHECKOUT', 'GR_SOLUCOES', 'UTMIFY', 'WEBAPI') NULL DEFAULT NULL");
    }
};
