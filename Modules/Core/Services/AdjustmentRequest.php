<?php

namespace Modules\Core\Services;

use Carbon\Carbon;
use LogicException;

class AdjustmentRequest
{
    public const CREDIT_ADJUSTMENT = 1;
    public const DEBIT_ADJUSTMENT = 2;

    protected string $sellerId, $merchantId, $description;
    protected int $subSellerId, $typeAdjustment, $amount, $companyId, $saleId;
    protected Carbon $dateAdjustment;

    public function setMerchantId(string $merchantId): AdjustmentRequest
    {
        $this->merchantId = $merchantId;
        return $this;
    }

    public function setAmount(int $amount): AdjustmentRequest
    {
        $this->amount = $amount;
        return $this;
    }

    public function setSaleId(int $saleId): AdjustmentRequest
    {
        $this->saleId = $saleId;
        return $this;
    }

    public function setCompanyId(int $companyId): AdjustmentRequest
    {
        $this->companyId = $companyId;
        return $this;
    }

    public function setDescription(string $description): AdjustmentRequest
    {
        $this->description = $description;
        return $this;
    }

    public function setSellerId(string $sellerId): AdjustmentRequest
    {
        $this->sellerId = $sellerId;
        return $this;
    }

    public function setSubSellerId(int $subSellerId): AdjustmentRequest
    {
        $this->subSellerId = $subSellerId;
        return $this;
    }

    public function setTypeAdjustment(int $typeAdjustment): AdjustmentRequest
    {
        if (!in_array($typeAdjustment, [self::CREDIT_ADJUSTMENT, self::DEBIT_ADJUSTMENT])) {
            throw new LogicException("Valor inválido para setTypeAdjustment(" . $typeAdjustment . ")");
        }

        $this->typeAdjustment = $typeAdjustment;
        return $this;
    }

    public function isValid(): bool
    {
        if (
            isset($this->sellerId) &&
            isset($this->merchantId) &&
            isset($this->description) &&
            isset($this->subSellerId) &&
            isset($this->typeAdjustment) &&
            isset($this->amount)
        ) {
            return true;
        }
        return false;
    }

    public function formatToSendApi(): array
    {
        return [
            "seller_id" => $this->getSellerId(),
            "merchant_id" => $this->getMerchantId(),
            "subseller_id" => $this->getSubSellerId(),
            "type_adjustment" => $this->getTypeAdjustment(),
            "amount" => $this->getAmount(),
            "date_adjustment" =>
                today()
                    ->addDay()
                    ->format("Y-m-d\TH:i:s") . "Z", //2020-11-26T13:58:17Z
            "description" => $this->getDescription(),
        ];
    }

    public function getSellerId(): string
    {
        return $this->sellerId;
    }

    public function getMerchantId(): string
    {
        return $this->merchantId;
    }

    public function getSubSellerId(): int
    {
        return $this->subSellerId;
    }

    public function getTypeAdjustment(): int
    {
        return $this->typeAdjustment;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getSaleId(): ?int
    {
        return $this->saleId ?? null;
    }

    public function getCompanyId(): int
    {
        return $this->companyId;
    }
}
