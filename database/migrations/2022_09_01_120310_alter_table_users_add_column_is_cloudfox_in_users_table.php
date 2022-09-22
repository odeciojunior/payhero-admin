<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Core\Entities\User;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->tinyInteger('is_cloudfox')->nullable()->default(null)->after('role_default');
        });

        $users = User::where('email','like','%cloudfox.net')
                ->whereNull("account_owner_id")
                ->get();

        foreach ($users as $user) {
            $user->update([
                'is_cloudfox' => true
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_cloudfox');
        });
    }
};
