<?php

namespace Modules\Core\Services;

use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Modules\Core\Entities\Checkout;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Customer;
use Modules\Core\Entities\Domain;
use Modules\Core\Entities\Log as CheckoutLog;
use Laracasts\Presenter\Exceptions\PresenterException;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\UserProject;
use Vinkla\Hashids\Facades\Hashids;

class SalesRecoveryService
{
    /**
     * @param string $type
     * @param string|null $projectId
     * @param string|null $dateStart
     * @param string|null $dateEnd
     * @param string|null $client
     * @return mixed
     * @throws PresenterException
     */
    public function verifyType(
        string $type,
        string $projectId = null,
        string $dateStart = null,
        string $dateEnd = null,
        string $client = null
    ) {
        if ($type == 2) {
            $paymentMethod = 2; // boleto
            $status = [3, 5]; // expired

            return $this->getSaleExpiredOrRefused($projectId, $dateStart, $dateEnd, $paymentMethod, $status, $client);
        } else {
            $paymentMethod = 1; // cartao
            $status = [3]; // refused

            return $this->getSaleExpiredOrRefused($projectId, $dateStart, $dateEnd, $paymentMethod, $status, $client);
        }
    }

    /**
     * @param int $paymentMethod
     * @param array $status
     * @param string $projectId
     * @param string|null $dateStart
     * @param string|null $dateEnd
     * @param string|null $customer
     * @param string|null $customerDocument
     * @param string|null $plan
     * @return mixed
     * @throws PresenterException
     */
    public function getSaleExpiredOrRefused(
        int $paymentMethod,
        array $status,
        array $projectIds,
        string $dateStart = null,
        string $dateEnd = null,
        string $customer = null,
        string $customerDocument = null,
        array $plans = null,
        int $company_id = null
    ) {

        $userProjectsModel = new UserProject();
        $customerModel = new Customer();

        $salesExpired = Sale::select('sales.*', 'checkout.email_sent_amount', 'checkout.sms_sent_amount', 'checkout.id as checkout_id',
                'checkout.id_log_session', DB::raw('(plan_sale.amount * plan_sale.plan_value ) AS value'))
            ->leftJoin('plans_sales as plan_sale', function ($join) {
                $join->on('plan_sale.sale_id', '=', 'sales.id');
            })->leftJoin('checkouts as checkout', function ($join) {
                $join->on('sales.checkout_id', '=', 'checkout.id');
            })->leftJoin('customers as customer', function ($join) {
                $join->on('sales.customer_id', '=', 'customer.id');
            // })->leftJoin('checkout_configs as checkout_config', function ($join) {
            //     $join->on('sales.project_id', '=', 'checkout_config.project_id');
            // })
            // ->where('checkout_config.company_id', $company_id)
            })->leftJoin('transactions as transaction', function ($join) {
                $join->on('sales.id', '=', 'transaction.sale_id');
            })
            ->where('transaction.company_id', $company_id)
            ->whereIn('sales.status', $status)
            ->where('sales.payment_method', $paymentMethod)
            ->with([
                "project",
                "customer",
                "project.domains" => function ($query) {
                    $query
                        ->where("status", 3) //dominio aprovado
                        ->first();
                },
            ]);

        if (!empty($customer)) {
            $customerSearch = $customerModel
                ->where("name", "like", "%" . $customer . "%")
                ->pluck("id")
                ->toArray();
            $salesExpired->whereIn("sales.customer_id", $customerSearch);
        }

        if (!empty($customerDocument)) {
            $customerSearch = $customerModel
                ->where("document", foxutils()->onlyNumbers($customerDocument))
                ->pluck("id");
            $salesExpired->whereIn("sales.customer_id", $customerSearch);
        }

        if (!empty($plans)) {
            $plansIds = collect($plans)
                ->map(function ($plan) {
                    return hashids_decode($plan);
                })
                ->toArray();

            $salesExpired->whereHas("plansSales", function ($query) use ($plansIds) {
                $query->whereIn("plan_id", $plansIds);
            });
        }

        $tokensIds = [];
        if (!empty($projectIds) && !in_array("all", $projectIds)) {
            foreach ($projectIds as $key=>$projectId) {
                if(str_starts_with($projectId,'TOKEN')){
                    $tokensIds[] = str_replace('TOKEN-','',$projectId);
                    unset($projectIds[$key]);
                }
            }
        } else {
            $projectIds = $userProjectsModel->where([
                ['user_id', auth()->user()->getAccountOwnerId()],
                [
                    'type_enum',UserProject::TYPE_PRODUCER_ENUM,
                ],
            ])->pluck('project_id')->toArray();
        }

        $salesExpired->where(function($qr) use($projectIds,$tokensIds){
            $qr->whereIn("sales.project_id", $projectIds)
            ->orWhere("sales.api_token_id",$tokensIds);
        });

        if (!empty($dateStart) && !empty($dateEnd)) {
            $salesExpired->whereBetween("sales.created_at", [$dateStart, $dateEnd]);
        } else {
            if (!empty($dateStart)) {
                $salesExpired->whereDate("sales.created_at", ">=", $dateStart);
            }
            if (!empty($dateEnd)) {
                $salesExpired->whereDate("sales.created_at", "<", $dateEnd);
            }
        }

        return $salesExpired->orderBy("sales.id", "desc")->paginate(10);
    }

