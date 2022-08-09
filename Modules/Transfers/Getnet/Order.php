<?php

namespace Modules\Transfers\Getnet;

use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;

class Order implements JsonSerializable, Arrayable
{
    protected ?int $saleId = null;
    protected ?string $hashId = null;
    protected ?string $orderId = null;

    public function jsonSerialize()
    {
        return $this->toArray();
    }

    public function toArray()
    {
        return [
            "saleId" => $this->saleId ? $this->getSaleId() : null,
            "hashId" => $this->hashId ? $this->getHashId() : null,
            "orderId" => $this->orderId ? $this->getOrderId() : null,
        ];
    }

    /**
     * @return int|null
     */
    public function getSaleId(): ?int
    {
        return $this->saleId;
    }

    /**
     * @param int|null $saleId
     * @return Order
     */
    public function setSaleId(?int $saleId): Order
    {
        $this->saleId = $saleId;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getHashId(): ?string
    {
        return $this->hashId;
    }

    /**
     * @param string|null $hashId
     * @return Order
     */
    public function setHashId(?string $hashId): Order
    {
        $this->hashId = $hashId;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getOrderId(): ?string
    {
        return $this->orderId;
    }

    /**
     * @param string|null $orderId
     * @return Order
     */
    public function setOrderId(?string $orderId): Order
    {
        $this->orderId = $orderId;
        return $this;
    }
}
