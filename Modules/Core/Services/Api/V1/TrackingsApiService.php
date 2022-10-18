<?php

namespace Modules\Core\Services\Api\V1;

use Exception;
use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Product;
use Modules\Core\Entities\Tracking;

class TrackingsApiService
{
    public static function getTrackingsQueryBuilder($filters, $id = null)
    {
        try {
            $userId = request()->user_id;

            $trackings = Tracking::join('sales', 'sales.id', 'trackings.sale_id');

            if (!empty($id)) {
                $trackings->where('trackings.id', foxutils()->decodeHash($id));
            }

            if (!empty($filters['company_id'])) {
                $companyId = hashids_decode($filters['company_id']);

                $trackings->leftJoin('transactions', function($q) use($companyId) {
                    $q->on('transactions.sale_id', 'sales.id')->where('transactions.company_id', $companyId);
                });
            } else {
                $companyId = Company::where('user_id', $userId)->pluck('id')->toArray();

                $trackings->leftJoin('transactions', function($q) use($companyId) {
                    $q->on('transactions.sale_id', 'sales.id')->whereIn('transactions.company_id', $companyId);
                });
            }

            $trackings
                ->join('products_plans_sales', 'products_plans_sales.id', 'trackings.product_plan_sale_id')
                ->leftJoin("products", function ($leftJoin) {
                    $leftJoin
                        ->on("products.id", "=", "products_plans_sales.product_id")
                        ->where("products.type_enum", Product::TYPE_PHYSICAL);
                })
                ->leftJoin("products_sales_api", function ($leftJoin) {
                    $leftJoin
                        ->on("products_sales_api.id", "=", "products_plans_sales.products_sales_api_id")
                        ->where("products_sales_api.product_type", "physical_goods");
                })
                ->where(function ($where) {
                    $where->whereNotNull("products.id")->orWhereNotNull("products_sales_api.id");
                });

            $trackings->whereNotNull('transactions.id');

            if (!empty($filters["tracking_code"])) {
                $trackings->where("trackings.tracking_code", "like", "%" . $filters["tracking_code"] . "%");
            }

            $trackings->select([
                "trackings.id",
                "trackings.tracking_code",
                "trackings.tracking_status_enum",
                "trackings.system_status_enum",
                "trackings.created_at",
                "sales.id as sale_id",
                DB::raw("ifnull(products.id, products_sales_api.id) as product_id"),
                DB::raw("ifnull(products.name, products_sales_api.name) as product_name"),
                "products.description as product_description",
                "products_plans_sales.amount as product_amount",
            ])
            ->orderBy("trackings.id", "desc");

            return $trackings;
        } catch(Exception $exception) {
            return response()->json([
                'message' => $exception->getMessage()
            ], 400);
        }
    }
}
