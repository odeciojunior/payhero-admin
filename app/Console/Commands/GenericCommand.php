<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Core\Services\FoxUtils;

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
        dd(
            FoxUtils::xorEncrypt(
                json_encode([
                    "api_key" => "1736|eXAeMSylNF7QsbdNq7wYMtwZ7KMmX4pMKVW7zqvW79c796e9",
                ])
            )
        );
    }
}
