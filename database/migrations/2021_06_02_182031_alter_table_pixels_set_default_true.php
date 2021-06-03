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
        $pixels = new Pixel();
        $pixels->where('checkout', 'true')->update(['checkout' => 1]);
        $pixels->where('checkout', 'false')->update(['checkout' => 0]);

        $pixels->where('purchase_boleto', 'true')->update(['purchase_boleto' => 1]);
        $pixels->where('purchase_boleto', 'false')->update(['purchase_boleto' => 0]);

        $pixels->where('purchase_card', 'true')->update(['purchase_card' => 1]);
        $pixels->where('purchase_card', 'false')->update(['purchase_card' => 0]);

        $pixels->where('purchase_pix', 'true')->update(['purchase_pix' => 1]);
        $pixels->where('purchase_pix', 'false')->update(['purchase_pix' => 0]);
        $pixels->whereNull('purchase_pix')->update(['purchase_pix' => 1]);

        Schema::table('pixels', function (Blueprint $table) {
            $table->string('checkout')->default(1)->change();
            $table->string('purchase_boleto')->default(1)->change();
            $table->string('purchase_card')->default(1)->change();
            $table->string('purchase_pix')->default(1)->change();
            $table->boolean('checkout')->change();
            $table->boolean('purchase_boleto')->change();
            $table->boolean('purchase_card')->change();
            $table->boolean('purchase_pix')->change();

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
