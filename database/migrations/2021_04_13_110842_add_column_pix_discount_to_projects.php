<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class AddColumnPixDiscountToProjects
 */
class AddColumnPixDiscountToProjects extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("projects", function (Blueprint $table) {
            $table
                ->boolean("pix")
                ->after("credit_card")
                ->default(false);
            $table
                ->string("pix_redirect")
                ->after("boleto_redirect")
                ->nullable();
            $table
                ->bigInteger("pix_discount")
                ->default(0)
                ->after("credit_card_discount");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("projects", function (Blueprint $table) {
            $table->dropColumn("pix");
            $table->dropColumn("pix_redirect");
            $table->dropColumn("pix_discount");
        });
    }
}
