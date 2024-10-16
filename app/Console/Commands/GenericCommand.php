<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenericCommand extends Command
{
    protected $signature = "generic";
    protected $description = "Get encrypted config";
    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
    }
}
