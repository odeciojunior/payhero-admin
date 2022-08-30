<?php

namespace Modules\Core\Services\Api;

use Carbon\Carbon;
use Exception;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Customer;
use Modules\Core\Entities\Transaction;

class SaleApiService
{
    public function getPaginatedSales($filters)
    {
        $transactions = $this->getSalesQueryBuilder($filters);

        return $transactions->paginate(10);
    }

    public function getSalesQueryBuilder($filters, $withProducts = false, $userId = 0, $withCashback = false)
    {
        try {

            $userId = request()->user_id;

            if (empty($filters["company"])) {
                $userCompanies = Company::where("user_id", $userId)
                    ->get()
                    ->pluck("id")
                    ->toArray();
            } else {
                $userCompanies = [];
                $companies = explode(",", $filters["company"]);

                foreach ($companies as $company) {
                    array_push($userCompanies, hashids_decode($company));
                }
            }

            $relationsArray = [
                "sale",
                "sale.project",
                "sale.customer",
                "sale.plansSales",
                "sale.shipping",
                "sale.checkout",
                "sale.delivery",
                "sale.affiliate.user",
                "sale.saleRefundHistory",
                "sale.cashback",
            ];

            if ($withProducts) {
                $relationsArray[] = "sale.productsPlansSale.plan";
                $relationsArray[] = "sale.productsPlansSale.product";
            }

            $transactions = Transaction::with($relationsArray)
                ->whereIn("company_id", $userCompanies)
                ->join("sales", "sales.id", "transactions.sale_id")
                ->whereNull("invitation_id");

            if (!empty($filters["project"])) {
                $projectIds = [];
                $projects = explode(",", $filters["project"]);

                foreach ($projects as $project) {
                    array_push($projectIds, hashids_decode($project));
                }

                //$projectId = hashids_decode($filters["project"]);
                $transactions->whereHas("sale", function ($querySale) use ($projectIds) {
                    $querySale->whereIn("project_id", $projectIds);
                });
            }

            if (!empty($filters["transaction"])) {
                $saleId = hashids_decode(str_replace("#", "", $filters["transaction"]), "sale_id");

                $transactions->whereHas("sale", function ($querySale) use ($saleId) {
                    $querySale->where("id", $saleId);
                });
            }

            if (!empty($filters["client"])) {// && empty($filters["email_client"])
                $customers = Customer::where("name", "LIKE", "%" . $filters["client"] . "%")->pluck("id");
                $transactions->whereHas("sale", function ($querySale) use ($customers) {
                    $querySale->whereIn("customer_id", $customers);
                });
            }

            if (!empty($filters["customer_document"])) {
                $customers = Customer::where("document", foxutils()->onlyNumbers($filters["customer_document"]))->pluck("id");

                if (count($customers) < 1) {
                    $customers = Customer::where("document", $filters["customer_document"])->pluck("id");
                }

                $transactions->whereHas("sale", function ($querySale) use ($customers) {
                    $querySale->whereIn("customer_id", $customers);
                });
            }

            // novo filtro
            if (!empty($filters["coupon"])) {
                $couponCode = $filters["coupon"];

                $transactions->whereHas("sale", function ($querySale) use ($couponCode) {
                    $querySale->where("cupom_code", "LIKE", "%" . $couponCode . "%");
                });
            }

            // novo filtro
            if (!empty($filters["value"])) {
                $value = $filters["value"];

                $transactions->where("value", $value);
            }

            if (!empty($filters["shopify_error"]) && $filters["shopify_error"] == true) {
                $transactions->whereHas("sale.project.shopifyIntegrations", function ($queryShopifyIntegration) {
                    $queryShopifyIntegration->where("status", 2);
                });
                $transactions->whereHas("sale", function ($querySaleShopify) {
                    $querySaleShopify->whereNull("shopify_order")->where(
                        "start_date",
                        "<=",
                        Carbon::now()
                            ->subMinutes(5)
                            ->toDateTimeString()
                    );
                });
            }
            if (!empty($filters["payment_method"])) {
                $forma = $filters["payment_method"];
                $transactions->whereHas("sale", function ($querySale) use ($forma) {
                    $querySale->whereIn("payment_method", explode(",", $forma));
                });
            }

            if (!empty($filters["plan"])) {
                $planIds = [];
                $plans = explode(",", $filters["plan"]);

                foreach ($plans as $plan) {
                    array_push($planIds, hashids_decode($plan));
                }
                // $planId = hashids_decode($filters["plan"]);

                $transactions->whereHas("sale.plansSales", function ($query) use ($planIds) {
                    $query->whereIn("plan_id", $planIds);
                });
            }

            if (empty($filters["status"])) {
                $status = [1, 2, 4, 7, 8, 12, 20, 21, 22, 24];
            } else {
                $status = explode(",", $filters["status"]);
                $status = in_array(7, $status) ? array_merge($status, [22]) : $status;
            }

            if (!empty($status)) {
                $transactions->whereHas("sale", function ($querySale) use ($status) {
                    $querySale->whereIn("status", $status);
                });
            }
            if (!empty($filters["upsell"]) && $filters["upsell"] == true) {
                $transactions->whereHas("sale", function ($querySale) {
                    $querySale->whereNotNull("upsell_id");
                });
            }

            if (!empty($filters["cashback"])) {
                $transactions->whereHas("sale", function ($querySale) {
                    $querySale->whereHas("cashback");
                });
            }

            if (!empty($filters["order_bump"]) && $filters["order_bump"] == true) {
                $transactions->whereHas("sale", function ($querySale) {
                    $querySale->where("has_order_bump", true);
                });
            }

            //tipo da data e periodo obrigatorio
            $dateRange = foxutils()->validateDateRange($filters["date_range"]);
            $dateType = $filters["date_type"];

            $transactions
                ->whereHas("sale", function ($querySale) use ($dateRange, $dateType) {
                    $querySale->whereBetween($dateType, [$dateRange[0] . " 00:00:00", $dateRange[1] . " 23:59:59"]);
                })
                ->selectRaw("transactions.*, sales.start_date")
                ->orderByDesc("sales.start_date");

            return $transactions;

        } catch (Exception $e) {
            report($e);
            return null;
        }
    }

}
