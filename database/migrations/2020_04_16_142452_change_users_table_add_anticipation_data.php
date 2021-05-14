<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeUsersTableAddAnticipationData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {

            $table->integer('debit_card_antecipation_money_days')->default(5);
            $table->boolean('antecipation_enabled_flag')->default(false);

            $table->dropColumn('release_money_days');
            $table->dropColumn('call_amount');
            $table->dropColumn('email_amount');
            $table->dropColumn('foxcoin');
            $table->dropColumn('sms_zenvia_amount');
            $table->dropColumn('score');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('debit_card_antecipation_money_days');
            $table->dropColumn('antecipation_enabled_flag');
        });
    }
}
