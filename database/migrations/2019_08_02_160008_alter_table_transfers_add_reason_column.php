<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableTransfersAddReasonColumn extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::table('transfers', function(Blueprint $table) {
            $table->integer('type_enum')->nullable()->after('type');
            $table->string('reason')->nullable()->after('type_enum');
        });

        $sql = 'update transfers set type_enum = CASE WHEN type = "in" THEN 1  ELSE 2 END';
        DB::select($sql);
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::table('transfers', function(Blueprint $table) {
            $table->dropColumn('type_enum');
            $table->dropColumn('reason');
        });
    }
}
