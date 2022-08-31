<?php

namespace App\Console\Commands;

use App\Jobs\ProcessTrackingJob;
use Exception;
use Illuminate\Console\Command;
use Modules\Core\Entities\Tracking;
use Modules\Core\Entities\User;
use Spatie\Permission\Models\Role;

class GenericCommand extends Command
{
    protected $signature = "generic";
    protected $description = "Command description";

    public function handle()
    {
        dd(User::find(3520)->roles()->where('guard_name','web')->get()->detach());
    }

}
