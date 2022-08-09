<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\Product;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\Tracking;

class CheckHasValidTracking extends Command
{
    protected $signature = "check:has-valid-tracking";

    protected $description = "Command description";

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        try {
            $query = Sale::select("sales.id", "sales.has_valid_tracking")
                ->join("products_plans_sales as pps", "sales.id", "=", "pps.sale_id")
                ->leftJoin("trackings as t", function ($join) {
                    $join
                        ->on("pps.id", "=", "t.product_plan_sale_id")
                        ->whereIn("t.system_status_enum", [
                            Tracking::SYSTEM_STATUS_VALID,
                            Tracking::SYSTEM_STATUS_CHECKED_MANUALLY,
                        ]);
                })
                ->where(function ($query) {
                    $query
                        ->whereExists(function ($query) {
                            $query
                                ->select(DB::raw(1))
                                ->from("products as p")
                                ->where("type_enum", Product::TYPE_PHYSICAL)
                                ->whereColumn("p.id", "pps.product_id");
                        })
                        ->orWhereExists(function ($query) {
                            $query
                                ->select(DB::raw(1))
                                ->from("products_sales_api as psa")
                                ->where("product_type", "physical_goods")
                                ->whereColumn("psa.id", "pps.products_sales_api_id");
                        });
                })
                ->where("has_valid_tracking", 0)
                ->where("sales.status", Sale::STATUS_APPROVED)
                ->groupBy("sales.id")
                ->having(DB::raw("count(pps.id)"), "=", DB::raw("count(t.id)"));

            $query->chunk(1000, function ($sales) {
                foreach ($sales as $sale) {
                    $sale->has_valid_tracking = true;
                    $sale->save();
                }
            });
        } catch (Exception $e) {
            report($e);
        }
    }
}
