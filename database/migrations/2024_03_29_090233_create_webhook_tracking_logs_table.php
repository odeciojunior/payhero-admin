<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('webhook_tracking_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("webhook_tracking_id")->index();
            $table->unsignedInteger("user_id")->index();
            $table->unsignedInteger("project_id")->index();
            $table->unsignedInteger("sale_id")->index();
            $table->string("url");
            $table->json("sent_data");
            $table->json("response")->nullable();
            $table->integer("response_status")->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table
                ->foreign(["webhook_tracking_id"])
                ->references(["id"])
                ->on("webhook_trackings")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
            
            $table
                ->foreign(["user_id"])
                ->references(["id"])
                ->on("users")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");

            $table
                ->foreign(["project_id"])
                ->references(["id"])
                ->on("projects")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");        
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('webhook_tracking_logs');
    }
};
