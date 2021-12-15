<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveUnusedColumnsFromProjects extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn([
                'checkout_type',
                'invoice_description',
                'contact',
                'contact_verified',
                'logo',
                'countdown_timer_flag',
                'countdown_timer_color',
                'countdown_timer_time',
                'countdown_timer_description',
                'countdown_timer_finished_message',
                'checkout_notification_configs',
                'finalizing_purchase_configs',
                'credit_card',
                'boleto',
                'pix',
                'product_amount_selector',
                'required_email_checkout',
                'installments_amount',
                'installments_interest_free',
                'pre_selected_installment',
                'boleto_due_days',
                'credit_card_discount',
                'billet_discount',
                'pix_discount',
                'custom_message_configs',
                'whatsapp_button',
                'support_phone',
                'support_phone_verified'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->string('installments_amount')->nullable()->after('woocommerce_id');
            $table->string('installments_interest_free')->nullable()->after('installments_amount');
            $table->string('invoice_description')->nullable();
            $table->string('contact')->nullable()->index('projects_contact_IDX');
            $table->boolean('contact_verified')->default(0)->index('projects_contact_verified_IDX');
            $table->string('logo')->nullable()->after('contact_verified');
            $table->boolean('boleto')->default(1)->after('logo');
            $table->boolean('credit_card')->default(1)->after('boleto');
            $table->boolean('pix')->default(0)->after('credit_card');
            $table->tinyInteger('boleto_due_days')->after('pix');
            $table->string('support_phone')->nullable()->index('projects_support_phone_IDX')->after('analyzing_redirect');
            $table->boolean('support_phone_verified')->default(0)->index('projects_support_phone_verified_IDX')->after('support_phone');
            $table->bigInteger('billet_discount')->default(0)->after('discount_recovery_value');
            $table->bigInteger('credit_card_discount')->default(0)->after('billet_discount');
            $table->bigInteger('pix_discount')->default(0)->after('credit_card_discount');
            $table->boolean('whatsapp_button')->default(1)->after('pix_discount');
            $table->boolean('countdown_timer_flag')->default(0)->after('whatsapp_button');
            $table->string('countdown_timer_color', 7)->default('#f78d1e')->after('countdown_timer_flag');
            $table->integer('countdown_timer_time')->default(5)->after('countdown_timer_color');
            $table->string('countdown_timer_description')->nullable()->after('countdown_timer_time');
            $table->string('countdown_timer_finished_message')->default('Seu tempo acabou! Você precisa finalizar sua compra imediatamente.')->after('countdown_timer_description');
            $table->json('finalizing_purchase_configs')->nullable()->after('reviews_config_icon_type');
            $table->json('checkout_notification_configs')->nullable()->after('finalizing_purchase_configs');
            $table->json('custom_message_configs')->nullable()->after('checkout_notification_configs');
            $table->integer('checkout_type')->default(1)->after('updated_at');
            $table->integer('pre_selected_installment')->default(12)->after('checkout_type');
            $table->boolean('required_email_checkout')->default(0)->after('pre_selected_installment');
            $table->boolean('product_amount_selector')->default(1)->after('document_type_checkout');
        });
    }
}
