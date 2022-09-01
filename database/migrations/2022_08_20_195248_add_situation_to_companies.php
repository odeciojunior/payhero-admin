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


        // Schema::table('companies', function (Blueprint $table) {
        //     $table->string('situation', 10)->default('active')->after("annual_income");
        //     $table->integer('situation_enum')->default(1)->after("situation");
        //     $table->dateTime("date_check_situation")->default(DB::raw('CURRENT_TIMESTAMP'))->after("situation_enum");

        // });

        // $subThreeDays = Carbon::now()
        //         ->subDays(7)
        //         ->format("Y-m-d H:i:s");
        //     foreach (Company::cursor() as $company) {
        //         $company->update([
        //                 'date_check_situation' => $subThreeDays,
        //             ]);
        //     }
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
