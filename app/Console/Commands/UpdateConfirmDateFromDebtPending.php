<?php

namespace App\Console\Commands;

use Exception;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Modules\Core\Entities\PendingDebt;
use Modules\Core\Services\GetnetBackOfficeService;

class UpdateConfirmDateFromDebtPending extends Command
{
    protected $signature = 'getnet:update-confirm-date-debt-pending';

    protected $description = 'Atualiza os debitos pendentes';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {

        try {

            $getnetService = new GetnetBackOfficeService();
            $data = Carbon::createFromFormat('d/m/Y', '01/01/2021');

            while ($data->lessThan(Carbon::now())) {
                $pendingDebts = PendingDebt::whereNull('confirm_date')->get();
                $response = $getnetService
                    ->setStatementStartDate($data)
                    ->setStatementEndDate($data->addDays(29))
                    ->setStatementDateField('schedule')
                    ->getStatement();
                $gatewaySale = json_decode($response);

                if (isset($gatewaySale->adjustments) && count($gatewaySale->adjustments) > 0) {
                    foreach ($gatewaySale->adjustments as $adjustment) {
                        foreach ($pendingDebts as $pendingDebt) {
                            if (
                                $adjustment->cnpj_marketplace != $adjustment->cpfcnpj_subseller &&
                                $pendingDebt->reason == $adjustment->adjustment_reason &&
                                $pendingDebt->value == $adjustment->adjustment_amount &&
                                !is_null($adjustment->subseller_rate_confirm_date)
                            ) {
                                $pendingDebt->update([
                                                         'confirm_date' => Carbon::parse($adjustment->subseller_rate_confirm_date)->format('Y-m-d')
                                                     ]);
                            }
                        }
                    }
                }
            }

        } catch (Exception $e) {
            report($e);
        }

    }
}
