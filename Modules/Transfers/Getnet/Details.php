<?php

namespace Modules\Transfers\Getnet;

use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;

class Details implements JsonSerializable, Arrayable
{
    const STATUS_WAITING_FOR_VALID_POST = "WAITING_FOR_VALID_POST";
    const STATUS_WAITING_LIQUIDATION = "WAITING_LIQUIDATION";
    const STATUS_WAITING_WITHDRAWAL = "WAITING_WITHDRAWAL";
    const STATUS_WAITING_RELEASE = "WAITING_RELEASE";
    const STATUS_PAID = "PAID";
    const STATUS_REVERSED = "REVERSED";
    const STATUS_ADJUSTMENT_CREDIT = "ADJUSTMENT_CREDIT";
    const STATUS_ADJUSTMENT_DEBIT = "ADJUSTMENT_DEBIT";
    const STATUS_ERROR = "ERROR";
    protected string $status, $description, $type;

    /**
     * @param string $description
     * @return Details
     */
    public function setDescription(string $description): Details
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @param string $type
     * @return Details
     */
    public function setType(string $type): Details
    {
        $this->type = $type;
        return $this;
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }

    public function toArray()
    {
        return [
            "status" => $this->getStatus(),
            "description" => $this->getDescription(),
            "type" => $this->getType(),
        ];
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param string $status
     * @return Details
     */
    public function setStatus(string $status): Details
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }
}
