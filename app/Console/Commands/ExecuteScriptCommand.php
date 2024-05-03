<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Core\Services\FoxUtils;

class ExecuteScriptCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'comand:ExecuteScriptCommand';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scritps de execução de comandos do sistema';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $var = FoxUtils::xorEncrypt("", "decrypt");

        var_dump($var);
    }
}
