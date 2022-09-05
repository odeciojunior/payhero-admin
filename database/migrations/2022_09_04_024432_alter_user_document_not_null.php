<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        User::withTrashed()->find(571)->update([
            "document" => "35470207001"
        ]);

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
