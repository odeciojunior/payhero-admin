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
use Modules\SalesRecovery\Transformers\SalesRecoveryCardRefusedResource;
use Modules\SalesRecovery\Transformers\SalesRecoveryResource;

//use Modules\SalesRecovery\Transformers\SalesRecoveryCardRefused;

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
            $paymentMethod = 1; // cartao
            $status        = 3; // refused

            return $this->getSaleExpiredOrRefused($projectId, $dateStart, $dateEnd, $paymentMethod, $status);
        } else if ($type == 3) {
            $paymentMethod = 2; // boleto
            $status        = 3; // expired

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
                                      ->orderBy('id', 'DESC')
                                      ->first();
        $log['hours']      = with(new Carbon($checkout->created_at))->format('H:i:s');
        $log['date']       = with(new Carbon($checkout->created_at))->format('d/m/Y');

        $status = '';
        if ($checkout->status == 'abandoned cart') {
            $status = 'Não recuperado';
        } else {
            $status = 'Recuperado';
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

        $link = "https://checkout." . $domain->name . "/recovery/" . $checkout->id_log_session;

        $whatsAppMsg = 'Olá ' . $log->name;

        return view('salesrecovery::details', [
            'checkout'      => $checkout,
            'log'           => $log,
            'whatsapp_link' => "https://api.whatsapp.com/send?phone=55" . preg_replace('/[^0-9]/', '', $log->telephone) . '&text=' . $whatsAppMsg,
            'status'        => $status,
            'hours'         => $log['hours'],
            'date'          => $log['date'],
            'plans'         => $plans,
            'total'         => number_format(intval($total) / 100, 2, ',', '.'),
            'link'          => $link,
        ]);
    }

    /**
     * @param int $saleId
     * @return string
     */
    public function getSalesCartOrBoletoDetails(int $saleId)
    {
        /*$salesModel    = new Sale();
        $logModel      = new Log();
        $checkoutModel = new Checkout();
        $sale          = $salesModel->with(['clientModel', 'checkoutModel'])->find($saleId);
        dd($sale);
        $log = $logModel->where('id_log_session', $sale->getRelation('checkoutModel')->id_log_session)
                        ->orderBy('id', 'desc')->first();
        dd($log);

        /* return view('salesrecovery::details', [
             'checkout'      => $sale,
             'log'           => $log,
             'whatsapp_link' => "https://api.whatsapp.com/send?phone=55" . preg_replace('/[^0-9]/', '', $log->telephone) . '&text=' . $whatsAppMsg,
             'status'        => $status,
             'hours'         => $log['hours'],
             'date'          => $log['date'],
             'plans'         => $plans,
             'total'         => number_format(intval($total) / 100, 2, ',', '.'),
             'link'          => $link,
         ]);*/

        return '';*/
    }
}
