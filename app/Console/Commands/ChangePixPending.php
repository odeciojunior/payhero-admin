<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Core\Services\PixService;

class ChangePixPending extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'change:pixpendingtocanceled';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command set pix expired';

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
        $pixService = new PixService();
        $pixService->changePixPending();
        dd('feitoo');
    }

}
