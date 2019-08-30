<?php

namespace Modules\Core\Services;

use App\Entities\Checkout;
use App\Entities\Domain;
use App\Entities\Log as CheckoutLog;
use App\Entities\Sale;
use App\Entities\UserProject;
use Carbon\Carbon;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Laracasts\Presenter\Exceptions\PresenterException;
use Modules\SalesRecovery\Transformers\SalesRecoveryCardRefusedResource;
use Modules\SalesRecovery\Transformers\SalesRecoveryResource;

class SalesRecoveryService
{
    /**
     * @param $type
     * @param $projectId
     * @param $dateStart
     * @param $dateEnd
     * @return AnonymousResourceCollection|string
     * Verifica tipo de recuperação
     */
    public function verifyType($type, $projectId = null, $dateStart = null, $dateEnd = null)
    {
        if ($type == 1) {
            return $this->getAbandonedCart($projectId, $dateStart, $dateEnd);
        } else if ($type == 2) {
            $paymentMethod = 2; // boleto
            $status        = [3, 5]; // expired

            return $this->getSaleExpiredOrRefused($projectId, $dateStart, $dateEnd, $paymentMethod, $status);
        } else {
            $paymentMethod = 1; // cartao
            $status        = [3]; // refused

            return $this->getSaleExpiredOrRefused($projectId, $dateStart, $dateEnd, $paymentMethod, $status);
        }
    }

    /**
     * @return string
     */
    public function getAllTypes()
    {
        return ''; //TODO FALTA IMPLEMENTAR
    }

    /**
     * @param $projectId
     * @param $dateStart
     * @param $dateEnd
     * @return AnonymousResourceCollection
     * Carrinho abandonado
     */
    public function getAbandonedCart(int $projectId = null, string $dateStart = null, string $dateEnd = null)
    {
        $checkoutModel     = new Checkout();
        $userProjectsModel = new UserProject();

        $abandonedCarts = $checkoutModel->whereIn('status', ['recovered', 'abandoned cart']);
        if (!empty($projectId)) {
            $abandonedCarts->where('project', $projectId);
        } else {
            $userProjects = $userProjectsModel->where([
                                                          ['user', auth()->user()->id],
                                                          ['type', 'producer'],
                                                      ])->pluck('project')->toArray();

            $abandonedCarts->whereIn('project', $userProjects)->with(['projectModel']);
        }

        if (!empty($dateStart) && !empty($dateEnd)) {
            $abandonedCarts->whereBetween('created_at', [$dateStart, $dateEnd]);
        } else {
            if (!empty($dateStart)) {
                $abandonedCarts->whereDate('created_at', '>=', $dateStart);
            }
            if (!empty($dateEnd)) {
                $abandonedCarts->whereDate('created_at', '<', $dateEnd);
            }
        }

        return SalesRecoveryResource::collection($abandonedCarts->orderBy('id', 'DESC')->paginate(10));
    }

