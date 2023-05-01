<?php

namespace Modules\Core\Services;

use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\Affiliate;
use Modules\Core\Entities\Checkout;
use Modules\Core\Entities\Domain;
use Modules\Core\Entities\RegeneratedBillet;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\UserProject;
use SendGrid;
use SendGrid\Mail\Mail;
use Vinkla\Hashids\Facades\Hashids;

/**
 * Class CheckoutService
 * @package Modules\Core\Services
 */
class CheckoutService
{
    /**
     * @var string
     */
    private $internalApiToken;

    public function getAbandonedCart(): LengthAwarePaginator
    {
        $companyId = hashids_decode(request("company"));
        $projectIdsAffiliate = [];
        $projectIds = [];

        $ownerId = auth()
            ->user()
            ->getAccountOwnerId();

        if (request("project") == "all") {
            $projectIds = UserProject::where("user_id", $ownerId)
                ->where("type_enum", UserProject::TYPE_PRODUCER_ENUM)
                ->pluck("project_id")
                ->toArray();

            $projectIdsAffiliate = Affiliate::where("user_id", $ownerId)
                ->where("affiliates.company_id", $companyId)
                ->where("status_enum", Affiliate::STATUS_ACTIVE)
                ->pluck("project_id")
                ->toArray();
        } else {
            $projects = explode(",", request("project"));
            foreach ($projects as $project) {
                $project = hashids_decode($project);
                $isAffiliate = Affiliate::where("user_id", $ownerId)
                    ->where("affiliates.company_id", $companyId)
                    ->where("status_enum", Affiliate::STATUS_ACTIVE)
                    ->where("affiliates.project_id", $project)
                    ->first();
                if ($isAffiliate === null) {
                    array_push($projectIds, $project);
                } else {
                    array_push($projectIdsAffiliate, $project);
                }
            }
        }

        $dateRange = foxutils()->validateDateRange(request("date_range"));

        $abandonedCartsStatus = [Checkout::STATUS_RECOVERED, Checkout::STATUS_ABANDONED_CART];

        $plansIds = [];
        if (request("plan") == "all") {
            $plansIds = "";
        } else {
            $plans = explode(",", request("plan"));
            foreach ($plans as $plan) {
                array_push($plansIds, hashids_decode($plan));
            }
        }

        $abandonedCarts = Checkout::join("checkout_configs", "checkouts.project_id", "=", "checkout_configs.project_id")
            ->leftJoin("affiliates", "affiliates.id", "=", "checkouts.affiliate_id")
            ->select("checkouts.*")
            ->with([
                "project.domains" => function ($query) {
                    $query->where("status", Domain::STATUS_APPROVED);
                },
                "checkoutPlans.plan",
            ])
            ->whereHas("checkoutPlans", function ($query) {
                $query->whereHas("plan");
            })
            ->whereIn("checkouts.status_enum", $abandonedCartsStatus)
            ->whereBetween("checkouts.created_at", [$dateRange[0] . " 00:00:00", $dateRange[1] . " 23:59:59"])
            ->where(function ($query) use ($projectIds, $projectIdsAffiliate, $companyId) {
                $query
                    ->where(function ($query) use ($projectIds, $projectIdsAffiliate, $companyId) {
                        $query
                            ->whereIn("checkouts.project_id", $projectIdsAffiliate)
                            ->where("affiliates.company_id", $companyId);
                    })
                    ->orWhere(function ($query) use ($projectIds, $companyId) {
                        $query
                            ->whereIn("checkouts.project_id", $projectIds)
                            ->where("checkout_configs.company_id", $companyId);
                    });
            })
            ->when(!empty(request("client")), function ($query) {
                $query->where("client_name", "like", "%" . request("client") . "%");
            })
            ->when(!empty(request("client_document")), function ($query) {
                $query->whereHas("logs", function ($query) {
                    $query->where("document", request("client_document"));
                });
            })
            ->when(!empty($plansIds), function ($query) use ($plansIds) {
                $query->whereHas("checkoutPlans", function ($query) use ($plansIds) {
                    $query->whereIn("plan_id", $plansIds);
                });
            });

        return $abandonedCarts->orderBy("checkouts.id", "DESC")->paginate(10);
    }

