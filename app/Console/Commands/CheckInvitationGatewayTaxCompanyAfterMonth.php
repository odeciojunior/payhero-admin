<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

/**
 * Class checkInvitationGatewayTaxCompanyAfterMonth
 * @package App\Console\Commands
 */
class CheckInvitationGatewayTaxCompanyAfterMonth extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:GatewayTaxCompanyAfterMonth';

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
//        $companies = Company::where('gateway_tax' , 3.9)->get();
//
//        foreach ($companies as $company) {
//            if(Carbon::now()->gt(Carbon::parse($company->created_at)->addMonth())) {
//                $company->update(
//                    [
//                        'gateway_tax' => Company::GATEWAY_TAX
//                    ]
//                );
//            }
//        }
//
//        return 0;
    }
}
