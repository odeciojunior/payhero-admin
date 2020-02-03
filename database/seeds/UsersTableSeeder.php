<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Modules\Core\Entities\User;

/**
 * Class UsersTableSeeder
 */
class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * @return void
     */
    public function run()
    {
        if (env('APP_ENV', 'local') != 'production') {
            User::query()
                ->whereNotNull('email')
                ->update(['password' => Hash::make('resende2019')]); //resende2019
        }
    }
}
