<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class UpdateCheckoutsClientColumns extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        DB::update(
            "UPDATE checkouts c
            JOIN (
                SELECT id_log_session, MAX(id) last_log_id
                FROM logs 
                GROUP BY id_log_session
            ) ll 
            ON ll.id_log_session = c.id_log_session
            JOIN logs l
            ON ll.last_log_id = l.id
            SET c.client_name = l.name
            , c.client_telephone = l.telephone
            WHERE c.status IN ('recovered', 'abandoned cart');"
        );
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        //
    }
}
