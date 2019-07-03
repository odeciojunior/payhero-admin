<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeStatusColumnInvitationsTable extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        $sql = 'UPDATE invitations SET status = null';
        DB::select($sql);

        Schema::table('invitations', function(Blueprint $table) {
            $table->integer('status')->default(2)->change();
        });

        $sql = 'UPDATE invitations SET status = 2';
        DB::select($sql);
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::table('invitations', function(Blueprint $table) {
            $table->string('status')->change();
        });

        $sql = "UPDATE invitations SET status = 'Convite enviado'";
        DB::select($sql);
    }
}
