<?php

namespace Modules\Core\Services;

use Carbon\Carbon;
use Illuminate\View\View;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\Client;
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
     * @param string $projectId
     * @param string $dateStart
     * @param string $dateEnd
     * @param string $client
     * @return AnonymousResourceCollection|string
     * Verifica tipo de recuperação
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
     * @param string $projectId
     * @param string $dateStart
     * @param string $dateEnd
     * @param int $paymentMethod
     * @param array $status
     * @param string|null $client
     * @return AnonymousResourceCollection
     *  Monta Tabela quando for boleto expirado ou cartão recusado
     */
    public function getSaleExpiredOrRefused(int $paymentMethod, array $status, string $projectId, string $dateStart = null, string $dateEnd = null, string $client = null)
    {
        $salesModel        = new Sale();
        $userProjectsModel = new UserProject();
        $clientModel       = new Client();

        $salesExpired = $salesModel
            ->select('sales.*', 'checkout.email_sent_amount', 'checkout.sms_sent_amount',
                     'checkout.id_log_session', DB::raw('(plan_sale.amount * plan_sale.plan_value ) AS value'))
            ->leftJoin('plans_sales as plan_sale', function($join) {
                $join->on('plan_sale.sale_id', '=', 'sales.id');
            })->leftJoin('checkouts as checkout', function($join) {
                $join->on('sales.checkout_id', '=', 'checkout.id');
            })->leftJoin('clients as client', function($join) {
                $join->on('sales.client_id', '=', 'client.id');
            })->whereIn('sales.status', $status)->where([
                                                            ['sales.payment_method', $paymentMethod],
                                                        ])->with([
                                                                     'project',
                                                                     'client',
                                                                     'project.domains' => function($query) {
                                                                         $query->where('status', 3)//dominio aprovado
                                                                               ->first();
                                                                     },
                                                                 ]);

        if (!empty($client)) {
            $clientSearch = $clientModel->where('name', 'like', '%' . $client . '%')->first();
            if (!empty($clientSearch)) {
                $salesExpired->where('sales.client_id', $clientSearch->id);
            }
        }

        if (!empty($projectId)) {
            $salesExpired->where('sales.project_id', $projectId);
        } else {
            $userProjects = $userProjectsModel->where([
                                                          ['user_id', auth()->user()->account_owner],
                                                          ['type', 'producer'],
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
        $log         = $logModel->where('id_log_session', $checkout->id_log_session)
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
                                          ['project_id', $checkout->project],
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
        $client   = $sale->client()->first();

        if (!empty($client->telephone)) {
            $client->telephone       = FoxUtils::getTelephone($client->telephone);
            $whatsAppMsg             = 'Olá ' . $client->present()->getFirstName();
            $client['whatsapp_link'] = "https://api.whatsapp.com/send?phone=55" . preg_replace('/[^0-9]/', '', $client->telephone) . '&text=' . $whatsAppMsg;
        } else {
            $client['whatsapp_link'] = '';
            $client->telephone       = 'Numero Inválido';
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
            $client->error = 'Não pago até a data do vencimento';
        } else {
            $log = $logModel->where('id_log_session', $checkout->id_log_session)
                            ->where('event', '=', 'payment error')
                            ->orderBy('id', 'DESC')
                            ->first();

            if ($log->error == 'CARTÃO RECUSADO !') {
                $client->error = $log->error . ' (saldo insuficiente)';
            } else {
                $client->error = $log->error;
            }
        }

        if ($checkout->status != 'recovered') {
            if ($sale->payment_method == 1) {
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

        $client->document = FoxUtils::getDocument($client->document);

        $delivery->zip_code = FoxUtils::getCep($delivery->zip_code);

        return [
            'checkout' => $checkout,
            'client'   => $client,
            'products' => $products,
            'delivery' => $delivery,
            'status'   => $status,
            'link'     => $link,
        ];
    }
}


