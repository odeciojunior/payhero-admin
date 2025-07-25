<?php

declare(strict_types=1);

namespace Modules\Integrations\Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class IntegrationsDatabaseSeeder extends Seeder
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
