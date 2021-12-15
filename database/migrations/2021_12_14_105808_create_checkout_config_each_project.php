<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateCheckoutConfigEachProject extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("INSERT INTO checkout_configs (project_id, checkout_type_enum, checkout_logo, countdown_enabled, countdown_time, countdown_description, countdown_finish_message, notifications_enabled, notifications_interval, notification_buying_enabled, notification_buying_minimum, notification_bought_30_minutes_enabled, notification_bought_30_minutes_minimum, notification_bought_last_hour_enabled, notification_bought_last_hour_minimum, notification_just_bought_enabled, notification_just_bought_minimum, social_proof_enabled, social_proof_message, social_proof_minimum, invoice_description, company_id, credit_card_enabled, bank_slip_enabled, pix_enabled, quantity_selector_enabled, email_required, installments_limit, interest_free_installments, preselected_installment, bank_slip_due_days, automatic_discount_credit_card, automatic_discount_bank_slip, automatic_discount_pix, post_purchase_message_enabled, post_purchase_message_title, post_purchase_message_content, whatsapp_enabled, support_phone, support_phone_verified, support_email, support_email_verified, created_at, updated_at) (
                             SELECT p.id                                                                                                 AS project_id,
                                    p.checkout_type                                                                                      AS checkout_type_enum,
                                    p.logo                                                                                               AS checkout_logo,
                                    p.countdown_timer_flag                                                                               AS countdown_enabled,
                                    p.countdown_timer_time                                                                               AS countdown_time,
                                    p.countdown_timer_description                                                                        AS countdown_description,
                                    p.countdown_timer_finished_message                                                                   AS countdown_finish_message,
                                    ifnull(p.checkout_notification_configs->>'$.toogle',0) = 1                                           AS notifications_enabled,
                                    ifnull(p.checkout_notification_configs->>'$.time',30)                                                AS notifications_interval,
                                    p.checkout_notification_configs->>'$.messages.products_moment' IS NOT NULL                           AS notification_buying_enabled,
                                    ifnull(SUBSTRING_INDEX(p.checkout_notification_configs->>'$.messages.products_moment','//',-1),1)    AS notification_buying_minimum,
                                    p.checkout_notification_configs->>'$.messages.products_half_hour' IS NOT NULL                        AS notification_bought_30_minutes_enabled,
                                    ifnull(SUBSTRING_INDEX(p.checkout_notification_configs->>'$.messages.products_half_hour','//',-1),1) AS notification_bought_30_minutes_minimum,
                                    p.checkout_notification_configs->>'$.messages.products_last_hour' IS NOT NULL                        AS notification_bought_last_hour_enabled,
                                    ifnull(SUBSTRING_INDEX(p.checkout_notification_configs->>'$.messages.products_last_hour','//',-1),1) AS notification_bought_last_hour_minimum,
                                    p.checkout_notification_configs->>'$.messages.client' IS NOT NULL                                    AS notification_just_bought_enabled,
                                    ifnull(SUBSTRING_INDEX(p.checkout_notification_configs->>'$.messages.client','//',-1),1)             AS notification_just_bought_minimum,
                                    ifnull(p.finalizing_purchase_configs->>'$.toogle',0) = 1                                             AS social_proof_enabled,
                                    p.finalizing_purchase_configs->>'$.text'                                                             AS social_proof_message,
                                    ifnull(p.finalizing_purchase_configs->>'$.min_value',1)                                              AS social_proof_minimum,
                                    p.invoice_description,
                                    up.company_id,
                                    p.credit_card                                                                                        AS credit_card_enabled,
                                    p.boleto                                                                                             AS bank_slip_enabled,
                                    p.pix                                                                                                AS pix_enabled,
                                    p.product_amount_selector                                                                            AS quantity_selector_enabled,
                                    p.required_email_checkout                                                                            AS email_required,
                                    ifnull(p.installments_amount,12)                                                                     AS installments_limit,
                                    ifnull(p.installments_interest_free,1)                                                               AS interest_free_installments,
                                    ifnull(p.pre_selected_installment,12)                                                                AS preselected_installment,
                                    p.boleto_due_days                                                                                    AS bank_slip_due_days,
                                    p.credit_card_discount                                                                               AS automatic_discount_credit_card,
                                    p.billet_discount                                                                                    AS automatic_discount_bank_slip,
                                    p.pix_discount                                                                                       AS automatic_discount_pix,
                                    ifnull(p.custom_message_configs->>'$.active',0) = TRUE                                               AS post_purchase_message_enabled,
                                    p.custom_message_configs->>'$.title'                                                                 AS post_purchase_message_title,
                                    p.custom_message_configs->>'$.message'                                                               AS post_purchase_message_content,
                                    p.whatsapp_button                                                                                    AS whatsapp_enabled,
                                    p.support_phone,
                                    p.support_phone_verified,
                                    p.contact                                                                                            AS support_email,
                                    p.contact_verified                                                                                   AS support_email_verified,
                                    now()                                                                                                AS created_at,
                                    now()                                                                                                AS updated_at
                             FROM projects AS p
                             JOIN users_projects AS up
                             ON up.project_id = p.id)");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("delete from checkout_configs");
    }
}
