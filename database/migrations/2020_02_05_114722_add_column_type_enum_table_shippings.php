<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Modules\Core\Entities\Shipping;

class AddColumnTypeEnumTableShippings extends Migration
{
    /**
     * @throws \Laracasts\Presenter\Exceptions\PresenterException
     */
    public function up()
    {
        Schema::table('shippings', function (Blueprint $table) {
            $table->integer('type_enum')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shippings', function (Blueprint $table) {
            $table->dropColumn(['type_enum']);
        });
    }
}