    /**
     * @param Checkout $checkout
     * @return array|Factory|View
     * @throws Exception
     * Modal detalhes quando for carrinho abandonado
     */
    public function getSalesCheckoutDetails(Checkout $checkout)
    {
        $logModel = new CheckoutLog();
        $domainModel = new Domain();
        $log = $logModel
            ->where("checkout_id", $checkout->id)
            ->orderBy("id", "DESC")
            ->first();
        $whatsAppMsg = "Olá " . explode(" ", $log->name)[0];

        if (!empty($log->telephone)) {
            $log["whatsapp_link"] =
                "https://api.whatsapp.com/send?phone=55" .
                preg_replace("/[^0-9]/", "", $log->telephone) .
                "&text=" .
                $whatsAppMsg;
            $log->telephone = foxutils()->getTelephone($log->telephone);
        } else {
            $log->telephone = "Numero inválido";
        }

        $checkout["hours"] = with(new Carbon($checkout->created_at))->format("H:i:s");
        $checkout["date"] = with(new Carbon($checkout->created_at))->format("d/m/Y");
        $checkout["total"] = number_format($checkout->present()->getSubTotal() / 100, 2, ",", ".");
        $checkout->src = $checkout->src == "null" || $checkout->src == null ? "" : $checkout->src;
        $checkout->utm_source =
            $checkout->utm_source == "null" || $checkout->utm_source == null ? "" : $checkout->utm_source;
        $checkout->utm_medium =
            $checkout->utm_medium == "null" || $checkout->utm_medium == null ? "" : $checkout->utm_medium;
        $checkout->utm_campaign =
            $checkout->utm_campaign == "null" || $checkout->utm_campaign == null ? "" : $checkout->utm_campaign;
        $checkout->utm_term = $checkout->utm_term == "null" || $checkout->utm_term == null ? "" : $checkout->utm_term;
        $checkout->utm_content =
            $checkout->utm_content == "null" || $checkout->utm_content == null ? "" : $checkout->utm_content;

        $delivery["city"] = $log->city;
        $delivery["street"] = $log->street;
        $delivery["zip_code"] = $log->zip_code;
        $delivery["state"] = $log->state;

        $status = "Recuperado";
        if ($checkout->status == "abandoned cart") {
            $status = "Não recuperado";
        }

        $checkout->browser = $checkout->browser == "null" || $checkout->browser == null ? "" : $checkout->browser;
        $checkout->operational_system =
            $checkout->operational_system == "null" || $checkout->operational_system == null
                ? ""
                : $checkout->operational_system;
        $checkout->is_mobile = $checkout->is_mobile == 1 ? "Dispositivo: Celular" : "Dispositivo: Computador";

        $products = $checkout->present()->getProducts();

        $domain = $domainModel->where([["status", 3], ["project_id", $checkout->project_id]])->first();

        $link = isset($domain) ? 'https://checkout.' . $domain->name : '';
        if(!foxutils()->isProduction()) {
            $link = env('CHECKOUT_URL', 'http://dev.checkout.com.br');
        }

        $user = Auth::user();
        if($user->company_default==Company::DEMO_ID){
            $link = "https://demo.cloudfox.net";
        }

        if(empty($link)){
            $link = 'Domínio removido';
            goto jump;
        }

        $link.= '/recovery/' . Hashids::encode($checkout->id);

        jump:

        $checkout->id = "";
        $log->id = "";

        return [
            "checkout" => $checkout,
            "client" => $log,
            "products" => $products,
            "delivery" => $delivery,
            "status" => $status,
            "link" => $link,
        ];
    }

