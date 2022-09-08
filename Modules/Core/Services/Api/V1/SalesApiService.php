<?php

namespace Modules\Core\Services\Api\V1;

use Illuminate\Support\Facades\Storage;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\Transaction;
use Modules\Core\Entities\User;
use Vinkla\Hashids\Facades\Hashids;

class SalesApiService
{
    public static function getSalesQueryBuilder($data)
    {
        $saleModel = new Sale();
        $transactionModel = new Transaction();
        $transactions = $transactionModel->join("sales", "sales.id", "transactions.sale_id");

        if (!empty($data["transaction"])) {
            $transaction_id = current(Hashids::connection('sale_id')->decode($data["transaction"]));

            $transactions->where("sales.id", $transaction_id);
        }

        if (!empty($data["company"])) {
            $companies = Hashids::decode($data["company"]);
        } else {
            $companies = Company::where("user_id", request()->user_id)->pluck("id")->toArray();
        }

        $transactions->whereIn("transactions.company_id", $companies);

        if (!empty($data["user"])) {
            $user_id = current(Hashids::decode($data["user"]));
            $subsellers = User::where("subseller_owner_id", request()->user_id)->where("id", $user_id)->pluck("id")->toArray();
        } else {
            $subsellers = User::where("subseller_owner_id", request()->user_id)->pluck("id")->toArray();
            array_push($subsellers, request()->user_id);
        }

        $transactions->whereIn("sales.owner_id", $subsellers);

        if (!empty($data["status"])) {
            $transactions->where("sales.status", $saleModel->present()->getStatus($data["status"]));
        } else {
            $transactions->whereIn("sales.status", [
                Sale::STATUS_APPROVED,
                Sale::STATUS_PENDING,
                Sale::STATUS_CHARGEBACK,
                Sale::STATUS_REFUNDED,
                Sale::STATUS_PARTIAL_REFUNDED,
                Sale::STATUS_IN_REVIEW,
                Sale::STATUS_CANCELED_ANTIFRAUD,
                Sale::STATUS_IN_DISPUTE,
            ]);
        }

        if (!empty($data['date_type']) && !empty($data['date_range'])) {
            $dateType = $data["date_type"];
            $dateRange = foxutils()->validateDateRange($data["date_range"]);

            $transactions->whereBetween("sales.".$dateType, [
                $dateRange[0] . " 00:00:00",
                $dateRange[1] . " 23:59:59",
            ]);
        }

        $transactions->orderByDesc("sales.start_date");

        return $transactions;
    }
}
