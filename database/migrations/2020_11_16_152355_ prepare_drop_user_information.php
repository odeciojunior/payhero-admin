<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Modules\Core\Entities\User;

class PrepareDropUserInformation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        try {
            Schema::table("users", function (Blueprint $table) {
                $table->string("sex", "50")->nullable();
                $table->string("mother_name", "255")->nullable();

                $table->dropColumn("get_net_status");
                $table->dropColumn("abroad_transfer_tax");
                $table->dropColumn("debit_card_tax");
                $table->dropColumn("debit_card_antecipation_money_days");
                $table->dropColumn("credit_card_antecipation_money_days");
                $table->dropColumn("boleto_antecipation_money_days");
                $table->dropColumn("percentage_antecipable");
                $table->dropColumn("antecipation_tax");
                $table->dropColumn("debit_card_release_money_days");
                $table->dropColumn("percentage_rate");
                $table->dropColumn("antecipation_enabled_flag");
            });

            DB::statement("ALTER TABLE users MODIFY COLUMN created_at timestamp AFTER mother_name");
            DB::statement("ALTER TABLE users MODIFY COLUMN updated_at timestamp AFTER created_at");
            DB::statement("ALTER TABLE users MODIFY COLUMN deleted_at timestamp AFTER updated_at");

            $usersInformation = DB::select(
                "select * from user_informations where mother_name is not null or sex is not null"
            );

            foreach ($usersInformation as $userInformation) {
                $user = User::find($userInformation->user_id);

                if (!empty($userInformation->sex)) {
                    $user->update(["sex" => $userInformation->sex]);
                }
                if (!empty($userInformation->mother_name)) {
                    $user->update(["mother_name" => $userInformation->mother_name]);
                }
            }

            Schema::dropIfExists("user_informations");
        } catch (Exception $e) {
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