    /**
     * @param null $checkoutPlans
     * @return float|int
     */
    public function getSubTotal($checkoutPlans = null)
    {
        $total = 0;
        foreach ($checkoutPlans as $checkoutPlan) {
            if (!empty($checkoutPlan->plan)) {
                $total +=
                    intval(preg_replace("/[^0-9]/", "", $checkoutPlan->plan->price)) * intval($checkoutPlan->amount);
            }
        }

        return $total;
    }

    public function cancelPaymentCheckout($sale): array
    {
        try {
            $idEncoded = hashids_encode($sale->id, "sale_id");
            $urlCancelPayment = env("CHECKOUT_URL") . "/api/payment/cancel/{$idEncoded}";

            $response = $this->runCurl($urlCancelPayment, "POST");

            if (!empty($response) && ($response->status ?? "") == "success") {
                return [
                    "status" => "success",
                    "message" => $response->message ?? "Venda estornada com sucesso.",
                    "response" => $response,
                ];
            }

            return [
                "status" => "error",
                "message" => $response->message ?? "Error ao tentar estornar venda.",
                "response" => $response ?? [],
            ];
        } catch (Exception $ex) {
            report($ex);

            return [
                "status" => "error",
                "message" => "Ocorreu algum erro, tente novamente em alguns minutos.",
                "response" => [],
                "error" => $ex->getMessage(),
            ];
        }
    }

    /**
     * @param $saleId
     * @param $totalPaidValue
     * @param $dueDate
     * @return array
     */
    public function regenerateBillet($saleId, $totalPaidValue, $dueDate)
    {
        try {
            $saleIdDecode = current(Hashids::connection("sale_id")->decode($saleId));
            $saleModel = new Sale();
            $sale = $saleModel
                ::with("project.domains")
                ->where("id", $saleIdDecode)
                ->first();

            $billets = RegeneratedBillet::where("sale_id", $saleIdDecode)
                ->orWhere("owner_id", $sale->owner_id)
                ->limit(6)
                ->get();

            if ($billets->where("sale_id", $saleIdDecode)->count() > 4) {
                return [
                    "status" => "error",
                    "error" => "error",
                    "message" => "Só é permitido regerar 4 boletos por venda",
                ];
            }

            if ($billets->where("created_at", ">", Carbon::now()->subMinute())->count() > 1) {
                return [
                    "status" => "error",
                    "error" => "error",
                    "message" => "Aguarde um instante, só é permitido regerar 2 boletos por minuto",
                ];
            }

            $domain = $sale->project->domains->where("status", 3)->first();
            if (foxutils()->isProduction()) {
                $regenerateBilletUrl = "https://checkout.cloudfox.net/api/payment/regeneratebillet";
            } else {
                $regenerateBilletUrl =
                    env("CHECKOUT_URL", "http://dev.checkout.com.br") . "/api/payment/regeneratebillet";
            }

            $data = [
                "sale_id" => $saleId,
                "due_date" => $dueDate,
                "total_paid_value" => $totalPaidValue,
            ];

            $response = $this->runCurl($regenerateBilletUrl, "POST", $data);
            if (
                !empty($response->status) &&
                !empty($response->response->status) &&
                $response->status == "success" &&
                $response->response->status == "success"
            ) {
                $dataUpdate = (array) $response->response;
                RegeneratedBillet::create([
                    "sale_id" => $saleIdDecode,
                    "billet_link" => $dataUpdate["boleto_link"],
                    "billet_digitable_line" => $dataUpdate["boleto_digitable_line"],
                    "billet_due_date" => $dataUpdate["boleto_due_date"],
                    "gateway_transaction_id" => $dataUpdate["gateway_transaction_id"],
                    "gateway_billet_identificator" => $dataUpdate["gateway_billet_identificator"] ?? null,
                    "gateway_id" => $sale->gateway_id,
                    "owner_id" => $sale->owner_id,
                ]);
                $result = [
                    "status" => "success",
                    "message" => print_r($response->message, true) ?? "",
                    "response" => $response,
                ];
                // } else {
                //     $result = [
                //         'status'   => 'error',
                //         'error'    => 'error',
                //         'message'  => 'Error ao tentar regerar boleto, tente novamente em instantes!',
                //         'response' => $response,
                //     ];
                // }
            } else {
                $result = [
                    "status" => "error",
                    "error" => "error",
                    "message" => "Error ao tentar regerar boleto, tente novamente em instantes!",
                    "response" => $response,
                ];
            }

            return $result;
        } catch (Exception $ex) {
            report($ex);

            return [
                "status" => "error",
                "message" => "Error ao tentar regerar boleto.",
                "error" => $ex->getMessage(),
            ];
        }
    }

