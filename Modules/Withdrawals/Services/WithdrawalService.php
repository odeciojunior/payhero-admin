<?php

namespace Modules\Withdrawals\Services;

use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Gateway;
use Modules\Core\Entities\Transaction;
use Modules\Core\Entities\Withdrawal;

class WithdrawalService
{
    public function isFirstUserWithdrawal($userId): bool
    {
        $withdrawalStatus = [
            Withdrawal::STATUS_IN_REVIEW,
            Withdrawal::STATUS_LIQUIDATING,
            Withdrawal::STATUS_PARTIALLY_LIQUIDATED,
            Withdrawal::STATUS_TRANSFERRED,
        ];

        $isFirstUserWithdrawal = false;
        $userWithdrawal = Withdrawal::whereHas("company", function ($query) use ($userId) {
            $query->where("user_id", $userId);
        })
            ->whereIn("status", $withdrawalStatus)
            ->exists();

        if (!$userWithdrawal) {
            $isFirstUserWithdrawal = true;
        }

        return $isFirstUserWithdrawal;
    }

    public function companyCanWithdraw($companyId, $gatewayId)
    {
        return Withdrawal::where("company_id", $companyId)
            ->where("gateway_id", $gatewayId)
            ->whereDate("created_at", now())
            ->count() < 10;
    }
}
