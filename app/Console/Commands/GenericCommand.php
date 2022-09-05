<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\User;


class GenericCommand extends Command
{
    protected $signature = "generic";
    protected $description = "Command description";

    public function handle()
    {

        try {

            //$users = User::withTrashed();

            $bar = $this->output->createProgressBar(User::count());
            //$bar = $this->output->createProgressBar($users->count());
            $bar->start();

            $documentArray = [];
            $documentDoubleArray = [];
            foreach( User::cursor() as $user ){
                $bar->advance();

                if(foxutils()->isEmpty($user->document)) continue;
                if($user->document == '00000000000') continue;

                if( !in_array($user->document, $documentArray) ){
                    $documentArray[] = $user->document;
                } else {

                    if( !in_array($user->document, $documentDoubleArray) ){
                        $usersDoubles = User::where('document',  $user->document)->get();
                        foreach($usersDoubles as $userDouble) {
                            Log::info($userDouble->document . "  -  " . $userDouble->id . "  -  " . $userDouble->name);
                        }
                    }

                    $documentDoubleArray[] = $user->document;
                    //Log::info($user->id . "  -  " . $user->name . "  -  " . $user->document);
                }

            }
            $bar->finish();
        } catch(Exception $e) {
            report($e->getMessage());
        }

    }

}
