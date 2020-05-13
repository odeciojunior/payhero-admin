<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Modules\Core\Entities\User;

class DisableUserAntecipation extends Command
{
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'command:DisableUserAntecipation';
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
        try {
            $userModel = new User();
            $startDate = Carbon::now()->subDays(3)->toDateString();
            $endDate   = Carbon::now()->toDateString();
            $users     = $userModel->where('antecipation_enabled_flag', true)
                                   ->whereDoesntHave('sales', function($query) use ($startDate, $endDate) {
                                       $query->where('status', 1)->whereBetween('start_date', [$startDate, $endDate]);
                                   })->get();
            foreach ($users as $user) {
                $user->update(['antecipation_enabled_flag' => false]);
            }
        } catch (Exception $e) {
            report($e);
        }
    }
}
