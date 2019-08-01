<?php

namespace Modules\Core\Services\SalesRecovery;

use App\Entities\Checkout;
use App\Entities\CheckoutPlan;
use App\Entities\Domain;
use App\Entities\Log as CheckoutLog;
use App\Entities\Log;
use App\Entities\Sale;
use App\Entities\UserProject;
use Carbon\Carbon;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Modules\Core\Services\FoxUtils;
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
     */
    public function verifyType(int $type, int $projectId = null, string $dateStart = null, string $dateEnd = null)
    {
        if ($type == 1) {
            return $this->getAbandonedCart($projectId, $dateStart, $dateEnd);
        } else if ($type == 2) {
            $paymentMethod = 2; // boleto
            $status        = 3; // expired

            return $this->getSaleExpiredOrRefused($projectId, $dateStart, $dateEnd, $paymentMethod, $status);
        } else if ($type == 3) {
            $paymentMethod = 1; // cartao
            $status        = 3; // refused

            return $this->getSaleExpiredOrRefused($projectId, $dateStart, $dateEnd, $paymentMethod, $status);
        } else {
            return $this->getAllTypes();
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
     * Carrinho abandonado e recuperado
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

        $abandonedCarts->orderBy('id', 'DESC');

        return SalesRecoveryResource::collection($abandonedCarts->paginate(10));
    }

    /**
     * @param $projectId
     * @param $dateStart
     * @param $dateEnd
     * @param $paymentMethod
     * @param $status
     * @return AnonymousResourceCollection
     */
    public function getSaleExpiredOrRefused(int $projectId = null, string $dateStart = null, string $dateEnd = null, int $paymentMethod, int $status)
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
            })->where([
                          ['sales.payment_method', $paymentMethod], ['sales.status', $status],
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

        $salesExpired->orderBy('id', 'DESC');

        return SalesRecoveryCardRefusedResource::collection($salesExpired->paginate(10));
    }

    /**
     * @param Checkout $checkout
     * @return Factory|View
     * @throws \Exception
     */
    public function getSalesCheckoutDetails(Checkout $checkout)
    {
        $logModel          = new CheckoutLog();
        $checkoutPlanModel = new CheckoutPlan();
        $domainModel       = new Domain();
        $log               = $logModel->where('id_log_session', $checkout->id_log_session)
                                      ->orderBy('id', 'DESC')->first();
        $telephone         = FoxUtils::prepareCellPhoneNumber($log->telephone);
        if (!empty($telephone)) {
            $log->telephone = $telephone;
        } else {
            $log->telephone = 'Numero Inválido';
        }

        $checkout['hours']      = with(new Carbon($checkout->created_at))->format('H:i:s');
        $checkout['date']       = with(new Carbon($checkout->created_at))->format('d/m/Y');
        $checkout->is_mobile    = ($checkout->is_mobile == 1) ? 'Mobile' : 'Computador';
        $checkout->src          = ($checkout->src == 'null') ? '' : $checkout->src;
        $checkout->utm_source   = ($checkout->utm_source == 'null') ? '' : $checkout->utm_source;
        $checkout->utm_medium   = ($checkout->utm_medium == 'null') ? '' : $checkout->utm_medium;
        $checkout->utm_campaign = ($checkout->utm_campaign == 'null') ? '' : $checkout->utm_campaign;
        $checkout->utm_term     = ($checkout->utm_term == 'null') ? '' : $checkout->utm_term;
        $checkout->utm_content  = ($checkout->utm_content == 'null') ? '' : $checkout->utm_content;

        $status = '';
        if ($checkout->status == 'abandoned cart') {
            $status = 'Não recuperado';
        } else {
            $status = 'Recuperado';
        }

        $checkout->is_mobile = ($checkout->is_mobile == 1) ? 'Mobile' : 'Computador';

        $checkoutPlans = $checkoutPlanModel->with('plan', 'plan.products')
                                           ->where('checkout', $checkout->id)
                                           ->get();

        $plans = [];
        $total = 0;
        foreach ($checkoutPlans as $checkoutPlan) {
            foreach ($checkoutPlan->getRelation('plan')->products as $key => $product) {
                $plans[$key]['name']   = $checkoutPlan->getRelation('plan')->name;
                $plans[$key]['value']  = $checkoutPlan->getRelation('plan')->price;
                $plans[$key]['photo']  = $product->photo;
                $plans[$key]['amount'] = $checkoutPlan->amount;
                $total                 += intval(preg_replace("/[^0-9]/", "", $checkoutPlan->getRelation('plan')->price)) * intval($checkoutPlan->amount);
            }
        }

        $domain = $domainModel->where([['status', 3], ['project_id', $checkout->project]])->first();
        if (!empty($domain)) {
            $link = "https://checkout." . $domain->name . "/recovery/" . $checkout->id_log_session;
        } else {
            $link = 'Dominio removido';
        }

        $whatsAppMsg = 'Olá ' . $log->name;

        return view('salesrecovery::details', [
            'checkout'      => $checkout,
            'client'        => $log,
            'whatsapp_link' => "https://api.whatsapp.com/send?phone=55" . preg_replace('/[^0-9]/', '', $log->telephone) . '&text=' . $whatsAppMsg,
            'status'        => $status,
            'hours'         => $checkout['hours'],
            'date'          => $checkout['date'],
            'plans'         => $plans,
            'total'         => number_format(intval($total) / 100, 2, ',', '.'),
            'link'          => $link,
        ]);
    }

    /**
     * @param int $saleId
     * @return Factory|View
     * @throws \Exception
     */
    public function getSalesCartOrBoletoDetails(int $saleId)
    {
        $salesModel        = new Sale();
        $checkoutModel     = new Checkout();
        $domainModel       = new Domain();
        $checkoutPlanModel = new CheckoutPlan();
        $logModel          = new Log();

        $sale      = $salesModel->with(['clientModel', 'delivery'])->find($saleId);
        $client    = $sale->getRelation('clientModel');
        $telephone = FoxUtils::prepareCellPhoneNumber($client->telephone);
        if (!empty($telephone)) {
            $client->telephone = $telephone;
        } else {
            $client->telephone = 'Numero Inválido';
        }
        $client->street   = $sale->getRelation('delivery')->street;
        $client->zip_code = $sale->getRelation('delivery')->zip_code;
        $client->city     = $sale->getRelation('delivery')->city;
        $client->state    = $sale->getRelation('delivery')->state;

        $checkout               = $checkoutModel->where('id', $sale->checkout)->first();
        $checkout['hours']      = with(new Carbon($checkout->created_at))->format('H:i:s');
        $checkout['date']       = with(new Carbon($checkout->created_at))->format('d/m/Y');
        $checkout->is_mobile    = ($checkout->is_mobile == 1) ? 'Mobile' : 'Computador';
        $checkout->src          = ($checkout->src == 'null') ? '' : $checkout->src;
        $checkout->utm_source   = ($checkout->utm_source == 'null') ? '' : $checkout->utm_source;
        $checkout->utm_medium   = ($checkout->utm_medium == 'null') ? '' : $checkout->utm_medium;
        $checkout->utm_campaign = ($checkout->utm_campaign == 'null') ? '' : $checkout->utm_campaign;
        $checkout->utm_term     = ($checkout->utm_term == 'null') ? '' : $checkout->utm_term;
        $checkout->utm_content  = ($checkout->utm_content == 'null') ? '' : $checkout->utm_content;

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

        if ($sale->payment_method == 1) {
            $status = 'Recusado';
        } else {
            $status = 'Expirado';
        }

        $checkoutPlans = $checkoutPlanModel->with('plan', 'plan.products')
                                           ->where('checkout', $checkout->id)
                                           ->get();

        $plans = [];
        $total = 0;
        foreach ($checkoutPlans as $checkoutPlan) {
            foreach ($checkoutPlan->getRelation('plan')->products as $key => $product) {
                $plans[$key]['name']   = $checkoutPlan->getRelation('plan')->name;
                $plans[$key]['value']  = $checkoutPlan->getRelation('plan')->price;
                $plans[$key]['photo']  = $product->photo;
                $plans[$key]['amount'] = $checkoutPlan->amount;
                $total                 += intval(preg_replace("/[^0-9]/", "", $checkoutPlan->getRelation('plan')->price)) * intval($checkoutPlan->amount);
            }
        }

        $domain = $domainModel->where([['status', 3], ['project_id', $checkout->project]])->first();
        if (!empty($domain)) {
            $link = "https://checkout." . $domain->name . "/recovery/" . $checkout->id_log_session;
        } else {
            $link = 'Dominio removido';
        }

        $whatsAppMsg = 'Olá ' . $client->name;

        return view('salesrecovery::details', [
            'checkout'      => $checkout,
            'client'        => $client,
            'whatsapp_link' => "https://api.whatsapp.com/send?phone=55" . preg_replace('/[^0-9]/', '', $client->telephone) . '&text=' . $whatsAppMsg,
            'status'        => $status,
            'hours'         => $checkout['hours'],
            'date'          => $checkout['date'],
            'plans'         => $plans,
            'total'         => number_format(intval($total) / 100, 2, ',', '.'),
            'link'          => $link,
        ]);
    }
}
