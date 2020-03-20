<?php

namespace Modules\Core\Services;

use Carbon\Carbon;
use Illuminate\View\View;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\Customer;
use Modules\Core\Entities\Domain;
use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\Checkout;
use Illuminate\Contracts\View\Factory;
use Modules\Core\Entities\UserProject;
use Modules\Core\Entities\Log as CheckoutLog;
use Laracasts\Presenter\Exceptions\PresenterException;
use Modules\SalesRecovery\Transformers\SalesRecoveryResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Modules\SalesRecovery\Transformers\SalesRecoveryCardRefusedResource;
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
    public function verifyType(string $type, string $projectId = null, string $dateStart = null, string $dateEnd = null, string $client = null)
    {
        if ($type == 2) {
            $paymentMethod = 2; // boleto
            $status        = [3, 5]; // expired

            return $this->getSaleExpiredOrRefused($projectId, $dateStart, $dateEnd, $paymentMethod, $status, $client);
        } else {
            $paymentMethod = 1; // cartao
            $status        = [3]; // refused

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
     * @return mixed
     * @throws PresenterException
     */
    public function getSaleExpiredOrRefused(int $paymentMethod, array $status, string $projectId, string $dateStart = null, string $dateEnd = null, string $customer = null)
    {
        $salesModel        = new Sale();
        $userProjectsModel = new UserProject();
        $customerModel     = new Customer();

        $salesExpired = $salesModel
            ->select('sales.*', 'checkout.email_sent_amount', 'checkout.sms_sent_amount',
                     'checkout.id_log_session', DB::raw('(plan_sale.amount * plan_sale.plan_value ) AS value'))
            ->leftJoin('plans_sales as plan_sale', function($join) {
                $join->on('plan_sale.sale_id', '=', 'sales.id');
            })->leftJoin('checkouts as checkout', function($join) {
                $join->on('sales.checkout_id', '=', 'checkout.id');
            })->leftJoin('customers as customer', function($join) {
                $join->on('sales.customer_id', '=', 'customer.id');
            })->whereIn('sales.status', $status)->where([
                                                            ['sales.payment_method', $paymentMethod],
                                                        ])->with([
                                                                     'project',
                                                                     'customer',
                                                                     'project.domains' => function($query) {
                                                                         $query->where('status', 3)//dominio aprovado
                                                                               ->first();
                                                                     },
                                                                 ]);

        if (!empty($customer)) {
            $customerSearch = $customerModel->where('name', 'like', '%' . $customer . '%')->pluck('id')->toArray();
            $salesExpired->whereIn('sales.customer_id', $customerSearch);
        }

        if (!empty($projectId)) {
            $salesExpired->where('sales.project_id', $projectId);
        } else {
            $userProjects = $userProjectsModel->where([
                                                          ['user_id', auth()->user()->account_owner_id],
                                                          ['type_enum', $userProjectsModel->present()->getTypeEnum('producer')],
                                                      ])->pluck('project_id')->toArray();

            $salesExpired->whereIn('sales.project_id', $userProjects);
        }

        if (!empty($dateStart) && !empty($dateEnd)) {
            $salesExpired->whereBetween('sales.created_at', [$dateStart, $dateEnd]);
        } else {
            if (!empty($dateStart)) {
                $salesExpired->whereDate('sales.created_at', '>=', $dateStart);
            }
            if (!empty($dateEnd)) {
                $salesExpired->whereDate('sales.created_at', '<', $dateEnd);
            }
        }

        return $salesExpired->orderBy('sales.id', 'desc')->paginate(10);
    }

    /**
     * @param Checkout $checkout
     * @return array|Factory|View
     * @throws \Exception
     * Modal detalhes quando for carrinho abandonado
     */
    public function getSalesCheckoutDetails(Checkout $checkout)
    {
        $logModel    = new CheckoutLog();
        $domainModel = new Domain();
        $log         = $logModel->where('checkout_id', $checkout->id)
                                ->orderBy('id', 'DESC')->first();
        $whatsAppMsg = 'Olá ' . explode(' ', $log->name)[0];

        if (!empty($log->telephone)) {
            $log['whatsapp_link'] = "https://api.whatsapp.com/send?phone=55" . preg_replace('/[^0-9]/', '', $log->telephone) . '&text=' . $whatsAppMsg;
            $log->telephone       = FoxUtils::getTelephone($log->telephone);
        } else {
            $log->telephone = 'Numero inválido';
        }

        $checkout['hours']      = with(new Carbon($checkout->created_at))->format('H:i:s');
        $checkout['date']       = with(new Carbon($checkout->created_at))->format('d/m/Y');
        $checkout['total']      = number_format($checkout->present()->getSubTotal() / 100, 2, ',', '.');
        $checkout->src          = ($checkout->src == 'null' || $checkout->src == null) ? '' : $checkout->src;
        $checkout->utm_source   = ($checkout->utm_source == 'null' || $checkout->utm_source == null) ? '' : $checkout->utm_source;
        $checkout->utm_medium   = ($checkout->utm_medium == 'null' || $checkout->utm_medium == null) ? '' : $checkout->utm_medium;
        $checkout->utm_campaign = ($checkout->utm_campaign == 'null' || $checkout->utm_campaign == null) ? '' : $checkout->utm_campaign;
        $checkout->utm_term     = ($checkout->utm_term == 'null' || $checkout->utm_term == null) ? '' : $checkout->utm_term;
        $checkout->utm_content  = ($checkout->utm_content == 'null' || $checkout->utm_content == null) ? '' : $checkout->utm_content;

        $delivery['city'] = $log->city;

        $delivery['street'] = $log->street;

        $delivery['zip_code'] = $log->zip_code;

        $delivery['state'] = $log->state;

        $status = '';
        if ($checkout->status == 'abandoned cart') {
            $status = 'Não recuperado';
        } else {
            $status = 'Recuperado';
        }

        $checkout->browser            = ($checkout->browser == 'null' || $checkout->browser == null) ? '' : $checkout->browser;
        $checkout->operational_system = ($checkout->operational_system == 'null' || $checkout->operational_system == null) ? '' : $checkout->operational_system;
        $checkout->is_mobile          = $checkout->is_mobile == 1 ? 'Dispositivo: Celular' : 'Dispositivo: Computador';

        $products = $checkout->present()->getProducts();

        $domain = $domainModel->where([
                                          ['status', 3],
                                          ['project_id', $checkout->project_id],
                                      ])->first();

        if (!empty($domain)) {
            $link = "https://checkout." . $domain->name . "/recovery/" . $checkout->id_log_session;
        } else {
            $link = 'Domínio removido';
        }
        $checkout->id = '';
        $log->id      = '';

        return [
            'checkout' => $checkout,
            'client'   => $log,
            'products' => $products,
            'delivery' => $delivery,
            'status'   => $status,
            'link'     => $link,
        ];
    }

    /**
     * @param Sale $sale
     * @return array
     * @throws \Exception
     * Modal detalhes quando for cartão recusado ou boleto
     */
    public function getSalesCartOrBoletoDetails(Sale $sale)
    {
        $checkoutModel = new checkout();
        $logModel      = new CheckoutLog();
        $domainModel   = new Domain();
        $saleService   = new SaleService();

        $checkout = $checkoutModel->find($sale->checkout_id);
        $delivery = $sale->delivery()->first();
        $customer = $sale->customer()->first();

        if (!empty($customer->telephone)) {
            $customer->telephone       = preg_replace('/[^0-9]/', '', $customer->telephone);
            $whatsAppMsg               = 'Olá ' . $customer->present()->getFirstName();
            $customer['whatsapp_link'] = "https://api.whatsapp.com/send?phone=" . $customer->telephone . '&text=' . $whatsAppMsg;
        } else {
            $customer['whatsapp_link'] = '';
            $customer->telephone       = 'Numero Inválido';
        }

        $checkout['sale_id'] = Hashids::connection('sale_id')->encode($sale->id);

        $checkout['hours']      = with(new Carbon($sale->created_at))->format('H:i:s');
        $checkout['date']       = with(new Carbon($sale->created_at))->format('d/m/Y');
        $checkout['total']      = number_format($checkout->present()->getSubTotal() / 100, 2, ',', '.');
        $checkout->src          = ($checkout->src == 'null' || $checkout->src == null) ? '' : $checkout->src;
        $checkout->utm_source   = ($checkout->utm_source == 'null' || $checkout->utm_source == null) ? '' : $checkout->utm_source;
        $checkout->utm_medium   = ($checkout->utm_medium == 'null' || $checkout->utm_medium == null) ? '' : $checkout->utm_medium;
        $checkout->utm_campaign = ($checkout->utm_campaign == 'null' || $checkout->utm_campaign == null) ? '' : $checkout->utm_campaign;
        $checkout->utm_term     = ($checkout->utm_term == 'null' || $checkout->utm_term == null) ? '' : $checkout->utm_term;
        $checkout->utm_content  = ($checkout->utm_content == 'null' || $checkout->utm_content == null) ? '' : $checkout->utm_content;

        $checkout->browser            = ($checkout->browser == 'null' || $checkout->browser == null) ? '' : $checkout->browser;
        $checkout->operational_system = ($checkout->operational_system == 'null' || $checkout->operational_system == null) ? '' : $checkout->operational_system;

        $checkout->is_mobile = $checkout->is_mobile == 1 ? 'Dispositivo: Celular' : 'Dispositivo: Computador';

        if ($sale->payment_method == 2) {
            $customer->error = 'Não pago até a data do vencimento';
        } else {
            $log = $logModel->where('checkout_id', $checkout->id)
                            ->where('event', '=', 'payment error')
                            ->orderBy('id', 'DESC')
                            ->first();

            if (empty($log->error)) {
                $customer->error = 'Saldo insuficiente!';
            } else if ($log->error == 'CARTÃO RECUSADO !') {
                $customer->error = $log->error . ' (saldo insuficiente)';
            } else {
                $customer->error = $log->error;
            }
        }

        if ($checkout->status != 'recovered') {
            if ($sale->payment_method == 1 || $sale->payment_method == 3) {
                $status = 'Recusado';
            } else {
                $status = 'Expirado';
            }
        } else {
            $status = 'Recuperado';
        }

        $domain = $domainModel->where([
                                          ['status', 3],
                                          ['project_id', $sale->project],
                                      ])->first();

        if (!empty($domain)) {
            $link = "https://checkout." . $domain->name . "/recovery/" . $checkout->id_log_session;
        } else {
            $link = 'Domínio removido';
        }

        $products = $saleService->getProducts($checkout['sale_id']);

        $customer->document = FoxUtils::getDocument($customer->document);

        $delivery->zip_code = FoxUtils::getCep($delivery->zip_code);

        return [
            'checkout' => $checkout,
            'client'   => $customer,
            'products' => $products,
            'delivery' => $delivery,
            'status'   => $status,
            'link'     => $link,
        ];
    }
}


