<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Core\Entities\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AssignPermissionsToRolesAndUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    { 
        $roles = Role::all();
        $permissionsList = Permission::all()->pluck('name');
        $permissions = [
            'admin' => [], 
            'account_owner' => [],
            'attendance' => [],
            'finantial' => [],
            'custom'=>[],
            'document_evaluation' => [],
            'antifraud_analysis' => []
        ];
        
        /**
         * admin e account_owner tem as mesmas permissões no sirius, porem eles podem diferir em algumas validações feitas por função 
         */         
        foreach($roles as $role){
            switch($role->name){
                case 'admin': //cloudfox
                    $permissions['admin'] = $permissionsList;
                    unset($permissions['admin']['invitations_manage']);
                    $role->syncPermissions($permissions['admin']); 
                break;
                case 'account_owner': //customer
                    $permissions['account_owner'] = $permissionsList;
                    $role->syncPermissions($permissions['account_owner']); 
                break;
                case 'attendance': //customer
                    $permissions['attendance'] = [
                        'sales',
                        'recovery',
                        'trackings',
                        'trackings_manage',                        
                        'contestations',
                        'contestations_manage',
                        'attendance',
                        'attendance_manage',
                        'report_coupons'
                    ];
                    $role->syncPermissions($permissions['attendance']); 
                break;
                case 'document_evaluation'://cloudfox
                    $permissions['document_evaluation'] = ['finances','finances_manage'];
                    $role->syncPermissions($permissions['document_evaluation']); 
                break;
                case 'antifraud_analysis'://cloudfox
                    $permissions['antifraud_analysis'] = ['finances','finances_manage'];
                    $role->syncPermissions($permissions['antifraud_analysis']); 
                break;
                case 'finantial': //customer
                    $permissions['finantial'] = [
                        'dashboard',
                        'sales',                        
                        'finances',
                        'finances_manage',
                        'report_sales', 
                        'report_pending',
                        'report_blockedbalance'
                    ];
                    $role->syncPermissions($permissions['finantial']); 
                break;
                case 'custom': //customer                    
                    $role->syncPermissions([]); 
                break;
            }                                       
        }

        foreach($roles as $role){            
            $users = User::role($role->name)->get();
            foreach($users as $user){
                $user->syncPermissions(collect($permissions[$role->name]));
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        
    }
}
