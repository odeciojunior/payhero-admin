<?php

namespace Modules\Core\Interfaces;

use Modules\Core\Entities\Company;
use Modules\Core\Entities\Sale;

interface Statement
{
    public function setCompany(Company $company);

    public function getAvailableBalance() : int;

    public function getPendingBalance() : int;

    public function getBlockedBalance() : int;

    public function getBlockedBalancePending() : int;

    //public function getPendingDebtBalance() : int;

    public function hasEnoughBalanceToRefund(Sale $sale): bool;
}
