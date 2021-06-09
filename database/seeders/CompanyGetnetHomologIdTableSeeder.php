<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Core\Entities\Company;

/**
 * Class CompanyGetnetHomologIdTableSeeder
 */
class CompanyGetnetHomologIdTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * @return void
     */
    public function run()
    {
        if (env('APP_ENV', 'local') != 'production') {
            Company::query()
                ->whereNotNull('id')
                ->update(['subseller_getnet_id' => '700051332', 'subseller_getnet_homolog_id' => '700051332']);
        }
    }
}
