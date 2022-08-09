<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Core\Entities\User;

class AddColumnShowOldFinancesTableUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("users", function (Blueprint $table) {
            $table
                ->boolean("show_old_finances")
                ->default(false)
                ->after("total_commission_value");
        });

        $users = User::get();

        foreach ($users as $user) {
            if ($user->has_sale_before_getnet) {
                $user->update([
                    "show_old_finances" => true,
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("users", function (Blueprint $table) {
            $table->dropColumn("show_old_finances");
        });
    }
}
