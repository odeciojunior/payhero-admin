<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Core\Entities\UserTerms;

class insertColumnAcceptedAt extends Command
{
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'insertAcceptedat';
    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     * @return mixed
     */
    public function handle()
    {

        foreach (UserTerms::cursor() as $item) {
            $item->update([
                              'accepted_at_terms' => $item->created_at,
                          ]);
        }
        dd('Terminou');
    }
}
