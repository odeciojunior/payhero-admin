<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsTableProjects extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('projects', function(Blueprint $table) {
            $table->dropColumn('url_redirect');
            $table->string('boleto_redirect')->nullable();
            $table->string('card_redirect')->nullable();
            $table->string('analyzing_redirect')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('projects', function(Blueprint $table) {
            $table->string('url_redirect')->nullable();
            $table->dropColumn('boleto_redirect');
            $table->dropColumn('card_redirect');
            $table->dropColumn('analyzing_redirect');
        });
    }
}
