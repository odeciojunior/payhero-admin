<?php

declare(strict_types=1);

namespace Modules\SalesRecovery\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class SalesRecoveryDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * @return void
     */
    public function run()
    {
        Model::unguard();
    }
}
