<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Modules\Core\Entities\User;
use Modules\Core\Entities\UserNotification;

class CreateUserNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::create('user_notifications', function(Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('user_id')->unsigned();
            $table->boolean('new_affiliation')->default(true);
            $table->boolean('new_affiliation_request')->default(true);
            $table->boolean('approved_affiliation')->default(true);
            $table->boolean('boleto_compensated')->default(true);
            $table->boolean('billet_generated')->default(true);
            $table->boolean('credit_card_in_proccess')->default(true);
            $table->boolean('sale_approved')->default(true);
            $table->boolean('notazz')->default(true);
            $table->boolean('withdrawal_approved')->default(true);
            $table->boolean('released_balance')->default(true);
            $table->boolean('domain_approved')->default(true);
            $table->boolean('shopify')->default(true);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
        });

        $users = User::get();
        foreach ($users as $user) {
            UserNotification::create(
                [
                    "user_id" => $user->id,
                ]
            );
        }
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        $users = User::get();
        foreach ($users as $user) {
            UserNotification::where("user_id", $user->id)->delete();
        }

        Schema::table('user_notifications', function(Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        Schema::dropIfExists('user_notifications');
    }
}
