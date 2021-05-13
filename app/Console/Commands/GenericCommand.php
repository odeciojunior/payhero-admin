<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenericCommand extends Command
{
    protected $signature = 'generic {user?}';

    protected $description = 'Command description';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        //
    }
}



