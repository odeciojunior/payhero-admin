<?php

namespace App\Console\Commands;
use Illuminate\Console\Command;
use Modules\Core\Services\FoxUtils;

class GenericCommand extends Command
{
    protected $signature = "generic:get-encrypted-config {client_id} {client_secret} {auth_basic}";
    protected $description = "Get encrypted config";
    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $jsonConfig = FoxUtils::xorEncrypt(
            json_encode([
                "client_id" => $this->argument('client_id'),
                "client_secret" => $this->argument('client_secret'),
                "auth_basic" => $this->argument('auth_basic') 
            ])
        );
        $this->line($jsonConfig);
    }
}
