<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableDomainsRecords extends Migration
{
    /**php artisan krlove:generate:model Checkout --table-name=checkouts --namespace=App\\Entities
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::create('domains_records', function(Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('domain_id')->index();
            $table->string('type');
            $table->string('name');
            $table->string('content');
            $table->tinyInteger('system_flag')->default(1);

            $table->timestamps();
        });

        Schema::table('domains_records', function(Blueprint $table) {
            $table->foreign('domain_id')->references('id')->on('domains');
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::table('domains_records', function(Blueprint $table) {
            $table->dropForeign(['domain_id']);
        });

        Schema::dropIfExists('domains_records');
    }
}
