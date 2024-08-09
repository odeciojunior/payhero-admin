<?php

namespace App\Console\Commands;
use Illuminate\Console\Command;
use Modules\Core\Services\FoxUtils;

class GenericCommand extends Command
{
    protected $signature = "generic {name?}";
    protected $description = "Command description";
    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $jsonConfig = FoxUtils::xorEncrypt(
            json_encode([
                "client_id" =>'x',
                "client_secret" => 'x',
                "auth_basic" => 'x',
            ])
        );
        $this->line($jsonConfig);
    }
}
