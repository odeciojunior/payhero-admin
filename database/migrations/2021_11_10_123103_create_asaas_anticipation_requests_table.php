<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAsaasAnticipationRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('asaas_anticipation_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('company_id');
            $table->unsignedBigInteger('sale_id');
            $table->json('sent_data')->nullable();
            $table->json('response')->nullable();
            $table->timestamps();
            $table->foreign('company_id')->references('id')->on('companies');
            $table->foreign('sale_id')->references('id')->on('sales');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('asaas_anticipations_requests');
    }
}
 // php artisan krlove:generate:model AsaasAnticipationRequests --output-path= Modules/Core/Entities/ --namespace=Modules\\Core\\Entities --table-name=asaas_anticipation_requests
//  php artisan krlove:generate:model AsaasAnticipationRequests -output-path=/full/path/to/output/directory --table-name=asaas_anticipation_requests
