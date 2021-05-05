<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropColumnAcceptedAtAndRenameColumnAcceptedAtTermsTableUserTerms extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::table('user_terms', function(Blueprint $table) {
            $table->dropColumn('accepted_at');
            $table->renameColumn('accepted_at_terms', 'accepted_at');
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::table('user_terms', function(Blueprint $table) {
            $table->boolean('accepted_at')->nullable()
                  ->after('device_data');
            $table->renameColumn('accepted_at', 'accepted_at_terms');
        });
    }
}