    /**
     * @throws Exception
     */
    public function runCurl($url, $method = "GET", $data = null)
    {
        try {
            $this->internalApiToken = env("ADMIN_TOKEN");
            $headers = ["Content-Type: application/json", "Accept: application/json"];
            if (!empty($this->internalApiToken)) {
                $headers[] = "Api-name:ADMIN";
                $headers[] = "Api-token:" . $this->internalApiToken;
            }
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            if ($method == "POST") {
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            }
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            $result = curl_exec($ch);
            return json_decode($result);
        } catch (Exception $ex) {
            report($ex);
            throw $ex;
        }
    }

    public function verifyCheckoutStatus()
    {
        $cloudFlareService = new CloudFlareService();

        if (!$cloudFlareService->checkHtmlMetadata("https://checkout.cloudfox.net/", "checkout-cloudfox", "1")) {
            // checkout OFF

            // email addresses for notify
            $emails = ["julioleichtweis@gmail.com", "felixlorram@gmail.com"];

            // phone numbers for notify
            $phoneNumbers = ["5555996931098", "5522981071202"];

            $sendgrid = new SendGrid(getenv("SENDGRID_API_KEY"));
            $smsService = new SmsService();

            foreach ($emails as $email) {
                try {
                    $sendgridMail = new Mail();
                    $sendgridMail->setFrom("help@nexuspay.com.br", "cloudfox");
                    $sendgridMail->addTo($email, "cloudfox");
                    $sendgridMail->setTemplateId("not"); // done

                    $response = $sendgrid->send($sendgridMail);
                } catch (Exception $e) {
                    //
                }
            }

            foreach ($phoneNumbers as $phoneNumber) {
                try {
                    $smsService->sendSms($phoneNumber, "Checkout caiu");
                } catch (Exception $e) {
                    //
                }
            }

            return true;
        }
    }

    /**
     * @throws Exception
     */
    public function releasePaymentGetnet($transactionId)
    {
        if (foxutils()->isProduction()) {
            $url = "https://checkout.cloudfox.net/api/payment/releasepaymentgetnet";
        } else {
            $url = env("CHECKOUT_URL", "http://dev.checkout.com.br") . "/api/payment/releasepaymentgetnet";
        }

        $data = [
            "transaction_id" => hashids_encode($transactionId),
        ];

        return $this->runCurl($url, "POST", $data);
    }

    /**
     * @throws Exception
     */
    public function releaseCloudfoxPaymentGetnet($data)
    {
        if (foxutils()->isProduction()) {
            $url = "https://checkout.cloudfox.net/api/payment/releasecloudfoxpaymentgetnet";
        } else {
            $url = env("CHECKOUT_URL", "http://dev.checkout.com.br") . "/api/payment/releasecloudfoxpaymentgetnet";
        }

        return $this->runCurl($url, "POST", $data);
    }

    /**
     * @throws Exception
     */
    public function checkPaymentPix($data)
    {
        if (foxutils()->isProduction()) {
            $url = "https://checkout.cloudfox.net/api/payment/check-payment-pix";
        } else {
            $url = env("CHECKOUT_URL", "http://dev.checkout.com.br") . "/api/payment/check-payment-pix";
        }

        return $this->runCurl($url, "POST", $data);
    }
}
