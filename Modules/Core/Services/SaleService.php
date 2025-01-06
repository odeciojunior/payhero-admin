<?php

namespace Modules\Core\Services;

use App\Jobs\WebhookSaleUpdateJob;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\Affiliate;
use Modules\Core\Entities\BlockReasonSale;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Customer;
use Modules\Core\Entities\Gateway;
use Modules\Core\Entities\PendingDebt;
use Modules\Core\Entities\Product;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\SaleInformation;
use Modules\Core\Entities\SaleLog;
use Modules\Core\Entities\SaleRefundHistory;
use Modules\Core\Entities\SaleWhiteBlackListResult;
use Modules\Core\Entities\Transaction;
use Modules\Core\Entities\Transfer;
use Modules\Core\Entities\User;
use Modules\Core\Events\ManualRefundEvent;
use Modules\Core\Events\SaleRefundedEvent;
use Modules\Products\Transformers\ProductsSaleResource;
use Modules\Transfers\Services\GetNetStatementService;
use PDF;
use Vinkla\Hashids\Facades\Hashids;

class SaleService
{
    public function getPaginatedSales($filters)
    {
        $transactions = $this->getSalesQueryBuilder($filters);

        return $transactions->paginate(10);
    }

    public function getSalesQueryBuilder($filters, $withProducts = false, $userId = 0, $withCashback = false)
    {
        try {
            $companyModel = new Company();
            $customerModel = new Customer();
            $transactionModel = new Transaction();

            if (!$userId) {
                $userId = auth()
                    ->user()
                    ->getAccountOwnerId();
            }

            if (empty($filters["company"])) {
                $userCompanies = $companyModel
                    ->where("user_id", $userId)
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
                "sale.apiToken",
            ];

            if ($withProducts) {
                $relationsArray[] = "sale.productsPlansSale.plan";
                $relationsArray[] = "sale.productsPlansSale.product";
                $relationsArray[] = "sale.productsSaleApi";
            }

            $transactions = $transactionModel
                ->with($relationsArray)
                ->whereIn("company_id", $userCompanies)
                ->join("sales", "sales.id", "transactions.sale_id")
                ->whereNull("invitation_id");

            if (!$withCashback) {
                $transactions->where("type", "<>", $transactionModel->present()->getType("cashback"));
            }

            if (!empty($filters["project"])) {
                $projectIds = [];
                $projects = explode(",", $filters["project"]);
                $tokens = [];

                foreach ($projects as $project) {
                    if (str_contains($project, "TOKEN")) {
                        array_push($tokens, hashids_decode(str_replace("TOKEN-", "", $project)));
                        continue;
                    }

                    array_push($projectIds, hashids_decode($project));
                }

                $transactions->whereHas("sale", function ($querySale) use ($projectIds, $tokens) {
                    $querySale->whereIn("project_id", $projectIds)->orWhereIn("api_token_id", $tokens);
                });
            }

            if (!empty($filters["transaction"])) {
                $saleId = hashids_decode(str_replace("#", "", $filters["transaction"]), "sale_id");

                $transactions->whereHas("sale", function ($querySale) use ($saleId) {
                    $querySale->where("id", $saleId);
                });
            }

            if (!empty($filters["client"])) {
                // && empty($filters["email_client"])
                $customers = $customerModel->where("name", "LIKE", "%".$filters["client"]."%")->pluck("id");
                $transactions->whereHas("sale", function ($querySale) use ($customers) {
                    $querySale->whereIn("customer_id", $customers);
                });
            }

            if (!empty($filters["customer_document"])) {
                $customers = $customerModel
                    ->where("document", foxutils()->onlyNumbers($filters["customer_document"]))
                    ->pluck("id");

                if (count($customers) < 1) {
                    $customers = $customerModel->where("document", $filters["customer_document"])->pluck("id");
                }

                $transactions->whereHas("sale", function ($querySale) use ($customers) {
                    $querySale->whereIn("customer_id", $customers);
                });
            }

            // novo filtro
            if (!empty($filters["coupon"])) {
                $couponCode = $filters["coupon"];

                $transactions->whereHas("sale", function ($querySale) use ($couponCode) {
                    $querySale->where("cupom_code", "LIKE", "%".$couponCode."%");
                });
            }

            // novo filtro
            if (!empty($filters["value"])) {
                $value = $filters["value"];

                $transactions->where("value", $value);
            }

            // // novo filtro
            // if (!empty($filters["email_client"]) && empty($filters["client"])) {
            //     $customers = $customerModel->where("email", "LIKE", "%" . $filters["email_client"] . "%")->pluck("id");
            //     $transactions->whereHas("sale", function ($querySale) use ($customers) {
            //         $querySale->whereIn("customer_id", $customers);
            //     });
            // }

            // // novo filtro
            // if (!empty($filters["email_client"]) && !empty($filters["client"])) {
            //     $customers = $customerModel
            //         ->where("name", "LIKE", "%" . $filters["client"] . "%")
            //         ->where("email", "LIKE", "%" . $filters["email_client"] . "%")
            //         ->pluck("id");
            //     $transactions->whereHas("sale", function ($querySale) use ($customers) {
            //         $querySale->whereIn("customer_id", $customers);
            //     });
            // }

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
                            ->toDateTimeString(),
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
                    $querySale->whereBetween($dateType, [$dateRange[0]." 00:00:00", $dateRange[1]." 23:59:59"]);
                })
                ->selectRaw("transactions.*, sales.start_date")
                ->orderByDesc("sales.start_date");

