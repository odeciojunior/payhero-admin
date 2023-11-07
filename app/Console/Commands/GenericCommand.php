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
                "secret_key" => "sk_live_DyuA6lfOqWjR3M5oXp0lWfnpd5sY63tIdkOtPWxh5I",
                "public_key" => "pk_live_ssauH5lLXkTFvxSFzzPUsVYBTdxmjv",
            ])
        );
        dd($jsonConfig);
    }
}
