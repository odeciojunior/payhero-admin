<?php

use Illuminate\Database\Migrations\Migration;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\SaleLog;

class UpdateSales extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Sale::where("status", Sale::STATUS_REFUNDED)
            ->where("total_paid_value", 0)
            ->whereNotNull("original_total_paid_value")
            ->chunkById(1000, function ($sales) {
                foreach ($sales as $sale) {
                    $sale->update([
                        "total_paid_value" => substr_replace(
                            $sale->original_total_paid_value,
                            ".",
                            strlen($sale->original_total_paid_value) - 2,
                            0
                        ),
                    ]);
                }
            });

        Sale::where("status", Sale::STATUS_REFUNDED)
            ->whereDoesntHave("saleLogs", function ($query) {
                $query->where("status_enum", Sale::STATUS_REFUNDED);
            })
            ->chunkById(1000, function ($sales) {
                foreach ($sales as $sale) {
                    SaleLog::create([
                        "sale_id" => $sale->id,
                        "status" => "refunded",
                        "status_enum" => Sale::STATUS_REFUNDED,
                        "created_at" => $sale->date_refunded,
                    ]);
                }
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
