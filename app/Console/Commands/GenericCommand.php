<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GenericCommand extends Command
{
    protected $signature = 'generic';
    protected $description = 'Command description';

    public $shopifyService;

    public function handle()
    {

    }
}
