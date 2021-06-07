<?php

use Modules\Core\Entities\Pixel;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdatePixelsTabel extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $pixels = Pixel::where('platform', 'like', '%google%')->get();

        foreach ($pixels as $pixel) {
            $pixel->update([
                               'platform' => 'google_adwords',
                           ]);
        }    
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
