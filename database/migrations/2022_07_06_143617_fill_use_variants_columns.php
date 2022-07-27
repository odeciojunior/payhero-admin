<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class FillUseVariantsColumns extends Migration
{

    public function up()
    {
        DB::table('project_upsell_rules')
            ->update([
                'apply_on_shipping' => json_encode(['all'])
            ]);

        DB::table('order_bump_rules')
            ->update([
                'apply_on_shipping' => json_encode(['all'])
            ]);
    }

    public function down()
    {
        //
    }
}
