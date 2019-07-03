<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableDomainColumn extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        $sql = 'UPDATE domains SET status = null';
        DB::select($sql);

        Schema::table('domains', function(Blueprint $table) {
            $table->renameColumn('project', 'project_id');
            $table->integer('status')->default(1)->change();
        });

        $sql = 'UPDATE domains SET status = 3';
        DB::select($sql);
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::table('domains', function(Blueprint $table) {
            $table->renameColumn('project_id', 'project');
            $table->string('status')->change();
        });

        $sql = "UPDATE domains SET status = 'Conectado'";
        DB::select($sql);
    }
}
