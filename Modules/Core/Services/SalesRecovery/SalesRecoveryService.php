<?php

namespace Modules\Core\Services\SalesRecovery;

use App\Entities\Checkout;
use App\Entities\Sale;
use App\Entities\UserProject;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Modules\SalesRecovery\Transformers\SalesRecoveryResource;

class SalesRecoveryService
{
    public function verifyType($type, $projectId, $dateStart, $dateEnd)
    {
        if ($type == 1) {
            return $this->getAbandonedCart($projectId, $dateStart, $dateEnd);
        } else if ($type == 2) {
            return $this->getBoletoExpired($projectId, $dateStart, $dateEnd);
        } else if ($type == 3) {
            return $this->getRecusedCard();
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

    public function getBoletoExpired($projectId, $dateStart, $dateEnd)
    {
        $checkoutModel     = new Checkout();
        $salesModel        = new Sale();
        $userProjectsModel = new UserProject();

        $salesExpired = $salesModel->where([
                                               ['payment_method', 2],
                                               ['status', 3],
                                           ]);
        if (!empty($projectId)) {
            $salesExpired->where('project', $projectId);
        } else {
            $userProjects = $userProjectsModel->where([
                                                          ['user', auth()->user()->id],
                                                          ['type', 'producer'],
                                                      ])->pluck('project')->toArray();

            $salesExpired->whereIn('project', $userProjects)->with('projectModel');
        }
        if (!empty($dateStart) && !empty($dateEnd)) {
            $salesExpired->whereBetween('created_at', [$dateStart, $dateEnd]);
        } else {
            if (!empty($dateStart)) {
                $salesExpired->whereDate('created_at', '>=', $dateStart);
            }
            if (!empty($dateEnd)) {
                $salesExpired->whereDate('created_at', '<', $dateEnd);
            }
        }

        $salesExpired->orderBy('id', 'DESC');

        return '';
    }

    public function getRecusedCard()
    {
        $checkoutModel = new Checkout();

        $salesModel        = new Sale();
        $userProjectsModel = new UserProject();

        $salesExpired = $salesModel
            ->select('sales.*', 'plan_sale.plan_value', 'plan_sale.amount', 'checkout.email_sent_amount', 'checkout.sms_sent_amount')
            ->leftJoin('plans_sales as plan_sale', function($join) {
                $join->on('plan_sale.sale', '=', 'sales.id');
            })
            ->leftJoin('checkouts as checkout', function($join) {
                $join->on('sales.checkout', 'checkout.id');
            })->where([
                          ['sales.payment_method', 1],
                          ['sales.status', 3],
                      ]);
        if (!empty($projectId)) {
            $salesExpired->where('sales.project', $projectId);
        } else {
            $userProjects = $userProjectsModel->where([
                                                          ['user', auth()->user()->id],
                                                          ['type', 'producer'],
                                                      ])->pluck('project')->toArray();

            $salesExpired->whereIn('sales.project', $userProjects)->with('projectModel');
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

        $salesExpired->get();

        return '';
    }
}