            return $transactions;
        } catch (Exception $e) {
            report($e);
            return null;
        }
    }

    public function getResume($filters)
    {
        $transactionModel = new Transaction();
        $transactions = $this->getSalesQueryBuilder($filters, false, null, true);
        $transactionStatus = implode(",", [
            Transaction::STATUS_PAID,
            Transaction::STATUS_TRANSFERRED,
            //Transaction::STATUS_CHARGEBACK,
            // Transaction::STATUS_REFUNDED,
        ]);
        $statusDispute = Sale::STATUS_IN_DISPUTE;

        $resume = $transactions
            ->without(["sale"])
            ->select(
                DB::raw(
                    "count(sales.id) as total_sales,
                              sum(if(transactions.status_enum in ({$transactionStatus}) && sales.status <> {$statusDispute}, transactions.value, 0)) / 100 as commission,
                              sum(sales.total_paid_value) as total",
                ),
            )
            ->first()
            ->toArray();

        $resume["commission"] = number_format($resume["commission"], 2, ",", ".");
        $resume["total"] = number_format($resume["total"], 2, ",", ".");

        return $resume;
    }

    public function getSaleWithDetails($saleId)
    {
        $companyModel = new Company();
        $saleModel = new Sale();

        //get sale
        $sale = $saleModel
            ->with(["transactions", "notazzInvoices", "affiliate", "saleRefundHistory", "apiToken"])
            ->find(hashids_decode($saleId, "sale_id"));

        //add details to sale
        $userCompanies = $companyModel->where("user_id", $sale->owner_id)->pluck("id");

        $this->getDetails($sale, $userCompanies);

        //invoices
        $invoices = [];
        foreach ($sale->notazzInvoices as $notazzInvoice) {
            $invoices[] = hashids_encode($notazzInvoice->id);
        }
        $sale->details->invoices = $invoices;

        return $sale;
    }

    public function getDetails($sale, $userCompanies)
    {
        $userTransaction = $sale->transactions
            ->where("invitation_id", null)
            ->whereIn("company_id", $userCompanies)
            ->first();

        //calcule total
        $subTotal = foxutils()->onlyNumbers($sale->sub_total);

        $total = $subTotal;

        $shipment_value = foxutils()->onlyNumbers($sale->shipment_value);
        $total += $shipment_value;
        $sale->shipment_value = number_format(intval($shipment_value) / 100, 2, ",", ".");

        if (foxutils()->onlyNumbers($sale->shopify_discount) > 0) {
            $total -= foxutils()->onlyNumbers($sale->shopify_discount);
            $discount = foxutils()->onlyNumbers($sale->shopify_discount);
        } else {
            $discount = "0,00";
        }

        $total -= $sale->automatic_discount;

        //valor do produtor
        $value = $userTransaction->value ?? 0;
        $cashbackValue = $sale->cashback->value ?? 0;
        $progressiveDiscount = $sale->progressive_discount ?? 0;
        $total -= $progressiveDiscount;


        $total = foxutils()->onlyNumbers(
            $sale->total_paid_value
        ); //reescrevendo valor total para corrigir erro quando a venda vem via api

        $comission = 'R$ '.number_format($value / 100, 2, ",", ".");

        //valor do afiliado
        $affiliateComission = "";
        $affiliateValue = 0;
        if (!empty($sale->affiliate_id)) {
            $affiliate = Affiliate::withTrashed()->find($sale->affiliate_id);
            $affiliateTransaction = $sale->transactions->where("company_id", $affiliate->company_id)->first();
            if (!empty($affiliateTransaction)) {
                $affiliateValue = $affiliateTransaction->value;
                $affiliateComission = 'R$ '.number_format($affiliateValue / 100, 2, ",", ".");
            }
        }

        $taxa = $totalTaxPercentage = $totalTax = $transactionTax = 0;
        //$totalToCalcTaxReal = ($sale->present()->getStatus() == 'refunded') ? $total + $sale->refund_value : $total;
        $totalToCalcTaxReal = $total + $cashbackValue;

        if (!empty($userTransaction) && $userTransaction->tax > 0) {
            if ($userTransaction->tax_type == Transaction::TYPE_PERCENTAGE_TAX) {
                $totalTaxPercentage = (int)($totalToCalcTaxReal * ($userTransaction->tax / 100));
                $totalTax += $totalTaxPercentage;
            } else {
                $totalTaxPercentage = foxutils()->onlyNumbers($userTransaction->tax);
                $totalTax += $totalTaxPercentage;
            }
        }

        if (!empty($userTransaction) && $userTransaction->transaction_tax > 0) {
            $transactionTax = foxutils()->onlyNumbers($userTransaction->transaction_tax);
            $totalTax += $transactionTax;
        }

        if (foxutils()->onlyNumbers($sale->installment_tax_value) > 0) {
            $taxaReal =
                $totalToCalcTaxReal -
                foxutils()->onlyNumbers($comission) -
                foxutils()->onlyNumbers($sale->installment_tax_value);
        } else {
            $taxaReal = $totalToCalcTaxReal - foxutils()->onlyNumbers($comission);
        }

        if ($taxaReal < 0) {
            $taxaReal *= -1;
        }

        if (!empty($sale->affiliate_id) && !empty(Affiliate::withTrashed()->find($sale->affiliate_id))) {
            $taxaReal -= $affiliateValue;
        }

        if (!empty($userTransaction->checkout_tax) && $userTransaction->checkout_tax > 0) {
            $taxaCheckout = FoxUtils::onlyNumbers($userTransaction->checkout_tax);
            $totalTax += $taxaCheckout;
        }

        // if ($sale->status == Sale::STATUS_REFUNDED) {
        //     $comission = foxutils()->formatMoney(0);
        // }

        //set flag

        if (empty($sale->flag)) {
            $sale->flag = $sale->present()->getPaymentFlag();
        }

        //format dates
        try {
            $sale->hours = (new Carbon($sale->start_date))->format("H:m:s") ?? "";
            $sale->start_date = (new Carbon($sale->start_date))->format("d/m/Y") ?? "";
        } catch (Exception $e) {
            $sale->hours = "";
        }

        if (isset($sale->boleto_due_date)) {
            try {
                $sale->boleto_due_date = (new Carbon($sale->boleto_due_date))->format("d/m/Y");
            } catch (Exception $e) {
                //
            }
        }

        if (!empty($userTransaction->release_date)) {
            $userTransaction->release_date = Carbon::parse($userTransaction->release_date);
        } else {
            $userTransaction->release_date = null;
        }
        $companyName = $userTransaction->company->fantasy_name;

        $saleInfo = SaleInformation::select("referer")
            ->where("sale_id", "=", $sale->id)
            ->orderByDesc("id")
            ->first();

        $referer = $saleInfo->referer ?? null;

        //add details to sale
        $sale->details = (object)[
            "transaction_tax" => foxutils()->formatMoney($transactionTax / 100),
            "tax" => $userTransaction->tax
                ? ($userTransaction->tax_type == 1
                    ? $userTransaction->tax."%"
                    : foxutils()->formatMoney(foxutils()->onlyNumbers($userTransaction->tax) / 100))
                : 0,
            "tax_type" => $userTransaction->tax_type ?? 0,
            "checkout_tax" =>
                foxutils()->onlyNumbers($userTransaction->checkout_tax) > 0
                    ? foxutils()->formatMoney(foxutils()->onlyNumbers($userTransaction->checkout_tax) / 100)
                    : null,
            "totalTax" => foxutils()->formatMoney($totalTax / 100),
            "total" => foxutils()->formatMoney($total / 100),
            "subTotal" => foxutils()->formatMoney(intval($subTotal) / 100),
            "discount" => foxutils()->formatMoney(intval($discount) / 100),
            "automatic_discount" => foxutils()->formatMoney(intval($sale->automatic_discount) / 100),
            "comission" => $comission,
            "taxa" => foxutils()->formatMoney($taxa / 100),
            "taxaDiscount" => foxutils()->formatMoney($totalTaxPercentage / 100),
            "taxaReal" => foxutils()->formatMoney($taxaReal / 100),
            "release_date" =>
                $userTransaction->release_date != null
                    ? $userTransaction->release_date->format("d/m/Y")
                    : "Processando",
            "has_withdrawal" => $userTransaction->withdrawal_id,
            "affiliate_comission" => $affiliateComission,
            "refund_value" => foxutils()->formatMoney(intval($sale->refund_value) / 100),
            "value_anticipable" => "0,00",
            "total_paid_value" => foxutils()->formatMoney($sale->total_paid_value),
            "refund_observation" => $sale->saleRefundHistory->count()
                ? $sale->saleRefundHistory->first()->refund_observation
                : null,
            "user_changed_observation" =>
                $sale->saleRefundHistory->count() && !$sale->saleRefundHistory->first()->user_id,
            "company_name" => $companyName,
            "referer" => $referer,
        ];
    }

    public function getProducts($saleId = null)
    {
        try {
            if ($saleId) {
                $productService = new ProductService();

                $products = $productService->getProductsBySale($saleId);

                return ProductsSaleResource::collection($products);
            }

            return null;
        } catch (Exception $ex) {
            Log::warning("Erro ao buscar produtos - SaleService - getProducts");
            report($ex);
        }
    }

    public function getEmailProducts($saleId)
    {
        $saleModel = new Sale();

        $sale = $saleModel->with(["productsPlansSale.product"])->find($saleId);
        $productsSale = [];
        if (!empty($sale)) {
            foreach ($sale->productsPlansSale as &$pps) {
                $product = $pps->product->toArray();
                $product["amount"] = $pps->amount;
                if (
                    $product["type_enum"] == (new Product())->present()->getType("digital") &&
                    !empty($product["digital_product_url"])
                ) {
                    $product["digital_product_url"] = foxutils()->getAwsSignedUrl(
                        $product["digital_product_url"],
                        $product["url_expiration_time"],
                    );
                    $pps->update([
                        "temporary_url" => $product["digital_product_url"],
                    ]);
                } else {
                    $product["digital_product_url"] = "";
                }

                $product["photo"] = foxutils()->checkFileExistUrl($product["photo"])
                    ? $product["photo"]
                    : "https://nexuspay-digital-products.s3.amazonaws.com/admin/produto.png";

                $productsSale[] = $product;
            }
        }

        return $productsSale;
    }

    public function checkPendingDebt($sale, $company, $transactionRefundAmount)
    {
        if (!in_array($sale->gateway_id, [Gateway::GETNET_PRODUCTION_ID, Gateway::GETNET_SANDBOX_ID])) {
            return;
        }

        $getnetBackOffice = new GetnetBackOfficeService();
        $getnetBackOffice
            ->setStatementSubSellerId(CompanyService::getSubsellerId($company))
            ->setStatementSaleHashId(hashids_encode($sale->id, "sale_id"));

        $gatewaySale = $getnetBackOffice->getStatement();

        $gatewaySale = json_decode($gatewaySale);

        if (isset($gatewaySale->list_transactions)) {
            foreach ($gatewaySale->list_transactions as $item) {
                if (
                    isset($item->summary) &&
                    isset($item->details) &&
                    is_array($item->details) &&
                    count($item->details) == 1
                ) {
                    $summary = $item->summary;
                    $details = $item->details[0];

                    $transactionStatusCode = $summary->transaction_status_code;
                    $hasOrderId = empty($summary->order_id) ? false : true;
                    $isTransactionCredit = $details->transaction_sign == "+";

                    $refundObservation = "Estorno da venda: ".hashids_encode($sale->id, "sale_id");

                    if (
                        !is_null($details->subseller_rate_confirm_date) &&
                        $hasOrderId &&
                        $isTransactionCredit &&
                        $transactionStatusCode == GetNetStatementService::TRANSACTION_STATUS_CODE_APROVADO
                    ) {
                        PendingDebt::create([
                            "company_id" => $company->id,
                            "sale_id" => $sale->id,
                            "type" => PendingDebt::REVERSED,
                            "request_date" => Carbon::now(),
                            "reason" => $refundObservation,
                            "value" => $transactionRefundAmount,
                        ]);
                    }
                }
            }
        }
    }

    public function getSaleTax($sale, $cashbackValue)
    {
        $foxValue = $sale->transactions->whereNull("company_id")->first()->value ?? 0;
        $inviteValue =
            $sale->transactions
                ->whereNotNull("company_id")
                ->where("type", 3)
                ->first()->value ?? 0;

        $saleTax = $foxValue + $cashbackValue + $inviteValue;

        if (!empty(foxutils()->onlyNumbers($sale->interest_total_value))) {
            $saleTax -= foxutils()->onlyNumbers($sale->interest_total_value);
        }

        if (!empty(foxutils()->onlyNumbers($sale->installment_tax_value))) {
            $saleTax -= foxutils()->onlyNumbers($sale->installment_tax_value);
        }

        return $saleTax;
    }

    public function getSaleTaxRefund($sale, $cashbackValue)
    {
        $saleTax = $this->getSaleTax($sale, $cashbackValue);

        if (!empty(foxutils()->onlyNumbers($sale->installment_tax_value))) {
            $saleTax += foxutils()->onlyNumbers($sale->installment_tax_value);
        }

        return $saleTax;
    }

    public function getSaleTotalValue($sale)
    {
        $total = foxutils()->onlyNumbers($sale->sub_total);

        if (!empty(foxutils()->onlyNumbers($sale->shipment_value))) {
            $total += foxutils()->onlyNumbers($sale->shipment_value);
        }

        if (!empty(foxutils()->onlyNumbers($sale->installment_tax_value))) {
            $total -= foxutils()->onlyNumbers($sale->installment_tax_value);
        }

        if (!empty(foxutils()->onlyNumbers($sale->automatic_discount))) {
            $total -= foxutils()->onlyNumbers($sale->automatic_discount);
        }
        return $total;
    }

    public function manualRefund(Sale $sale, $refundObservation)
    {
        try {
            DB::beginTransaction();
            $vegaBalance = 0;
            $saleIdEncode = hashids_encode($sale->id, "sale_id");
            $isBillet = $sale->payment_method == Sale::BILLET_PAYMENT;

            SaleRefundHistory::create([
                "sale_id" => $sale->id,
                "refunded_amount" => foxutils()->onlyNumbers($sale->total_paid_value),
                "date_refunded" => Carbon::now(),
                "gateway_response" => json_encode([]),
                "refund_value" => foxutils()->onlyNumbers($sale->total_paid_value),
                "refund_observation" => $refundObservation,
                "user_id" => auth()->user()->account_owner_id ?? $sale->owner_id,
            ]);

            foreach ($sale->transactions as $transaction) {
                if (empty($transaction->company_id)) {
                    $transaction->update([
                        "status_enum" => $isBillet ? Transaction::STATUS_BILLET_REFUNDED : Transaction::STATUS_REFUNDED,
                        "status" => $isBillet ? "billet_refunded" : "refunded",
                    ]);
                    continue;
                }

                $vegaBalance = $transaction->company->vega_balance;

                if ($transaction->status_enum == Transaction::STATUS_PAID) {
                    Transfer::create([
                        "transaction_id" => $transaction->id,
                        "user_id" => $transaction->company->user_id,
                        "company_id" => $transaction->company->id,
                        "type_enum" => Transfer::TYPE_IN,
                        "value" => $transaction->value,
                        "type" => "in",
                        "gateway_id" => $sale->gateway_id,
                    ]);

                    $vegaBalance += $transaction->value;
                    $transaction->company->update([
                        "vega_balance" => $vegaBalance,
                    ]);
                }

                $refundValue = $transaction->value;

                if ($transaction->type == Transaction::TYPE_PRODUCER) {
                    $refundValue = (int)foxutils()->onlyNumbers($sale->total_paid_value);
                }

                Transfer::create([
                    "transaction_id" => $transaction->id,
                    "user_id" => $transaction->company->user_id,
                    "value" => $refundValue,
                    "type" => "out",
                    "type_enum" => Transfer::TYPE_OUT,
                    "reason" => $isBillet ? "Estorno de boleto #{$saleIdEncode}" : "Estorno de pix #{$saleIdEncode}",
                    "company_id" => $transaction->company->id,
                    "gateway_id" => $sale->gateway_id,
                ]);

                $transaction->company->update([
                    "vega_balance" => $vegaBalance - $refundValue,
                ]);

                $transaction->update([
                    "status_enum" => $isBillet ? Transaction::STATUS_BILLET_REFUNDED : Transaction::STATUS_REFUNDED,
                    "status" => $isBillet ? "billet_refunded" : "refunded",
                ]);
            }

            $sale->update([
                "status" => $isBillet ? Sale::STATUS_BILLET_REFUNDED : Sale::STATUS_REFUNDED,
                "gateway_status" => "refunded",
            ]);

            self::createSaleLog($sale->id, $isBillet ? "billet_refunded" : "refunded");

            $transactionUser = Transaction::where("sale_id", $sale->id)
                ->where("type", Transaction::TYPE_PRODUCER)
                ->first();

            Transfer::create([
                "transaction_id" => $transactionUser->id,
                "user_id" => auth()->user()->account_owner_id ?? $sale->owner_id,
                "customer_id" => $sale->customer_id,
                "company_id" => $transactionUser->company_id,
                "value" => foxutils()->onlyNumbers($sale->total_paid_value),
                "type_enum" => Transfer::TYPE_IN,
                "type" => "in",
                "reason" => $isBillet ? "Estorno de boleto #{$saleIdEncode}" : "Estorno de pix #{$saleIdEncode}",
            ]);

            $sale->customer->update([
                "balance" => $sale->customer->balance + foxutils()->onlyNumbers($sale->total_paid_value),
            ]);

            event(new ManualRefundEvent($sale));

            DB::commit();

            return "Venda estornado com sucesso";
        } catch (Exception $ex) {
            report($ex);
            DB::rollBack();
            throw $ex;
        }
    }

    public function getResumeBlocked($filters)
    {
        $cacheName = "blocked-resume-".Auth::user()->getAccountOwnerId()."-".json_encode($filters);
        return cache()->remember($cacheName, 120, function () use ($filters) {
            $transactionModel = new Transaction();
            $filters["invite"] = 1;
            $transactions = $this->getSalesBlockedBalance($filters);
            $transactionStatus = implode(",", [Transaction::STATUS_TRANSFERRED, Transaction::STATUS_PAID]);

            $resume = $transactions
                ->without(["sale"])
                ->select(
                    DB::raw(
                        "
                        sum(CASE WHEN transactions.invitation_id IS NULL THEN 1 ELSE 0 END) as total_sales,
                        sum(CASE WHEN transactions.invitation_id IS NULL THEN
                            if(transactions.status_enum in ({$transactionStatus}), transactions.value, 0) ELSE 0 END
                        ) / 100 as commission,
                        sum(CASE WHEN transactions.invitation_id IS NOT NULL THEN
                            if(transactions.status_enum in ({$transactionModel->present()->getStatusEnum(
                            "transfered",
                        )}), transactions.value, 0) ELSE 0 END
                        ) / 100 as commission_invite,
                        sum(CASE WHEN transactions.invitation_id IS NULL THEN
                                (sales.sub_total + sales.shipment_value) -
                                (ifnull(sales.shopify_discount, 0) + sales.automatic_discount) / 100
                                ELSE 0 END
                            ) as total",
                    ),
                )
                ->first()
                ->toArray();

            $resume["commission"] = number_format($resume["commission"], 2, ",", ".");
            $resume["commission_invite"] = number_format($resume["commission_invite"], 2, ",", ".");
            $resume["total"] = number_format($resume["total"], 2, ",", ".");

            return $resume;
        });
    }

    public function getSalesBlockedBalance($filters)
    {
        try {
            $customerModel = new Customer();
            $transactionModel = new Transaction();

            $blockReasonQuery = function ($blocked) use ($filters) {
                $blocked->where("status", BlockReasonSale::STATUS_BLOCKED);
                if (!empty($filters["reason"])) {
                    $blocked->where("blocked_reason_id", intval($filters["reason"]));
                }
            };

            $transactions = $transactionModel
                ->with([
                    "sale.project",
                    "sale.customer",
                    "sale.plansSales.plan",
                    "sale.tracking",
                    "sale.productsPlansSale",
                    "sale.affiliate" => function ($funtionTrash) {
                        $funtionTrash->withTrashed()->with("user");
                    },
                    "blockReasonSale" => $blockReasonQuery,
                ])
                ->where("company_id", current(Hashids::decode($filters["company"])))
                ->where(
                    "user_id",
                    auth()
                        ->user()
                        ->getAccountOwnerId(),
                )
                ->join("sales", "sales.id", "transactions.sale_id")
                ->whereHas("blockReasonSale", $blockReasonQuery);

            if (!empty($filters["company"])) {
                $companyId = hashids_decode($filters["company"]);
                $transactions->where("company_id", $companyId);
            }

            if (empty($filters["invite"])) {
                $transactions->whereNull("invitation_id");
            }

            if (!empty($filters["project"])) {
                $showSalesApi = $filters["project"] == "API-TOKEN";
                $projectId = $showSalesApi ? null : hashids_decode($filters["project"]);

                $transactions->where("sales.api_flag", $showSalesApi);

                if (!$showSalesApi) {
                    $transactions->where("sales.project_id", $projectId);
                }
            }

            if (!empty($filters["transaction"])) {
                $saleId = hashids_decode(str_replace("#", "", $filters["transaction"]), "sale_id");

                $transactions->where("sales.id", $saleId);
            }

            if (!empty($filters["client"])) {
                $customers = $customerModel->where("name", "LIKE", "%".$filters["client"]."%")->pluck("id");
                $transactions->whereIn("sales.customer_id", $customers);
            }

            if (!empty($filters["customer_document"])) {
                $customers = $customerModel
                    ->where("document", foxutils()->onlyNumbers($filters["customer_document"]))
                    ->pluck("id");

                if (count($customers) < 1) {
                    $customers = $customerModel->where("document", $filters["customer_document"])->pluck("id");
                }

                $transactions->whereIn("sales.customer_id", $customers);
            }

            if (!empty($filters["payment_method"])) {
                $transactions->where("sales.payment_method", $filters["payment_method"]);
            }

            if (!empty($filters["plan"])) {
                $planId = hashids_decode($filters["plan"]);
                $transactions->whereHas("sale.plansSales", function ($query) use ($planId) {
                    $query->where("plan_id", $planId);
                });
            }

            $dateRange = foxutils()->validateDateRange($filters["date_range"]);

            //$status = (!empty($filters['status'])) ? [$filters['status']] : [1, 24];
            $transactions
                ->whereBetween("sales.".$filters["date_type"], [
                    $dateRange[0]." 00:00:00",
                    $dateRange[1]." 23:59:59",
                ])
                //->whereIn('sales.status', $status)
                ->selectRaw("transactions.*, sales.start_date")
                ->orderByDesc("sales.start_date");

            // $companyId = current(Hashids::decode($filters["plan"]));
            // $transactions->whereHas(
            //     'sale.plansSales',
            //     function ($query) use ($planId) {
            //         $query->where('plan_id', $planId);
            //     }
            // );

            return $transactions;
        } catch (Exception $e) {
            report($e);

            return null;
        }
    }

    public function getResumePending($filters)
    {
        $cacheName = "pending-resume-".json_encode($filters);
        return cache()->remember($cacheName, 120, function () use ($filters) {
            $transactions = $this->getSalesPendingBalance($filters);
            $transactionStatus = implode(",", [Transaction::STATUS_PAID]);

            $resume = $transactions
                ->without(["sale"])
                ->select(
                    DB::raw(
                        "count(sales.id) as total_sales, sum(if(transactions.status_enum in ({$transactionStatus}), transactions.value, 0)) / 100 as commission",
                    ),
                )
                ->first()
                ->toArray();

            $resume["commission"] = foxutils()->formatMoney($resume["commission"]);

            return $resume;
        });
    }

    public function getSalesPendingBalance($filters)
    {
        try {
            $relationsArray = ["sale", "sale.project", "sale.customer"];

            $transactions = Transaction::with($relationsArray)
                ->where(
                    "user_id",
                    auth()
                        ->user()
                        ->getAccountOwnerId(),
                )
                ->where("company_id", auth()->user()->company_default)
                ->join("sales", "sales.id", "transactions.sale_id")
                ->leftJoin("block_reason_sales", "block_reason_sales.sale_id", "transactions.sale_id")
                ->where("transactions.status_enum", Transaction::STATUS_PAID)
                ->whereNull("invitation_id");

            // Filtro Company
            // if (!empty($filters["company"])) {
            //     $companyId = hashids_decode($filters["company"]);
            //     $transactions->where('company_id', $companyId);
            // }

            $transactions->whereNull("withdrawal_id");
            if (!empty($filters["acquirer"])) {
                $gatewayIds = $this->getGatewayIdsByFilter($filters["acquirer"]);
                if ($filters["acquirer"] != "Cielo") {
                    $transactions->whereIn("transactions.gateway_id", $gatewayIds);
                }

                switch ($filters["acquirer"]) {
                    case "Asaas":
                        $transactions->where("transactions.created_at", ">", "2021-09-20");
                        break;
                    case "Getnet":
                    case "Gerencianet":
                        $transactions->where("is_waiting_withdrawal", 0);
                        break;
                    case "Cielo":
                        if (auth()->user()->show_old_finances) {
                            $transactions->where(function ($query) use ($gatewayIds) {
                                $query->whereIn("transactions.gateway_id", $gatewayIds)->orWhere(function ($query) {
                                    $query
                                        ->where("transactions.gateway_id", Gateway::ASAAS_PRODUCTION_ID)
                                        ->where("transactions.created_at", "<", "2021-09");
                                });
                            });
                        }
                        break;
                }
            } else {
                $transactions->where(function ($qr) {
                    $qr->where(function ($qr2) {
                        $qr2->whereIn("transactions.gateway_id", $this->getGatewayIdsByFilter("Asaas"))->where(
                            "transactions.created_at",
                            ">",
                            "2021-09-20",
                        );
                    })
                        ->orWhere(function ($qr2) {
                            $qr2->whereIn("transactions.gateway_id", $this->getGatewayIdsByFilter("Vega"));
                        })
                        ->orWhere(function ($qr2) {
                            $qr2->whereIn(
                                "transactions.gateway_id",
                                $this->getGatewayIdsByFilter("Gerencianet"),
                            )->where("is_waiting_withdrawal", 0);
                        })
                        ->orWhere(function ($qr3) {
                            $qr3->where("is_waiting_withdrawal", 0)->whereIn(
                                "transactions.gateway_id",
                                $this->getGatewayIdsByFilter("Getnet"),
                            );
                        })
                        ->orWhere(function ($qr2) {
                            if (auth()->user()->show_old_finances) {
                                $qr2->whereIn("transactions.gateway_id", $this->getGatewayIdsByFilter("Cielo"))
                                    ->where("block_reason_sales.status", BlockReasonSale::STATUS_BLOCKED)
                                    ->whereNull("block_reason_sales.id")
                                    ->orWhere(function ($query) {
                                        $query
                                            ->where("transactions.gateway_id", Gateway::ASAAS_PRODUCTION_ID)
                                            ->where("transactions.created_at", "<", "2021-09");
                                    });
                            }
                        });
                });
            }

            // Filtros - INICIO
            $dateRange = foxutils()->validateDateRange($filters["date_range"]);
            $dateType = $filters["date_type"];

            // Filtro de Data
            $transactions
                ->whereHas("sale", function ($querySale) use ($dateRange, $dateType) {
                    $querySale->whereBetween($dateType, [$dateRange[0]." 00:00:00", $dateRange[1]." 23:59:59"]);
                })
                ->selectRaw("transactions.*, sales.start_date")
                ->orderByDesc("sales.start_date");

            // Projeto
            if (!empty($filters["project"])) {
                $showSalesApi = $filters["project"] == "API-TOKEN";
                $projectId = $showSalesApi ? null : hashids_decode($filters["project"]);

                $transactions->whereHas("sale", function ($querySale) use ($projectId, $showSalesApi) {
                    if (!$showSalesApi) {
                        $querySale->where("sales.project_id", $projectId);
                        return;
                    }
                    $querySale->where("sales.api_flag", $showSalesApi);
                });
            }

            // Código de Venda
            if (!empty($filters["sale_code"])) {
                $saleId = !empty(hashids_decode($filters["sale_code"], "sale_id"))
                    ? hashids_decode($filters["sale_code"], "sale_id")
                    : 0;

                $transactions->whereHas("sale", function ($querySale) use ($saleId) {
                    $querySale->where("id", $saleId);
                });
            }

            // Nome do Usuário
            if (!empty($filters["client"])) {
                $customerIds = Customer::where("name", "LIKE", "%".$filters["client"]."%")->pluck("id");
                $transactions->whereHas("sale", function ($querySale) use ($customerIds) {
                    $querySale->whereIn("customer_id", $customerIds);
                });
            }

            // CPF do Usuário
            if (!empty($filters["customer_document"])) {
                $customers = Customer::where("document", foxutils()->onlyNumbers($filters["customer_document"]))->pluck(
                    "id",
                );

                if (count($customers) < 1) {
                    $customers = Customer::where("document", $filters["customer_document"])->pluck("id");
                }

                $transactions->whereHas("sale", function ($querySale) use ($customers) {
                    $querySale->whereIn("customer_id", $customers);
                });
            }

            // Forma de pagamento
            if (!empty($filters["payment_method"])) {
                $forma = $filters["payment_method"];
                $transactions->whereHas("sale", function ($querySale) use ($forma) {
                    $querySale->where("payment_method", $forma);
                });
            }

            // Reserva de Segurança
            if (!empty($filters["is_security_reserve"]) && $filters["is_security_reserve"] == true) {
                $transactions->where("is_security_reserve", true);
            }

            // Filtros - FIM
            return $transactions;
        } catch (Exception $e) {
            report($e);

            return null;
        }
    }

    public function getPendingBalance($filters)
    {
        $cacheName = "pending-".json_encode($filters);
        return cache()->remember($cacheName, 120, function () use ($filters) {
            $transactions = $this->getSalesPendingBalance($filters);
            return $transactions->paginate(10);
        });
    }

    public function getPaginetedBlocked($filters)
    {
        $cacheName = "blocked-".json_encode($filters);
        return cache()->remember($cacheName, 120, function () use ($filters) {
            $transactions = $this->getSalesBlockedBalance($filters);
            return $transactions->paginate(10);
        });
    }

    public function getApprovedSalesInPeriod(User $user, Carbon $startDate, Carbon $endDate)
    {
        return Sale::whereIn("status", [
            Sale::STATUS_APPROVED,
            Sale::STATUS_CHARGEBACK,
            Sale::STATUS_REFUNDED,
            Sale::STATUS_IN_DISPUTE,
        ])
            ->whereBetween("start_date", [
                $startDate->format("Y-m-d")." 00:00:00",
                $endDate->format("Y-m-d")." 23:59:59",
            ])
            ->where(function ($query) use ($user) {
                $query->where("owner_id", $user->id)->orWhere("affiliate_id", $user->id);
            });
    }

    public function getCreditCardApprovedSalesInPeriod(User $user, Carbon $startDate, Carbon $endDate)
    {
        return Sale::where("payment_method", Sale::PAYMENT_TYPE_CREDIT_CARD)
            ->whereIn("status", [Sale::STATUS_APPROVED, Sale::STATUS_CHARGEBACK, Sale::STATUS_REFUNDED])
            ->whereBetween("start_date", [
                $startDate->format("Y-m-d")." 00:00:00",
                $endDate->format("Y-m-d")." 23:59:59",
            ])
            ->where(function ($query) use ($user) {
                $query->where("owner_id", $user->id)->orWhere("affiliate_id", $user->id);
            })
            ->count();
    }

    public function returnBlacklistBySale(Sale $sale): array
    {
        try {
            $descriptionBlackList = [];
            if ($sale->status == 10) {
                $saleBlackList = SaleWhiteBlackListResult::where("sale_id", $sale->id)->first();
                if (!empty($saleBlackList)) {
                    if ($saleBlackList->blacklist) {
                        $descriptionBlackListJson = json_decode($saleBlackList->whiteblacklist_json);
                        $descriptionBlackList[] = $descriptionBlackListJson->blackList;
                    }
                }
            }
            return $descriptionBlackList;
        } catch (Exception $e) {
            report($e);
        }
    }

    public function verifyIfUserHasSalesByDate(Carbon $date, int $user_id): bool
    {
        $sale = Sale::where("owner_id", $user_id)
            ->whereDate("start_date", ">=", $date)
            ->where("status", Sale::STATUS_APPROVED)
            ->count();

        return $sale >= 1;
    }

    public static function createSaleLog($saleId, $status)
    {
        try {
            if (is_int($saleId) && !empty($status)) {
                $statusPresenter = (new Sale())->present()->getStatus($status);

                SaleLog::query()
                    ->create([
                        "sale_id" => $saleId,
                        "status" => is_int($status) ? $statusPresenter : $status,
                        "status_enum" => is_int($statusPresenter) ? $statusPresenter : $status,
                    ]);
            }
        } catch (Exception $e) {
            report($e);
        }
    }

    public function getGatewayIdsByFilter($nameGateway)
    {
        switch ($nameGateway) {
            case "Asaas":
                return [Gateway::ASAAS_PRODUCTION_ID, Gateway::ASAAS_SANDBOX_ID];
            case "Getnet":
                return [Gateway::GETNET_PRODUCTION_ID, Gateway::GETNET_SANDBOX_ID];
            case "Gerencianet":
                return [Gateway::GERENCIANET_PRODUCTION_ID, Gateway::GERENCIANET_SANDBOX_ID];
            case "Cielo":
                return [Gateway::CIELO_PRODUCTION_ID, Gateway::CIELO_SANDBOX_ID];
            case "Vega":
                return [
                    Gateway::SAFE2PAY_PRODUCTION_ID,
                    Gateway::SAFE2PAY_SANDBOX_ID,
                    Gateway::IUGU_PRODUCTION_ID,
                    Gateway::IUGU_SANDBOX_ID,
                    Gateway::VEGA_PRODUCTION_ID,
                    Gateway::VEGA_SANDBOX_ID,
                ];
        }
        return [];
    }

    public static function refundReceipt($hashSaleId, $transaction)
    {
        $company = (object)$transaction->company->toArray();
        $company->subseller_getnet_id = CompanyService::getSubsellerId($transaction->company);
        $transaction->flag = strtoupper($transaction->sale->flag) ?? null;

        $saleInfo = DB::table("sale_informations")
            ->select("customer_name", "last_four_digits")
            ->where("sale_id", "=", $transaction->sale_id)
            ->orderByDesc("id")
            ->first();

        $arr = explode(" ", trim($saleInfo->customer_name));
        $saleInfo->firstname = $arr[0];

        $checkoutConfigs = DB::table("checkout_configs")
            ->select("checkout_logo")
            ->where("checkout_logo_enabled", "=", "1")
            ->where("project_id", "=", $transaction->sale->project_id)
            ->first();

        $productsPlansSales = DB::table("products_plans_sales")
            ->select("amount", "name")
            ->where("sale_id", "=", $transaction->sale_id)
            ->get();

        $refundDate = $transaction->sale
            ->saleLogs()
            ->whereIn("status_enum", [Sale::STATUS_REFUNDED, Sale::STATUS_BILLET_REFUNDED])
            ->first()->created_at;

        return PDF::loadView(
            "sales::refund_receipt",
            compact("company", "transaction", "saleInfo", "checkoutConfigs", "productsPlansSales", "refundDate"),
        );
    }

    public static function getProjectsWithSales()
    {
        $companyId = auth()->user()->company_default;
        $userId = auth()
            ->user()
            ->getAccountOwnerId();

        return DB::table("sales")
            ->select("sales.project_id", "projects.name", DB::Raw("'' as prefix"))
            ->distinct()
            ->leftJoin("projects", "projects.id", "=", "sales.project_id")
            ->leftJoin("transactions", "transactions.sale_id", "=", "sales.id")
            ->where("sales.gateway_status", "!=", "canceled")
            ->where("transactions.user_id", $userId)
            ->where("transactions.company_id", $companyId)
            ->whereNull("transactions.invitation_id")
            ->where(function ($query) {
                if (auth()->user()->deleted_project_filter) {
                    $query->whereIn("projects.status", [1, 2]);
                } else {
                    $query->where("projects.status", 1);
                }
            })
            ->get();
    }

    public static function getProjectsWithSalesAndTokens()
    {
        $companyId = auth()->user()->company_default;
        $userId = auth()
            ->user()
            ->getAccountOwnerId();

        $projects = self::getProjectsWithSales();

        $tokens = DB::table("sales")
            ->select("api.id as project_id", "api.description as name", DB::Raw("'TOKEN-' as prefix"))
            ->distinct()
            ->join("api_tokens as api", "api.id", "=", "sales.api_token_id")
            ->where("api.user_id", $userId)
            ->whereIn("api.integration_type_enum", [4, 5])
            ->whereNull("api.deleted_at")
            ->where("api.company_id", $companyId)
            ->where("sales.gateway_status", "!=", "canceled")
            ->get();

        return $projects->merge($tokens);
    }

    public function refund(Sale $sale, $refundObservation = null)
    {
        if ($sale->status != Sale::STATUS_APPROVED) {
            return [
                "status" => "error",
                "message" => "Somente vendas aprovadas podem ser estornada.",
            ];
        }

        $gatewayService = Gateway::getServiceById($sale->gateway_id);

        if (!$gatewayService->refundEnabled()) {
            return [
                "status" => "error",
                "message" => "Está venda não pode mais ser estornada.",
            ];
        }

        if ($sale->contestations->count() > 0) {
            return [
                "status" => "error",
                "message" => "Estorno desabilitado, venda está em pré-chargeback (contestação)",
            ];
        }

        $producerCompany = $sale
            ->transactions()
            ->where("user_id", $sale->owner_id)
            ->first()->company;
        $gatewayService->setCompany($producerCompany);

        if (!$gatewayService->hasEnoughBalanceToRefund($sale)) {
            return [
                "status" => "error",
                "message" => "Saldo insuficiente para realizar o estorno",
            ];
        }

        $message = null;

        if ($gatewayService->canRefund($sale)) {
            $result = (new CheckoutService())->cancelPaymentCheckout($sale);
            if ($result["status"] != "success") {
                return [
                    "status" => "error",
                    "message" => $result["message"],
                ];
            }

            $gatewayService->cancel($sale, $result["response"], $refundObservation);

            event(new SaleRefundedEvent($sale));

            $message = $result["message"];
        } else {
            $message = $this->manualRefund($sale, $refundObservation);
        }

        return [
            "status" => "success",
            "message" => $message,
        ];
    }

    public function alreadyRefunded(Sale $sale)
    {
        return $sale->status == Sale::STATUS_REFUNDED || $sale->status == Sale::STATUS_BILLET_REFUNDED;
    }
}
