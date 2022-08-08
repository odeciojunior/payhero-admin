<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Core\Entities\Pixel;

class AddPurchaseAllInPixels extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("pixels", function (Blueprint $table) {
            $table
                ->boolean("purchase_all")
                ->default(true)
                ->after("send_value_checkout");
        });

        foreach (Pixel::where("platform", "google_adwords")->get() as $pixel) {
            $sum =
                $pixel->checkout +
                $pixel->send_value_checkout +
                $pixel->purchase_all +
                $pixel->basic_data +
                $pixel->delivery +
                $pixel->coupon +
                $pixel->payment_info +
                $pixel->purchase_card +
                $pixel->purchase_boleto +
                $pixel->purchase_pix +
                $pixel->upsell +
                $pixel->purchase_upsell;

            if ($sum > 1) {
                $pixel->update([
                    "checkout" => false,
                    "send_value_checkout" => false,
                    "purchase_all" => true,
                    "basic_data" => false,
                    "delivery" => false,
                    "coupon" => false,
                    "payment_info" => false,
                    "purchase_card" => false,
                    "purchase_boleto" => false,
                    "purchase_pix" => false,
                    "upsell" => false,
                    "purchase_upsell" => false,
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("pixels", function (Blueprint $table) {
            $table->dropColumn("purchase_all");
        });
    }
}
