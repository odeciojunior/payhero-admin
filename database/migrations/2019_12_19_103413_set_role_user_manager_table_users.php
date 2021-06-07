<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Modules\Core\Entities\User;
use Spatie\Permission\Models\Role;

class SetRoleUserManagerTableUsers extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
public function up()
    {
        Role::create(['name' => 'document_evaluation']);
        Role::create(['name' => 'antifraud_analysis']);

        $userManager = User::find(20);
        $userManager->syncRoles(['attendance']);

        $userAdmin = User::create(
            [
                'name'           => "Manager admin",
                'email'          => "admin@cloudfox.net",
                'email_verified' => "1",
                'password'       => bcrypt('C4Pj7mvUaR@&F^mPLX#T'),
            ]
        );
        $userAdmin->assignRole('admin');

        $userDocumentEvaluation = User::create(
            [
                'name'           => "Manager doc",
                'email'          => "doc@cloudfox.net",
                'email_verified' => "1",
                'password'       => bcrypt('@IhjrGc9aX&5zB1KCej4'),
            ]
        );

        $userDocumentEvaluation->assignRole('document_evaluation');

        $userAntifraud = User::create(
            [
                'name'           => "Manager antifraud",
                'email'          => "antifraud@cloudfox.net",
                'email_verified' => "1",
                'password'       => bcrypt('h&8yY9gBG5X&lBo16Fdh'),
            ]
        );

        $userAntifraud->assignRole('antifraud_analysis');
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        //
    }
}
