<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Core\Entities\User;
use Modules\Core\Services\AccountHealthService;
use phpDocumentor\Reflection\DocBlock\Tags\Link;

class UpdateUserAccountHealth extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'account-health:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command updates user\'s account health stats';

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
        $accountHealthService = new AccountHealthService();
        $output = [];
        foreach ([26, 557, 577] as $id) {
            $output[] = $accountHealthService->testTrackingScore(User::find($id));
        }
        dd($output);

        for ($uninformedRate = 0; $uninformedRate <= 13; $uninformedRate++) {
            $maxScore = 10;
            if ($uninformedRate <= 3) {
                $score = $maxScore;
            } else {
                //after 3% every 1% of uninformed rate means -1 point of score
                $score = $maxScore + 3 - $uninformedRate;
            }
            $this->line($uninformedRate . ' - ' . $score);
        }

        dd('aqui');

        $user = User::find(577); // Carolina
        //$user = User::find(26); // Joao
        $accountHealthService->updateAccountScore($user);
        return 0;
    }
}
