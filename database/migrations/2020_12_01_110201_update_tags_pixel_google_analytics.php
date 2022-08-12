<?php

use Illuminate\Database\Migrations\Migration;
use Modules\Core\Entities\Pixel;

class UpdateTagsPixelGoogleAnalytics extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $pixels = Pixel::where("platform", "google_analytics")->get();

        foreach ($pixels as $pixel) {
            $pixel->update([
                "code" => "UA-" . $pixel->code,
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
