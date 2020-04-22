<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnAcceptedAtTermsTableUserTerms extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::table('user_terms', function(Blueprint $table) {
            $table->dateTime('accepted_at_terms')
                  ->nullable()
                  ->after('accepted_at');
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::table('user_terms', function(Blueprint $table) {
            $table->dropColumn('accepted_at_terms');
        });
    }
}
