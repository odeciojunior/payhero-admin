<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\Gateway;
use Modules\Core\Entities\SecurityReserve;
use Modules\Core\Entities\Transfer;

class ReleaseSecurityReserve extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "release-security-reserve";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Command description";

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            DB::beginTransaction();

            $securityReserves = SecurityReserve::with("company")
                ->where("status", SecurityReserve::STATUS_PENDING)
                ->where("release_date", "<=", Carbon::now()->format("Y-m-d"))
                ->get();

            foreach ($securityReserves as $securityReserve) {
                $securityReserve->update([
                    "status" => SecurityReserve::STATUS_TRANSFERRED,
                ]);

                $company = $securityReserve->company;

                $company->update([
                    "vega_balance" => $company->vega_balance + $securityReserve->value,
                ]);

                Transfer::create([
                    "transaction_id" => $securityReserve->transaction_id,
                    "user_id" => $securityReserve->user_id,
                    "company_id" => $company->id,
                    "type_enum" => Transfer::TYPE_IN,
                    "value" => $securityReserve->value,
                    "type" => Transfer::TYPE_IN,
                    "reason" => "Liberação de reserva de segurança",
                    "gateway_id" => foxutils()->isProduction()
                        ? Gateway::SAFE2PAY_PRODUCTION_ID
                        : Gateway::SAFE2PAY_SANDBOX_ID,
                ]);
            }
            DB::commit();
        } catch (Exception $e) {
            report($e);
            DB::rollBack();
        }
    }
}
