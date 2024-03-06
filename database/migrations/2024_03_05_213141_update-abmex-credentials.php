<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement(
            "UPDATE `gateways` SET `json_config`='WwJTRUNSRVR/S0VZAhoCU0t/TElWRX9meGFJUlZEV0VpeHJuUWxyehhuQURzc0oQURBzTmwRRFpZSVZUbUZEWW4CDAJQVUJMSUN/S0VZAhoCUEt/TElWRX9TZm9PR1REeVlSeGd4Tnl0YxlGFGZjWXdXehlzFEMCXQ==' WHERE id = 11;"
        );
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
};
