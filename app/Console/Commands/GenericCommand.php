<?php

namespace App\Console\Commands;

use Hashids\Hashids;
use Illuminate\Console\Command;
use Modules\Core\Entities\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class GenericCommand extends Command
{
    protected $signature = 'generic';

    protected $description = 'Command description';

    public function handle()
    {
       dd(hashids_encode(8));
    }
}
