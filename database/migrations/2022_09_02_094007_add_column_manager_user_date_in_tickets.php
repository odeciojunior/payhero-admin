<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dateTime('manager_user_assignment_date')
                ->nullable()
                ->after('manager_user_id');
        });

        $tickets = Facades\DB::table('tickets')
            ->select('id')
            ->whereNotNull('manager_user_id')
            ->get();

        foreach ($tickets as $ticket){
            Facades\DB::table('tickets')
                ->where('id',$ticket->id)
                ->update(['manager_user_assignment_date' => '2022-08-20 11:35:00']);
        }

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn("manager_user_assignment_date");
        });
    }
};
