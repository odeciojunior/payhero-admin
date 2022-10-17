<?php

namespace Modules\Core\Services\Api\V1;

use Exception;
use Illuminate\Support\Facades\DB;
use Modules\Api\Transformers\V1\TrackingsApiResource;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\Tracking;
use Modules\Core\Entities\Transaction;
use Modules\Core\Entities\User;
use Vinkla\Hashids\Facades\Hashids;

class TrackingsApiService
{
    public static function getTrackingsQueryBuilder($requestData)
    {
        try {
            $companies = Company::where('user_id', request()->user_id)->pluck('id')->toArray();

            $trackingModel = new Tracking();
            $trackings = $trackingModel
                        ->select('trackings.*')
                        ->join('sales', 'sales.id', 'trackings.sale_id')
                        ->join('transactions', 'transactions.sale_id', 'sales.id')
                        ->whereIn('transactions.company_id', $companies)
                        ->orderBy('trackings.id', 'desc');

            return $trackings;
        } catch(Exception $exception) {
            return response()->json([
                'message' => $exception->getMessage()
            ], 400);
        }
    }

    public static function showTrackingsQueryBuilder($id)
    {
        try {
            $trackingId = current(Hashids::decode($id));
            $companies = Company::where('user_id', request()->user_id)->pluck('id')->toArray();

            $trackingModel = new Tracking();
            $trackings = $trackingModel
                        ->select('trackings.*')
                        ->join('sales', 'sales.id', 'trackings.sale_id')
                        ->join('transactions', 'transactions.sale_id', 'sales.id')
                        ->whereIn('transactions.company_id', $companies)
                        ->where('trackings.id', $trackingId)
                        ->first();

            return $trackings;
        } catch(Exception $exception) {
            return response()->json([
                'message' => $exception->getMessage()
            ], 400);
        }
    }
}
