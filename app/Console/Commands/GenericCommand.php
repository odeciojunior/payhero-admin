<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\Tracking;
use Modules\Core\Entities\User;
use Modules\Core\Services\TrackingService;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class GenericCommand extends Command
{
    protected $signature = 'generic';

    protected $description = 'Command description';

    public function handle()
    {
        $role = Role::where('name','account_owner')->first();        
        
        $user = User::find(26);
        $user->syncPermissions($role->permissions->pluck('name'));

        /*
        $service = new TrackingService();

        $trackings = DB::select("select tracking_code, product_plan_sale_id
                                          from trackings
                                          where tracking_code in (
                                              select tracking_code
                                              from trackings
                                              where system_status_enum = 5
                                          )");

        $bar = $this->output->createProgressBar(count($trackings));
        $bar->start();

        foreach ($trackings as $t) {
            $service->createOrUpdateTracking($t->tracking_code, $t->product_plan_sale_id, false, false);
            $bar->advance();
        }

        $bar->finish();*/
    }
}


