<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Modules\Core\Entities\User;

class AlterUsersTableAddDebitTaxes extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::table("users", function(Blueprint $table) {
            $table->string("debit_card_tax")->after("boleto_release_money_days")->default('5.9');
            $table->unsignedInteger("debit_card_release_money_days")->after("debit_card_tax")->default(2);
        });

        $users = User::all();
        foreach ($users as $user) {
            $user->debit_card_tax = $user->boleto_tax;
            $user->save();
        }
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::table("users", function(Blueprint $table) {
            $table->dropColumn(["debit_card_tax", "debit_card_release_money_days"]);
        });
    }
}
