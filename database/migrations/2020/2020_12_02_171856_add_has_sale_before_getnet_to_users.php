<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\User;

class AddHasSaleBeforeGetnetToUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("users", function (Blueprint $table) {
            $table
                ->boolean("has_sale_before_getnet")
                ->default(0)
                ->after("mother_name");
        });

        foreach (User::all() as $user) {
            $hasSaleBeforeGetnet = Sale::where(function ($q) use ($user) {
                $q->where("owner_id", $user->account_owner_id)->orWhere("affiliate_id", $user->account_owner_id);
            })
                ->whereNotIn("gateway_id", [Gateway::GETNET_SANDBOX_ID, Gateway::GETNET_PRODUCTION_ID])
                ->exists();

            if ($hasSaleBeforeGetnet) {
                $user->update([
                    "has_sale_before_getnet" => true,
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
        Schema::table("users", function (Blueprint $table) {
            $table->dropColumn("has_sale_before_getnet");
        });
    }
}
