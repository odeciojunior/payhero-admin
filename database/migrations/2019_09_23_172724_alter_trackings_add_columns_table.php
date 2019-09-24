<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTrackingsAddColumnsTable extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::table('tracking_histories', function(Blueprint $table) {
            //            $table->bigIncrements('id'); // Já tem
            $table->unsignedBigInteger('plans_sale_id')->after('id')->nullable()->index();
            $table->unsignedBigInteger('delivery_id')->nullable()->change(); // Já tem
            $table->string('tracking_code')->nullable()->change(); // Já tem
            $table->tinyInteger('tracking_type_enum')->nullable();
            $table->tinyInteger('tracking_status_enum')->nullable();
            $table->dateTime('tracking_date')->nullable();
            $table->text('description')->nullable();
            //            $table->timestamps(); // Já tem
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::table('tracking_histories', function(Blueprint $table) {
            $table->dropForeign('tracking_histories_delivery_id_foreign');
        });
        Schema::table('tracking_histories', function(Blueprint $table) {
            $table->dropIndex('tracking_histories_delivery_id_index');
        });
        Schema::table('tracking_histories', function(Blueprint $table) {
            $table->foreign('delivery_id')->references('id')->on('deliveries');
        });
        Schema::table('tracking_histories', function(Blueprint $table) {
            $table->dropColumn('plans_sale_id');
            $table->unsignedBigInteger('delivery_id')->index()->change();
            $table->string('tracking_code')->change();
            $table->dropColumn('tracking_type_enum');
            $table->dropColumn('tracking_status_enum');
            $table->dropColumn('tracking_date');
            $table->dropColumn('description');
            $table->dropSoftDeletes();
        });
    }
}