    /**
     * @param $projectId
     * @param $dateStart
     * @param $dateEnd
     * @param $paymentMethod
     * @param $status
     * @return AnonymousResourceCollection
     *  Monta Tabela quando for boleto expirado
     */
    public function getSaleExpiredOrRefused(int $projectId = null, string $dateStart = null, string $dateEnd = null, int $paymentMethod, array $status)
    {
        $salesModel        = new Sale();
        $userProjectsModel = new UserProject();

        $salesExpired = $salesModel
            ->select('sales.*', 'checkout.email_sent_amount', 'checkout.sms_sent_amount',
                     'checkout.id_log_session', DB::raw('(plan_sale.amount * plan_sale.plan_value ) AS value'))
            ->leftJoin('plans_sales as plan_sale', function($join) {
                $join->on('plan_sale.sale', '=', 'sales.id');
            })->leftJoin('checkouts as checkout', function($join) {
                $join->on('sales.checkout', '=', 'checkout.id');
            })->leftJoin('clients as client', function($join) {
                $join->on('sales.client', '=', 'client.id');
            })->whereIn('sales.status', $status)->where([
                                                            ['sales.payment_method', $paymentMethod],
                                                        ])->with([
                                                                     'projectModel',
                                                                     'clientModel',
                                                                     'projectModel.domains' => function($query) {
                                                                         $query->where('status', 3)//dominio aprovado
                                                                               ->first();
                                                                     },
                                                                 ]);

        if (!empty($projectId)) {
            $salesExpired->where('sales.project', $projectId);
        } else {
            $userProjects = $userProjectsModel->where([
                                                          ['user', auth()->user()->id],
                                                          ['type', 'producer'],
                                                      ])->pluck('project')->toArray();

            $salesExpired->whereIn('sales.project', $userProjects);
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

        return SalesRecoveryCardRefusedResource::collection($salesExpired->orderBy('sales.id', 'desc')->paginate(10));
    }

    /**
     * @param Checkout $checkout
     * @return array|Factory|View
     * @throws \Exception
     */
    public function getSalesCheckoutDetails(Checkout $checkout)
    {
        $logModel    = new CheckoutLog();
        $domainModel = new Domain();
        $log         = $logModel->where('id_log_session', $checkout->id_log_session)
                                ->orderBy('id', 'DESC')->first();
        $whatsAppMsg = 'Olá ' . $log->name;

        if (!empty($log->telephone)) {
            $log['whatsapp_link'] = "https://api.whatsapp.com/send?phone=55" . preg_replace('/[^0-9]/', '', $log->telephone) . '&text=' . $whatsAppMsg;
            $log->telephone       = FoxUtils::getTelephone($log->telephone);
        } else {
            $log->telephone = 'Numero inválido';
        }

        $checkout['hours']      = with(new Carbon($checkout->created_at))->format('H:i:s');
        $checkout['date']       = with(new Carbon($checkout->created_at))->format('d/m/Y');
        $checkout['total']      = number_format($checkout->present()->getTotal() / 100, 2, ',', '.');
        $checkout->src          = ($checkout->src == 'null' || $checkout->src == null) ? '' : $checkout->src;
        $checkout->utm_source   = ($checkout->utm_source == 'null' || $checkout->utm_source == null) ? '' : $checkout->utm_source;
        $checkout->utm_medium   = ($checkout->utm_medium == 'null' || $checkout->utm_medium == null) ? '' : $checkout->utm_medium;
        $checkout->utm_campaign = ($checkout->utm_campaign == 'null' || $checkout->utm_campaign == null) ? '' : $checkout->utm_campaign;
        $checkout->utm_term     = ($checkout->utm_term == 'null' || $checkout->utm_term == null) ? '' : $checkout->utm_term;
        $checkout->utm_content  = ($checkout->utm_content == 'null' || $checkout->utm_content == null) ? '' : $checkout->utm_content;

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
            $link = 'Dominio removido';
        }

        return [
            'checkout' => $checkout,
            'client'   => $log,
            'products' => $products,
            'status'   => $status,
            'link'     => $link,
        ];
    }

    /**
     * @param Sale $sale
     * @return array
     * @throws PresenterException
     * Modal detalhes quando for cartão recusado ou boleto
     */
    public function getSalesCartOrBoletoDetails(Sale $sale)
    {
        $checkoutModel = new checkout();
        $logModel      = new CheckoutLog();
        $domainModel   = new Domain();

        $checkout = $checkoutModel->find($sale->checkout);
        $delivery = $sale->delivery()->first();
        $client   = $sale->clientModel()->first();

        if (!empty($client->telephone)) {
            $client->telephone       = FoxUtils::getTelephone($client->telephone);
            $whatsAppMsg             = 'Olá ' . $client->name;
            $client['whatsapp_link'] = "https://api.whatsapp.com/send?phone=55" . preg_replace('/[^0-9]/', '', $client->telephone) . '&text=' . $whatsAppMsg;
        } else {
            $client['whatsapp_link'] = '';
            $client->telephone       = 'Numero Inválido';
        }

        $checkout['hours']      = with(new Carbon($sale->created_at))->format('H:i:s');
        $checkout['date']       = with(new Carbon($sale->created_at))->format('d/m/Y');
        $checkout['total']      = number_format($checkout->present()->getTotal() / 100, 2, ',', '.');
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
            $link = 'Dominio removido';
        }

        $products = $sale->present()->getProducts();

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


