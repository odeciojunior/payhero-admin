<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterAffiliatesAddContact extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('affiliates', function (Blueprint $table) {
            $table->boolean('suport_phone_verified')->default(0)->after('status_enum');
            $table->string('suport_phone', 20)->nullable()->after('status_enum');
            $table->boolean('suport_contact_verified')->default(0)->after('status_enum');
            $table->string('suport_contact')->nullable()->after('status_enum');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('affiliates', function (Blueprint $table) {
            $table->dropColumn('suport_phone_verified');
            $table->dropColumn('suport_phone');
            $table->dropColumn('suport_contact_verified');
            $table->dropColumn('suport_contact');
        });
    }
}
