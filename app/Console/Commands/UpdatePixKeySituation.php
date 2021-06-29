<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\PixTransfer;
use Modules\Core\Entities\Withdrawal;
use Modules\Core\Services\PixService;

class UpdatePixKeySituation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'change:fixpixsituation';

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
        $companies_has_validated_key = Withdrawal::join('pix_transfers', 'pix_transfers.withdrawal_id', 'withdrawals.id')
            ->groupBy('company_id')->pluck('company_id');

        $companies = Company::whereIn('id', $companies_has_validated_key)->get();

        foreach($companies as $company) {
            $company->pix_key_situation = 'VERIFIED';
            $company->save();
        }
    }

}
