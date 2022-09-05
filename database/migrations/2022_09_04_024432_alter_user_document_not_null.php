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
        $users = User::withTrashed();

        foreach( $users->cursor() as $user ){

            // remove mask document
            $document = foxutils()->onlyNumbers($user->document);

            // remove mask document
            $user->update(["document"=> $document]);


            if(!foxutils()->isEmpty($user->document)) continue;

            // if document null set hash the id
            $document2 = '00000000000';

            $user->update(["document"=> $document2]);

        }

        Schema::table('users', function (Blueprint $table) {
            $table->string('document', 11)->nullable(false)->change();
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
            $table->string('document')->nullable()->change();
        });
    }
};

//php artisan migrate --path=/database/migrations/2022_08_31_101718_add_column_bureau_check_count_to_users_table.php
