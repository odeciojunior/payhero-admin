<?php

namespace App\Console\Commands\Install;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class EasyInstall extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "install:easy-install";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Command description";

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Artisan::call("migrate:fresh", [], new \Symfony\Component\Console\Output\ConsoleOutput());

        Artisan::call("db:seed", [], new \Symfony\Component\Console\Output\ConsoleOutput());

        Artisan::call(
            "passport:client",
            ["--personal" => true, "--name" => "Laravel Personal Access Client"],
            new \Symfony\Component\Console\Output\ConsoleOutput()
        );

        $database = env("DB_DATABASE");
        $this->changeDatabaseEnv($database . "_demo");

        // Config::clearResolvedInstances();
        // Artisan::call("config:cache");

        DB::reconnect("mysql");

        Artisan::call("migrate:fresh", ["--seed" => true], new \Symfony\Component\Console\Output\ConsoleOutput());

        Artisan::call("passport:client", ["--personal" => true], new \Symfony\Component\Console\Output\ConsoleOutput());

        $this->changeDatabaseEnv($database);

        $this->line("finalizando");
    }

    private function changeDatabaseEnv($newDbDatabase)
    {
        $lines = file(base_path(".env"));

        foreach ($lines as $key => $line) {
            if (str_starts_with($line, "DB_DATABASE=")) {
                $lines[$key] = "DB_DATABASE={$newDbDatabase}\n";
                break;
            }
        }
        File::put(base_path(".env"), implode("", $lines));
    }
}
