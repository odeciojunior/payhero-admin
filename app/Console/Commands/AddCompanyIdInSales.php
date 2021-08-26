<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\UserProject;

class AddCompanyIdInSales extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sale:add_company';

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
        foreach(Sale::all() as $sale) {
            $userProject = UserProject::where('project_id', $sale->project_id)->first();

            $sale->update(['company_id' => $userProject->company_id]);

            $this->line('id: '.$sale->id);
        }
    }
}
