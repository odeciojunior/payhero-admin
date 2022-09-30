<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Modules\Core\Services\PixService;

class ChangePixToCanceled extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "change:new-pix-to-canceled";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Command set pix expired";

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        try {
            $pixService = new PixService();
            $pixService->changePixToCanceled();
        } catch (Exception $e) {
            report($e);
        }
    }
}
