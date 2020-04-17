<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Core\Entities\Pixel;

class pixelPlansCommand extends Command
{
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'pixelplans';
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
        foreach (Pixel::cursor() as $pixel) {
            $pixel->update([
                               'apply_on_plans' => json_encode(["all"]),
                           ]);
        }
        dd('terminou');
    }
}
