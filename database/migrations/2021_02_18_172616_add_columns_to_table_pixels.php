<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToTablePixels extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(
            'pixels',
            function (Blueprint $table) {
                $table->string('outbrain_conversion_name')->default('Purchase')->nullable()->after(
                    'code_meta_tag_facebook'
                );
                $table->string('taboola_conversion_name')->default('make_purchase')->nullable()->after(
                    'outbrain_conversion_name'
                );
            }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(
            'pixels',
            function (Blueprint $table) {
                $table->dropColumn(['outbrain_conversion_name', 'taboola_conversion_name']);
            }
        );
    }
}
