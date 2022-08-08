<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Core\Entities\User;
use Spatie\Permission\Models\Permission;

class InsertNewPermissionIntoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /**
         * Insert the permission into the database
         */
        Permission::create([
            "name" => "extract_reports",
            "title" => "Extração de Relatórios",
            "guard" => "web",
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Permission::where("name", "extract_reports")->delete();
    }
}