    /**
     * @param Sale $sale
     * @return array
     * @throws Exception
     * Modal detalhes quando for cartão recusado ou boleto
     */
    public function getSalesCartOrBoletoDetails(Sale $sale)
    {
        $logModel = new CheckoutLog();
        $domainModel = new Domain();
        $saleService = new SaleService();
        $delivery = $sale->delivery;
        $customer = $sale->customer;
        $checkout = $sale->checkout??Checkout::find($sale->checkout_id);

        if (!empty($customer->telephone)) {
            $customer->telephone = preg_replace("/[^0-9]/", "", $customer->telephone);
            $whatsAppMsg = "Olá " . $customer->present()->getFirstName();
            $customer->whatsapp_link =
                "https://api.whatsapp.com/send?phone=" . $customer->telephone . "&text=" . $whatsAppMsg;
        } else {
            $customer->whatsapp_link = "";
            $customer->telephone = "Numero Inválido";
        }

        $checkout->sale_id = hashids_encode($sale->id, "sale_id");

        $checkout->hours = with(new Carbon($sale->created_at))->format("H:i:s");
        $checkout->date = with(new Carbon($sale->created_at))->format("d/m/Y");
        $checkout->total = number_format($checkout->present()->getSubTotal() / 100, 2, ",", ".");
        $checkout->src = $checkout->src == "null" || $checkout->src == null ? "" : $checkout->src;
        $checkout->utm_source =
            $checkout->utm_source == "null" || $checkout->utm_source == null ? "" : $checkout->utm_source;
        $checkout->utm_medium =
            $checkout->utm_medium == "null" || $checkout->utm_medium == null ? "" : $checkout->utm_medium;
        $checkout->utm_campaign =
            $checkout->utm_campaign == "null" || $checkout->utm_campaign == null ? "" : $checkout->utm_campaign;
        $checkout->utm_term = $checkout->utm_term == "null" || $checkout->utm_term == null ? "" : $checkout->utm_term;
        $checkout->utm_content =
            $checkout->utm_content == "null" || $checkout->utm_content == null ? "" : $checkout->utm_content;

        $checkout->browser = $checkout->browser == "null" || $checkout->browser == null ? "" : $checkout->browser;
        $checkout->operational_system =
            $checkout->operational_system == "null" || $checkout->operational_system == null
                ? ""
                : $checkout->operational_system;

        $checkout->is_mobile = $checkout->is_mobile == 1 ? "Dispositivo: Celular" : "Dispositivo: Computador";

        if ($sale->payment_method == Sale::PAYMENT_TYPE_BANK_SLIP) {
            $customer->error = "Não pago até a data do vencimento";
        } else {
            $log = $logModel
                ->where("checkout_id", $checkout->id)
                ->where("event", "=", "payment error")
                ->orderBy("id", "DESC")
                ->first();

            if (empty($log->error)) {
                $customer->error = "Saldo insuficiente!";
                if($sale->payment_method == Sale::PAYMENT_TYPE_PIX){
                    $customer->error = "Expirado!";
                }
            } elseif ($log->error == "CARTÃO RECUSADO !") {
                $customer->error = $log->error . " (saldo insuficiente)";
            } else {
                $customer->error = $log->error;
            }
        }

        if ($checkout->status != "recovered") {
            if ($sale->payment_method == 1 || $sale->payment_method == 3) {
                $status = "Recusado";
            } else {
                $status = "Expirado";
            }
        } else {
            $status = "Recuperado";
        }

        $domain = $domainModel
            ->where("project_id", $sale->project_id)
            ->where("status", $domainModel->present()->getStatus("approved"))
            ->first();

        $link = isset($domain) ? 'https://checkout.' . $domain->name : '';
        if(!foxutils()->isProduction()) {
            $link = env('CHECKOUT_URL', 'http://dev.checkout.com.br');
        }

        $user = Auth::user();
        if($user->company_default==Company::DEMO_ID){
            $link = "https://demo.cloudfox.net";
        }

        if(empty($link)){
            $link = 'Domínio removido';
            goto jump;
        }

        if($sale->payment_method === Sale::PIX_PAYMENT)
        {
            $link.='/pix/' . Hashids::connection('sale_id')->encode($this->id);
            goto jump;
        }

        $link.= '/recovery/' . Hashids::encode($this->checkout_id);

        jump:

        $products = $saleService->getProducts($checkout->sale_id);

        $customer->document = foxutils()->getDocument($customer->document);

        if (!empty($delivery)) {
            $delivery->zip_code = foxutils()->getCep($delivery->zip_code);
        }

        return [
            "checkout" => $checkout,
            "client" => $customer,
            "products" => $products,
            "delivery" => $delivery,
            "status" => $status,
            "link" => $link,
        ];
    }

    public static function getProjectsWithRecovery(){
        $first = Sale::select('project_id')
            ->distinct()
            ->where('owner_id',auth()->user()->getAccountOwnerId())
            ->where('status',3);

        $s = Checkout::select('checkouts.project_id')
            ->distinct()
            ->leftjoin('checkout_configs','checkout_configs.project_id','checkouts.project_id')
            ->leftjoin('companies','companies.id','checkout_configs.company_id')
            ->leftjoin('affiliates','affiliates.id','checkouts.affiliate_id')
            ->where(function($query) {
                $query
                ->where('affiliates.user_id', auth()->user()->getAccountOwnerId())
                ->orWhere('companies.user_id',auth()->user()->getAccountOwnerId());
            })
            ->where('checkouts.status_enum',2)
            ->union($first)
            ->get();
        return $s;
    }
}
