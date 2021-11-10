<?php

namespace App\Console\Commands;

use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Console\Command;
use Modules\Core\Entities\User;

class GenericCommand extends Command
{
    protected $signature = 'generic';

    protected $description = 'Command description';

    public function handle()
    {
        $userExist = User::whereHas(
            'roles',
            function ($query) {
                $query->whereIn('name', ['admin']);
            }
        )
        ->where('email', 'luccas332@gmail.com')
        ->orWhere(function($qr){
            $qr->where('email', 'assdds')
            ->whereNull('account_owner_id');
        })->exists();

            dd($userExist);
    }

}
