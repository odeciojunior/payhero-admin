<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Invitation;
use Modules\Core\Entities\PromotionalTax;
use Modules\Core\Services\CompanyService;

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
    protected $signature = 'check:gateway-tax-company-after-month';

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
        $invitesDiogo = Invitation::where(
            [
                ['invite', 177],
                ['created_at', '>', '2021-04-14']
            ]
        )->get();

        foreach ($invitesDiogo as $invite) {
            $create = Carbon::parse($invite->created_at)->addMonth();

            if(Carbon::now()->gt($create)) {
                $company = Company::where([
                    'user_id' => $invite->user_invited,
                    'gateway_tax' => PromotionalTax::PROMOTIONAL_TAX
                ])->first();

                if (!empty($company)) {
                    $company->update(['gateway_tax', (new CompanyService())->getTax($company->gateway_release_money_days)]);
                }
            }
        }

        return 0;
    }
}
