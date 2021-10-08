<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenericCommand extends Command
{
    protected $signature = 'generic';

    protected $description = 'Command description';

    public function handle()
    {
        $prefix = 'laravel';
        dd(preg_replace("/{$prefix}:/", '', 'laravel:sua-mae'));
    }
}
