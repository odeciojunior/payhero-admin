<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCheckoutConfigsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('checkout_configs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->integer('checkout_type_enum')->default(2);
            $table->boolean('checkout_logo_enabled')->default(false);
            $table->string('checkout_logo')->nullable();
            $table->boolean('checkout_banner_enabled')->default(false);
            $table->integer('checkout_banner_type')->default(1);
            $table->string('checkout_banner')->nullable();
            $table->boolean('countdown_enabled')->default(false);
            $table->integer('countdown_time')->default(15);
            $table->string('countdown_description')->nullable();
            $table->string('countdown_finish_message')->nullable();
            $table->boolean('topbar_enabled')->default(false);
            $table->text('topbar_content')->nullable();
            $table->boolean('notifications_enabled')->default(false);
            $table->integer('notifications_interval')->default(30);
            $table->boolean('notification_buying_enabled')->default(false);
            $table->integer('notification_buying_minimum')->default(1);
            $table->boolean('notification_bought_30_minutes_enabled')->default(false);
            $table->integer('notification_bought_30_minutes_minimum')->default(1);
            $table->boolean('notification_bought_last_hour_enabled')->default(false);
            $table->integer('notification_bought_last_hour_minimum')->default(1);
            $table->boolean('notification_just_bought_enabled')->default(false);
            $table->integer('notification_just_bought_minimum')->default(1);
            $table->boolean('social_proof_enabled')->default(false);
            $table->text('social_proof_message')->nullable();
            $table->integer('social_proof_minimum')->default(15);
            $table->string('invoice_description')->nullable();
            $table->unsignedBigInteger('company_id');
            $table->boolean('cpf_enabled')->default(true);
            $table->boolean('cnpj_enabled')->default(true);
            $table->boolean('credit_card_enabled')->default(true);
            $table->boolean('bank_slip_enabled')->default(true);
            $table->boolean('pix_enabled')->default(true);
            $table->boolean('quantity_selector_enabled')->default(true);
            $table->boolean('email_required')->default(true);
            $table->integer('installments_limit')->default(12);
            $table->integer('interest_free_installments')->default(1);
            $table->integer('preselected_installment')->default(12);
            $table->integer('bank_slip_due_days')->default(3);
            $table->integer('automatic_discount_credit_card')->default(0);
            $table->integer('automatic_discount_bank_slip')->default(0);
            $table->integer('automatic_discount_pix')->default(0);
            $table->boolean('post_purchase_message_enabled')->default(false);
            $table->string('post_purchase_message_title')->nullable();
            $table->text('post_purchase_message_content')->nullable();
            $table->boolean('whatsapp_enabled')->default(false);
            $table->string('support_phone')->nullable();
            $table->boolean('support_phone_verified')->default(false);
            $table->string('support_email')->nullable();
            $table->boolean('support_email_verified')->default(false);
            $table->integer('theme_enum')->default(1);
            $table->string('color_primary')->default('#4B8FEF');
            $table->string('color_secondary')->default('#313C52');
            $table->string('color_buy_button')->default('#23d07d');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('checkout_configs');
    }
}
