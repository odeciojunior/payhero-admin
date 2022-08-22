<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Core\Entities\Pixel;

class AlterTablePixelsSetDefaultTrue extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Pixel::where("checkout", "true")
            ->withTrashed()
            ->update(["checkout" => 1]);
        Pixel::where("checkout", "false")
            ->withTrashed()
            ->update(["checkout" => 0]);
        Pixel::whereNull("checkout")
            ->withTrashed()
            ->update(["checkout" => 0]);

        Pixel::where("purchase_boleto", "true")
            ->withTrashed()
            ->update(["purchase_boleto" => 1]);
        Pixel::where("purchase_boleto", "false")
            ->withTrashed()
            ->update(["purchase_boleto" => 0]);
        Pixel::whereNull("purchase_boleto")
            ->withTrashed()
            ->update(["purchase_boleto" => 0]);

        Pixel::where("purchase_card", "true")
            ->withTrashed()
            ->update(["purchase_card" => 1]);
        Pixel::where("purchase_card", "false")
            ->withTrashed()
            ->update(["purchase_card" => 0]);
        Pixel::whereNull("purchase_card")
            ->withTrashed()
            ->update(["purchase_card" => 0]);

        Pixel::where("purchase_pix", "true")
            ->withTrashed()
            ->update(["purchase_pix" => 1]);
        Pixel::where("purchase_pix", "false")
            ->withTrashed()
            ->update(["purchase_pix" => 0]);
        Pixel::whereNull("purchase_pix")
            ->withTrashed()
            ->update(["purchase_pix" => 1]);

        Schema::table("pixels", function (Blueprint $table) {
            $table
                ->string("checkout")
                ->default(1)
                ->change();
            $table
                ->string("purchase_boleto")
                ->default(1)
                ->change();
            $table
                ->string("purchase_card")
                ->default(1)
                ->change();
            $table
                ->string("purchase_pix")
                ->default(1)
                ->change();

            $table->boolean("checkout")->change();
            $table->boolean("purchase_boleto")->change();
            $table->boolean("purchase_card")->change();
            $table->boolean("purchase_pix")->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
