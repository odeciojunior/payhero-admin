<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Core\Entities\Company;

class AddSituationToCompanies extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->json('situation')->after('annual_income')->nullable();
        });

        $subDays = Carbon::now()->subDays(7)->format("Y-m-d H:i:s");

        // Default penalty values
        $data = [
            'situation' => 'active',
            'situation_enum' => 1,
            'date_check_situation' => $subDays,
        ];

        // Company records with default values
        Company::query()->update(['situation' => json_encode($data)]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn("situation");
        });
    }
}
