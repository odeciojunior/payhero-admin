<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("sale_informations", function (Blueprint $table) {
            $table->index("operational_system");
            $table->index("browser");
            $table->index("browser_fingerprint");
            $table->index("customer_phone");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("sale_informations", function (Blueprint $table) {
            $table->dropIndex(["operational_system"]);
            $table->dropIndex(["browser"]);
            $table->dropIndex(["browser_fingerprint"]);
            $table->dropIndex(["customer_phone"]);
        });
    }
};
