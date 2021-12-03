<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Core\Entities\PromotionalTax;
use Modules\Core\Services\UserService;

class FixTaxCompanies extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $promotional_taxes = PromotionalTax::where('old_tax', 'like', '%3.9%')
            ->count();

        foreach ($promotional_taxes as $promotional_tax) {
            (new UserService())->removePromotionalTax($promotional_tax);
        }
    }
}
