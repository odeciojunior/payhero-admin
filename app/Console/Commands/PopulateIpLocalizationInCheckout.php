<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Modules\Core\Entities\Checkout;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;

class PopulateIpLocalizationInCheckout extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:populate-iplocalization';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
     * @return int
     */
    public function handle()
    {
        try {
            $checkouts = Checkout::get();

            $output = new ConsoleOutput();
            $progress = new ProgressBar($output, count($checkouts));
            $progress->start();

            foreach($checkouts as $checkout) {
                $progress->advance();

                $localization = json_decode(getRegionByIp($checkout->ip));

                $checkout->update([
                    'ip_localization' => $localization,
                    'ip_state' => $localization->state
                ]);
            }

            $this->line($progress);

            $progress->finish();
        } catch(Exception $e) {
            report($e);
        }
    }
}
