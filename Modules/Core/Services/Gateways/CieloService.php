<?php

namespace Modules\Core\Services\Gateways;

use Modules\Core\Entities\BlockReasonSale;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Gateway;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\Transaction;
use Modules\Core\Interfaces\Statement;

class CieloService implements Statement
{

    public Company $company;
    public $gatewayIds = [];

    public function __construct()
    {
        $this->gatewayIds = [ 
            Gateway::CIELO_PRODUCTION_ID, 
            Gateway::CIELO_SANDBOX_ID ,
            // extrato cielo engloba as vendas antigas zoop e pagarme
            Gateway::PAGARME_PRODUCTION_ID,
            Gateway::PAGARME_SANDBOX_ID,
            Gateway::ZOOP_PRODUCTION_ID,
            Gateway::ZOOP_SANDBOX_ID
        ];
    }

    public function setCompany(Company $company)
    {
        $this->company = $company;
    }

    public function getAvailableBalance() : int
    {
        if (!$this->company->user->show_old_finances){
            return 0;
        }

        return $this->company->balance;
    }

    public function getPendingBalance() : int
    {
        if (!$this->company->user->show_old_finances){
            return 0;
        }

        return Transaction::where('company_id', $this->company->id)
                            ->where('status_enum', Transaction::STATUS_PAID)
                            ->where(function($query) {
                                $query->whereIn('gateway_id', $this->gatewayIds)
                                    ->orWhere(function($query) {
                                        $query->where('gateway_id', Gateway::ASAAS_PRODUCTION_ID)->where('created_at', '<', '2021-09');
                                    });
                            })
                            ->where('is_waiting_withdrawal', 0)
                            ->whereNull('withdrawal_id')
                            ->sum('value');
    }

    public function getBlockedBalance() : int
    {
        if (!$this->company->user->show_old_finances){
            return 0;
        }

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
        if (!$this->company->user->show_old_finances){
            return 0;
        }

        return Transaction::where('company_id', $this->company->id)
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
