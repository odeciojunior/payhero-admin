<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
class GenericCommand extends Command
{
    protected $signature = 'generic';

    protected $description = 'Command description';

    public function handle()
    {
        dd(number_format(450020 * 100 / 500000, 1, '.', ''));
    }
}
