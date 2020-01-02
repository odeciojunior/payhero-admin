<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeNameColumnToNullableOnPlansAndProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->string('name')->default('sem nome')->change();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->string('name')->default('sem nome')->change();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->string('name')->default('')->change();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->string('name')->default('')->change();
        });
    }

}

