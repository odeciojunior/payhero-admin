<?php

namespace Modules\Core\Services\Gateways;

use Modules\Core\Entities\BlockReasonSale;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Gateway;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\Transaction;
use Modules\Core\Interfaces\Statement;

class GetnetService implements Statement
{
    public Company $company;
    public $gatewayIds = [];

    public function __construct()
    {
        $this->gatewayIds = [ 
            Gateway::GETNET_PRODUCTION_ID, 
            Gateway::GETNET_SANDBOX_ID 
        ];
    }

    public function setCompany(Company $company)
    {
        $this->company = $company;
    }

    public function getAvailableBalance() : int
    {
        return Transaction::whereIn('gateway_id', $this->gatewayIds)
                            ->where('company_id', $this->company->id)
                            ->where('is_waiting_withdrawal', 1)
                            ->whereNull('withdrawal_id')
                            ->sum('value');
    }

    public function getPendingBalance() : int
    {
        return Transaction::where('company_id', $this->company->id)
                            ->where('status_enum', Transaction::STATUS_PAID)
                            ->whereIn('gateway_id', $this->gatewayIds)
                            ->where('is_waiting_withdrawal', 0)
                            ->whereNull('withdrawal_id')
                            ->sum('value');
    }

    public function getBlockedBalance() : int
    {
        return Transaction::where('company_id', $this->company->id)
                            ->whereIn('gateway_id', $this->gatewayIds)
                            ->where('status_enum', Transaction::STATUS_TRANSFERRED)
                            ->whereHas('blockReasonSale',function ($query) {
                                    $query->where('status', BlockReasonSale::STATUS_BLOCKED);
                            })
                            ->sum('value');
    }

    public function getBlockedBalancePending() : int
    {
        return Transaction::where('company_id', $this->company->id)
                            ->whereNull('invitation_id')
                            ->whereIn('gateway_id', $this->gatewayIds)
                            ->where('status_enum', Transaction::STATUS_PAID)
                            ->whereHas('blockReasonSale',function ($query) {
                                    $query->where('status', BlockReasonSale::STATUS_BLOCKED);
                            })
                            ->sum('value');
    }

    public function hasEnoughBalanceToRefund(Sale $sale): bool
    {
        return false;
    }

    public function getStatement()
    {

    }
}
