<?php

namespace Modules\Core\Interfaces;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Sale;

interface Statement
{
    public function setCompany(Company $company);

    public function getAvailableBalance(): int;

    public function getPendingBalance(): int;

    public function getBlockedBalance(): int;

    public function getPendingDebtBalance(): int;

    public function hasEnoughBalanceToRefund(Sale $sale): bool;

    public function getWithdrawals(): JsonResource;

    public function createWithdrawal($value);

    public function getGatewayId(): int;

    public function refundEnabled(): bool;

    public function canRefund(Sale $sale): bool;
}
