<?php

namespace Modules\Core\Services;

use Modules\Core\Entities\Transfer;
use Modules\Transfers\Transformers\TransfersResource;

class StatementService
{
    public function getDefaultStatement($companyId, $gatewayIds, $filters)
    {
        $transfers = $this->queryBuilderFilters($companyId, $gatewayIds, $filters);

        $balanceInPeriod = $transfers
            ->selectRaw(
                "sum(CASE WHEN transfers.type_enum = 2 THEN (transfers.value * -1) ELSE transfers.value END) as balanceInPeriod"
            )
            ->first();

        if (!empty($balanceInPeriod)) {
            $balanceInPeriod = $balanceInPeriod->balanceInPeriod / 100;
            $balanceInPeriod = number_format($balanceInPeriod, 2, ",", ".");
        }
        $transfers = $transfers->whereNull("transfers.customer_id");

        $transfers = $transfers
            ->select("transfers.*", "transaction.sale_id", "transaction.type as transaction_type")
            ->orderBy("id", "DESC")
            ->paginate(10);

        $statement = TransfersResource::collection($transfers);

        $statement->additional([
            "meta" => [
                "balance_in_period" => $balanceInPeriod,
            ],
        ]);

        return $statement;
    }

    public function getPeriodBalance($companyId, $gatewayIds, $filters)
    {
        $transfers = $this->queryBuilderFilters($companyId, $gatewayIds, $filters);
        $balanceInPeriod = $transfers
            ->selectRaw(
                "sum(CASE WHEN transfers.type_enum = 2 THEN (transfers.value * -1) ELSE transfers.value END) as balanceInPeriod"
            )
            ->first();

        $total = 0;
        if (!empty($balanceInPeriod)) {
            $total = $balanceInPeriod->balanceInPeriod / 100;
        }
        return $total;
    }

    public function queryBuilderFilters($companyId, $gatewayIds, $filters)
    {
        $transfers = Transfer::leftJoin("transactions as transaction", "transaction.id", "transfers.transaction_id")
            // ->whereIn("transfers.gateway_id", $gatewayIds)
            ->where("transfers.company_id", $companyId);

        $saleId = str_replace("#", "", $filters["transaction"]);
        $saleId = hashids_decode($saleId, "sale_id");

        if ($saleId) {
            $transfers = $transfers->where(function ($q) use ($saleId) {
                $q->where("transaction.sale_id", $saleId)->orWhere("transfers.anticipation_id", $saleId);
            });
        }

        if (empty($saleId)) {
            $dateRange = foxutils()->validateDateRange($filters["date_range"]);
            if ($filters["date_type"] == "transaction_date") {
                $dateType = "transaction.created_at";
            } elseif ($filters["date_type"] == "transfer_date") {
                $dateType = "transfers.created_at";
            } else {
                $dateType = "sales.start_date";
                $transfers = $transfers->join("sales", "sales.id", "=", "transaction.sale_id");
            }
            $transfers = $transfers->whereBetween($dateType, [
                $dateRange[0] . " 00:00:00",
                $dateRange[1] . " 23:59:59",
            ]);
        }

        if (!empty($filters["type"])) {
            $transfers->where("transfers.type_enum", $filters["type"]);
        }

        if (!empty($filters["reason"])) {
            if (strtolower($filters["reason"]) == "chargeback") {
                $filters["reason"] = "chargedback";
            }
            $transfers->where("transfers.reason", "like", "%" . $filters["reason"] . "%");
        }

        if (!empty($filters["value"])) {
            $value = foxutils()->onlyNumbers($filters["value"]);
            $transfers->where("transfers.value", $value);
        }

        return $transfers;
    }
}
