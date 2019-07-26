<?php

namespace Modules\Core\Services\SalesRecovery;

use App\Entities\Checkout;
use App\Entities\Sale;
use App\Entities\UserProject;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
//use Modules\SalesRecovery\Transformers\SalesRecoveryCardRefused;
use Modules\SalesRecovery\Transformers\SalesRecoveryCardRefusedResource;
use Modules\SalesRecovery\Transformers\SalesRecoveryResource;

class SalesRecoveryService
{
    public function verifyType($type, $projectId, $dateStart, $dateEnd)
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

    public function getAllTypes()
    {
        return '';
    }

    /**
     * @param $projectId
     * @param $dateStart
     * @param $dateEnd
     * @return AnonymousResourceCollection
     * Carrinho abandonado e recuperado
     */
    public function getAbandonedCart($projectId, $dateStart, $dateEnd)
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

    public function getSaleExpiredOrRefused($projectId, $dateStart, $dateEnd, $paymentMethod, $status)
    {
        $salesModel        = new Sale();
        $userProjectsModel = new UserProject();

        $salesExpired = $salesModel
            ->select('sales.*', DB::raw('(plan_sale.amount * plan_sale.plan_value ) AS value'), 'checkout.email_sent_amount', 'checkout.sms_sent_amount', 'checkout.id_log_session')
            ->leftJoin('plans_sales as plan_sale', function($join) {
                $join->on('plan_sale.sale', '=', 'sales.id');
            })->leftJoin('checkouts as checkout', function($join) {
                $join->on('sales.checkout', 'checkout.id');
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

        $salesExpired->orderBy('id');

        return SalesRecoveryCardRefusedResource::collection($salesExpired->paginate(10));
    }
}
