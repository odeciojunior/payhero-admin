<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\User;

class CreateAntifraudSaleReviewTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('antifraud_sale_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Sale::class);
            $table->foreignIdFor(User::class)->nullable();
            $table->text('observation')->nullable();
            $table->string('status')->nullable();
            $table->string('card_status')->nullable();
            $table->string('result')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        DB::statement(
            "insert into antifraud_sale_reviews
                    (sale_id, observation, result, created_at)
                select  id,
                        antifraud_observation,
                        if(status = 1, 'accept', if(status in (3, 4, 7, 21), 'refuse', null)),
                        now()
                from sales
                where (antifraud_observation is not null
                    or status = 20)
                  and id not in (select sale_id from antifraud_sale_reviews);"
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('antifraud_sale_reviews');
    }
}
