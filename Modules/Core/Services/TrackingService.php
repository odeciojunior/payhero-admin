<?php

namespace Modules\Core\Services;

use Modules\Core\Entities\Company;
use Modules\Core\Entities\Tracking;
use Vinkla\Hashids\Facades\Hashids;

class TrackingService
{

    public function getTrackings($filters)
    {
        $trackingModel = new Tracking();
        $companyModel = new Company();

        $userCompanies = $companyModel->where('user_id', auth()->user()->id)
            ->pluck('id')
            ->toArray();

        $trackings = $trackingModel
            ->with([
                'sale.transactions',
                'product',
            ])
            ->whereHas('sale.transactions', function ($query) use ($userCompanies) {
                $query->whereIn('company_id', $userCompanies);
            })
            ->whereNotNull('tracking_code');

        if (isset($filters['status'])) {
            $trackings->where('tracking_status_enum', $trackingModel->present()->getTrackingStatusEnum($filters['status']));
        }

        if (isset($filters['tracking_code'])) {
            $trackings->where('tracking_code', 'like', '%' . $filters['tracking_code'] . '%');
        }

        if (isset($filters['project'])) {
            $trackings->whereHas('product', function ($query) use ($filters) {
                $query->where('project_id', current(Hashids::decode($filters['project'])));
            });
        }
        //tipo da data e periodo obrigatorio
        $dateRange = FoxUtils::validateDateRange($filters["date_updated"]);
        $trackings->whereBetween('updated_at', [$dateRange[0] . ' 00:00:00', $dateRange[1] . ' 23:59:59']);

        return $trackings->orderBy('id', 'desc')->paginate(10);
    }

    public function getResume($filters)
    {
        $trackingModel = new Tracking();
        $companyModel = new Company();

        $userCompanies = $companyModel->where('user_id', auth()->user()->id)
            ->pluck('id')
            ->toArray();

        $query = $trackingModel
            ->whereHas('sale.transactions', function ($query) use ($userCompanies) {
                $query->whereIn('company_id', $userCompanies);
            })
            ->whereNotNull('tracking_code');

        if (isset($filters['status'])) {
            $query->where('tracking_status_enum', $trackingModel->present()->getTrackingStatusEnum($filters['status']));
        }

        if (isset($filters['tracking_code'])) {
            $query->where('tracking_code', 'like', '%' . $filters['tracking_code'] . '%');
        }

        if (isset($filters['project'])) {
            $query->whereHas('product', function ($query) use ($filters) {
                $query->where('project_id', current(Hashids::decode($filters['project'])));
            });
        }
        //tipo da data e periodo obrigatorio
        $dateRange = FoxUtils::validateDateRange($filters["date_updated"]);
        $query->whereBetween('updated_at', [$dateRange[0] . ' 00:00:00', $dateRange[1] . ' 23:59:59']);

        $trackings = $query->select('tracking_status_enum')
            ->get();

        $total = $trackings->count();
        $posted = $trackings->where('tracking_status_enum', $trackingModel->present()->getTrackingStatusEnum('posted'))->count();
        $dispatched = $trackings->where('tracking_status_enum', $trackingModel->present()->getTrackingStatusEnum('dispatched'))->count();
        $delivered = $trackings->where('tracking_status_enum', $trackingModel->present()->getTrackingStatusEnum('delivered'))->count();
        $out_for_delivery = $trackings->where('tracking_status_enum', $trackingModel->present()->getTrackingStatusEnum('out_for_delivery'))->count();
        $exception = $trackings->where('tracking_status_enum', $trackingModel->present()->getTrackingStatusEnum('exception'))->count();

        return response()->json(['data' => [
            'total' => $total,
            'posted' => $posted,
            'dispatched' => $dispatched,
            'delivered' => $delivered,
            'out_for_delivery' => $out_for_delivery,
            'exception' => $exception,
        ]]);
    }
}
