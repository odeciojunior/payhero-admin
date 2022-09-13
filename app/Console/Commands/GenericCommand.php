<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\User;
use Spatie\Permission\Models\Role;

class GenericCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "generic {name?}";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Command description";

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $users = DB::select("SELECT ");
        foreach ($users as $user) {
            \Log::info($user->id);
            DB::statement("INSERT INTO model_has_permissions VALUES(31,'Modules\\Core\\Entities\\User',$user->id);");
            DB::statement("INSERT INTO model_has_permissions VALUES(32,'Modules\\Core\\Entities\\User',$user->id);");
        }
    }
}
