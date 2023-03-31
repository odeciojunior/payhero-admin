<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("achievement_user", function (Blueprint $table) {
            $table->unsignedBigInteger("achievement_id");
            $table->unsignedInteger("user_id")->index("achievement_user_user_id_foreign");
            $table->timestamps();

            $table->primary(["achievement_id", "user_id"]);
        });

        Schema::create("achievements", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->string("name", 100);
            $table->string("description", 100);
            $table->text("storytelling");
            $table->string("icon", 100);
            $table->timestamps();
        });

        Schema::create("activecampaign_customs", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table
                ->unsignedBigInteger("activecampaign_integration_id")
                ->index("activecampaign_customs_activecampaign_integration_id_foreign");
            $table->unsignedBigInteger("custom_field_id")->index("activecampaign_customs_custom_field_id_IDX");
            $table->string("custom_field");
            $table
                ->timestamp("created_at")
                ->nullable()
                ->index("activecampaign_customs_created_at_IDX");
            $table
                ->timestamp("updated_at")
                ->nullable()
                ->index("activecampaign_customs_updated_at_IDX");
        });

        Schema::create("activecampaign_events", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table
                ->unsignedBigInteger("activecampaign_integration_id")
                ->index("activecampaign_events_activecampaign_integration_id_foreign");
            $table->integer("event_sale")->index("activecampaign_events_event_sale_IDX");
            $table->string("add_tags")->nullable();
            $table->string("remove_tags")->nullable();
            $table->string("remove_list")->nullable();
            $table->string("add_list")->nullable();
            $table
                ->timestamp("created_at")
                ->nullable()
                ->index("activecampaign_events_created_at_IDX");
            $table
                ->timestamp("updated_at")
                ->nullable()
                ->index("activecampaign_events_updated_at_IDX");
            $table->softDeletes()->index("activecampaign_events_deleted_at_IDX");
        });

        Schema::create("activecampaign_integrations", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->unsignedInteger("user_id")->index("activecampaign_integrations_user_id_IDX");
            $table->unsignedInteger("project_id")->index("activecampaign_integrations_project_id_IDX");
            $table->string("api_url");
            $table->string("api_key");
            $table
                ->timestamp("created_at")
                ->nullable()
                ->index("activecampaign_integrations_created_at_IDX");
            $table
                ->timestamp("updated_at")
                ->nullable()
                ->index("activecampaign_integrations_updated_at_IDX");
            $table->softDeletes()->index("activecampaign_integrations_deleted_at_IDX");

            $table->index(["project_id"]);
            $table->index(["user_id"]);
        });

        Schema::create("activecampaign_sent", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->text("data");
            $table->text("response");
            $table
                ->integer("sent_status")
                ->nullable()
                ->index("activecampaign_sent_sent_status_IDX");
            $table
                ->unsignedInteger("event_sale")
                ->nullable()
                ->index("activecampaign_sent_event_sale_IDX");
            $table
                ->unsignedBigInteger("instance_id")
                ->nullable()
                ->index("activecampaign_sent_instance_id_IDX");
            $table->string("instance")->nullable();
            $table
                ->unsignedBigInteger("activecampaign_integration_id")
                ->nullable()
                ->index("activecampaign_sent_activecampaign_integration_id_foreign");
            $table
                ->timestamp("created_at")
                ->nullable()
                ->index("activecampaign_sent_created_at_IDX");
            $table
                ->timestamp("updated_at")
                ->nullable()
                ->index("activecampaign_sent_updated_at_IDX");
        });

        Schema::create("activity_log", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table
                ->string("log_name")
                ->nullable()
                ->index();
            $table->text("description");
            $table->unsignedBigInteger("subject_id")->nullable();
            $table->string("subject_type")->nullable();
            $table->string("event")->nullable();
            $table->unsignedBigInteger("causer_id")->nullable();
            $table->string("causer_type")->nullable();
            $table->json("properties")->nullable();
            $table->char("batch_uuid", 36)->nullable();
            $table
                ->timestamp("created_at")
                ->nullable()
                ->index("activity_log_created_at_IDX");
            $table
                ->timestamp("updated_at")
                ->nullable()
                ->index("activity_log_updated_at_IDX");

            $table->index(["causer_id", "causer_type"], "causer");
            $table->index(["subject_id", "subject_type"], "subject");
        });

        Schema::create("affiliate_links", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->unsignedBigInteger("affiliate_id")->index("links_afiliados_afiliado_foreign");
            $table
                ->unsignedBigInteger("campaign_id")
                ->nullable()
                ->index("links_afiliados_campanha_foreign");
            $table
                ->unsignedBigInteger("plan_id")
                ->nullable()
                ->index("links_afiliados_plano_foreign");
            $table->string("parameter")->nullable();
            $table->string("name")->nullable();
            $table->string("link")->nullable();
            $table->bigInteger("clicks_amount")->default(0);
            $table
                ->timestamp("created_at")
                ->nullable()
                ->index("affiliate_links_created_at_IDX");
            $table
                ->timestamp("updated_at")
                ->nullable()
                ->index("affiliate_links_updated_at_IDX");
            $table->softDeletes()->index("affiliate_links_deleted_at_IDX");
        });

        Schema::create("affiliate_requests", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->unsignedInteger("user_id")->index("solicitacoes_afiliacoes_user_foreign");
            $table->unsignedInteger("project_id")->index("solicitacoes_afiliacoes_projeto_foreign");
            $table
                ->unsignedInteger("company_id")
                ->nullable()
                ->index("affiliate_requests_company_id_foreign");
            $table->string("status");
            $table
                ->timestamp("created_at")
                ->nullable()
                ->index("affiliate_requests_created_at_IDX");
            $table
                ->timestamp("updated_at")
                ->nullable()
                ->index("affiliate_requests_updated_at_IDX");
            $table->softDeletes()->index("affiliate_requests_deleted_at_IDX");
        });

        Schema::create("affiliates", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->unsignedInteger("user_id")->index("afiliados_user_foreign");
            $table->unsignedInteger("project_id")->index("afiliados_projeto_foreign");
            $table
                ->unsignedInteger("company_id")
                ->nullable()
                ->index("afiliados_empresa_foreign");
            $table->string("percentage")->nullable();
            $table
                ->tinyInteger("status_enum")
                ->nullable()
                ->index("affiliates_status_enum_IDX");
            $table->string("suport_contact")->nullable();
            $table->boolean("suport_contact_verified")->default(false);
            $table->string("suport_phone", 20)->nullable();
            $table->boolean("suport_phone_verified")->default(false);
            $table
                ->unsignedInteger("order_priority")
                ->default(0)
                ->index("affiliates_order_priority_IDX");
            $table
                ->timestamp("created_at")
                ->nullable()
                ->index("affiliates_created_at_IDX");
            $table
                ->timestamp("updated_at")
                ->nullable()
                ->index("affiliates_updated_at_IDX");
            $table->softDeletes()->index("affiliates_deleted_at_IDX");
        });

        Schema::create("anticipated_transactions", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->integer("value");
            $table->string("tax");
            $table->integer("tax_value");
            $table->string("days_to_release");
            $table->unsignedBigInteger("anticipation_id")->index("anticipated_transactions_anticipation_id_foreign");
            $table->unsignedBigInteger("transaction_id")->index("anticipated_transactions_transaction_id_foreign");
            $table
                ->timestamp("created_at")
                ->nullable()
                ->index("anticipated_transactions_created_at_IDX");
            $table
                ->timestamp("updated_at")
                ->nullable()
                ->index("anticipated_transactions_updated_at_IDX");
        });

        Schema::create("anticipations", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->integer("value");
            $table->integer("tax");
            $table->string("percentage_tax");
            $table->string("percentage_anticipable")->nullable();
            $table->unsignedInteger("company_id")->index("anticipations_company_id_foreign");
            $table
                ->timestamp("created_at")
                ->nullable()
                ->index("anticipations_created_at_IDX");
            $table
                ->timestamp("updated_at")
                ->nullable()
                ->index("anticipations_updated_at_IDX");
        });

        Schema::create("antifraud_device_data", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->string("attempt_reference");
            $table->unsignedBigInteger("sale_id")->nullable();
            $table->json("request")->nullable();
            $table->string("site_url")->nullable();
            $table->string("ip")->nullable();
            $table->string("browser_fingerprint")->nullable();
            $table->string("os")->nullable();
            $table
                ->integer("os_enum")
                ->default(0)
                ->index();
            $table->string("os_version")->nullable();
            $table->string("browser")->nullable();
            $table->string("browser_version")->nullable();
            $table->longText("user_agent")->nullable();
            $table->json("cookies")->nullable();
            $table->string("robot")->nullable();
            $table->string("incognito")->nullable();
            $table->string("proxy")->nullable();
            $table->string("battery")->nullable();
            $table->string("lat")->nullable();
            $table->string("long")->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create("antifraud_postbacks", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table
                ->unsignedBigInteger("sale_id")
                ->nullable()
                ->index();
            $table
                ->unsignedBigInteger("antifraud_id")
                ->nullable()
                ->index();
            $table->json("data");
            $table
                ->unsignedTinyInteger("processed_flag")
                ->default(0)
                ->index();
            $table
                ->unsignedTinyInteger("postback_valid_flag")
                ->default(0)
                ->index();
            $table->json("machine_result")->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create("antifraud_quiz_questions", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->unsignedBigInteger("sale_id")->index("antifraud_quiz_questions_sale_id_foreign");
            $table->string("bigid_quiz_id");
            $table->string("question");
            $table->string("correct_answer");
            $table->string("answer")->nullable();
            $table->boolean("open_answer_flag")->nullable();
            $table->dateTime("started_at")->nullable();
            $table->dateTime("finished_at")->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create("antifraud_sale_reviews", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->unsignedBigInteger("sale_id")->index("antifraud_sale_reviews_sale_id_foreign");
            $table
                ->unsignedInteger("user_id")
                ->nullable()
                ->index("antifraud_sale_reviews_user_id_foreign");
            $table->text("observation")->nullable();
            $table->string("status")->nullable();
            $table->string("card_status")->nullable();
            $table->string("result")->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create("antifraud_warnings", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->unsignedBigInteger("sale_id")->nullable();
            $table->tinyInteger("status")->index();
            $table->string("column")->index();
            $table->string("value")->index();
            $table->string("level", 20);
            $table->timestamps();

            $table->unique(["sale_id", "column", "value"]);
        });

        Schema::create("antifrauds", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->string("name");
            $table->string("api");
            $table->unsignedInteger("antifraud_api_enum")->index("antifrauds_antifraud_api_enum_IDX");
            $table->string("environment", 50);
            $table->string("client_id");
            $table->string("client_secret");
            $table->string("merchant_id");
            $table->boolean("available_flag")->default(true);
            $table
                ->timestamp("created_at")
                ->nullable()
                ->index("antifrauds_created_at_IDX");
            $table
                ->timestamp("updated_at")
                ->nullable()
                ->index("antifrauds_updated_at_IDX");
        });

        Schema::create("api_tokens", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->unsignedInteger("user_id")->index();
            $table
                ->unsignedInteger("company_id")
                ->nullable()
                ->index("api_tokens_company_id_foreign");
            $table
                ->string("token_id", 100)
                ->nullable()
                ->index();
            $table->text("access_token")->nullable();
            $table->text("scopes")->nullable();
            $table
                ->tinyInteger("integration_type_enum")
                ->nullable()
                ->index("api_tokens_integration_type_enum_IDX");
            $table->string("description")->nullable();
            $table->string("postback", 1000)->nullable();
            $table
                ->timestamp("created_at")
                ->nullable()
                ->index("api_tokens_created_at_IDX");
            $table
                ->timestamp("updated_at")
                ->nullable()
                ->index("api_tokens_updated_at_IDX");
            $table->softDeletes()->index("api_tokens_deleted_at_IDX");
        });

        Schema::create("asaas_anticipation_requests", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->unsignedInteger("company_id")->index("asaas_anticipation_requests_company_id_foreign");
            $table->unsignedBigInteger("sale_id")->index("asaas_anticipation_requests_sale_id_foreign");
            $table->json("sent_data")->nullable();
            $table->json("response")->nullable();
            $table->timestamps();
        });

        Schema::create("asaas_backoffice_requests", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->unsignedInteger("company_id")->index("asaas_backoffice_requests_company_id_foreign");
            $table->json("sent_data")->nullable();
            $table->json("response")->nullable();
            $table->timestamps();
        });

        Schema::create("asaas_transfers", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table
                ->unsignedBigInteger("withdrawal_id")
                ->nullable()
                ->index("asaas_transfers_withdrawal_id_foreign");
            $table
                ->unsignedBigInteger("transfer_id")
                ->nullable()
                ->index("asaas_transfers_transfer_id_foreign");
            $table->string("transaction_id", 50)->nullable();
            $table->integer("value");
            $table->string("status", 15)->nullable();
            $table->json("sent_data")->nullable();
            $table->json("response")->nullable();
            $table
                ->tinyInteger("is_cloudfox")
                ->nullable()
                ->default(0);
            $table->timestamps();
        });

        Schema::create("astron_members_integrations", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->string("link");
            $table->string("token");
            $table->boolean("boleto_generated")->default(true);
            $table->boolean("boleto_paid")->default(true);
            $table->boolean("credit_card_refused")->default(true);
            $table->boolean("credit_card_paid")->default(true);
            $table->boolean("abandoned_cart")->default(true);
            $table->boolean("pix_generated")->default(true);
            $table->boolean("pix_paid")->default(true);
            $table->boolean("pix_expired")->default(true);
            $table->unsignedInteger("project_id")->index("astron_members_integrations_project_id_foreign");
            $table->unsignedInteger("user_id")->index("astron_members_integrations_user_id_foreign");
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create("benefits", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->string("name", 100);
            $table->text("description");
            $table->integer("level");
            $table->timestamps();
        });

        Schema::create("biometry_postbacks", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table
                ->unsignedInteger("user_id")
                ->nullable()
                ->index();
            $table
                ->unsignedBigInteger("user_biometry_resut_id")
                ->nullable()
                ->index();
            $table->string("vendor")->index();
            $table->json("postback_data")->nullable();
            $table->json("api_data")->nullable();
            $table
                ->unsignedTinyInteger("processed_flag")
                ->default(0)
                ->index();
            $table->dateTime("date_api_data")->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create("block_reason_sales", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->unsignedBigInteger("sale_id")->index("block_reason_sales_sale_id_foreign");
            $table->unsignedBigInteger("blocked_reason_id")->index("block_reason_sales_blocked_reason_id_foreign");
            $table
                ->tinyInteger("status")
                ->default(1)
                ->index();
            $table->string("observation");
            $table->timestamps();
        });

        Schema::create("block_reasons", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->string("reason");
            $table->tinyInteger("reason_enum")->nullable();
            $table->timestamps();
        });

        Schema::create("bonus_balances", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->unsignedInteger("user_id")->index("bonus_balances_user_id_foreign");
            $table->integer("total_value")->default(0);
            $table->integer("current_value")->default(0);
            $table->date("expires_at");
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create("braspag_backoffice_postbacks", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->json("data");
            $table->timestamps();
        });

        Schema::create("braspag_backoffice_requests", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->json("sent_data")->nullable();
            $table->json("response")->nullable();
            $table->timestamps();
            $table->unsignedInteger("company_id")->index("braspag_backoffice_requests_company_id_foreign");
        });

        Schema::create("cashbacks", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->unsignedInteger("user_id")->index("cashbacks_user_id_foreign");
            $table->unsignedInteger("company_id")->index("cashbacks_company_id_foreign");
            $table
                ->unsignedBigInteger("transaction_id")
                ->nullable()
                ->index("cashbacks_transaction_id_foreign");
            $table
                ->unsignedBigInteger("sale_id")
                ->nullable()
                ->index("cashbacks_sale_id_foreign");
            $table->integer("value");
            $table->integer("type_enum")->default(1);
            $table->integer("status")->default(1);
            $table->double("percentage", 8, 2)->nullable();
            $table->timestamps();
        });

        Schema::create("categories", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->string("name", 30)->index("categories_name_IDX");
            $table
                ->string("description", 100)
                ->nullable()
                ->index("categories_description_IDX");
            $table
                ->timestamp("created_at")
                ->nullable()
                ->index("categories_created_at_IDX");
            $table
                ->timestamp("updated_at")
                ->nullable()
                ->index("categories_updated_at_IDX");
            $table->softDeletes()->index("categories_deleted_at_IDX");
        });

        Schema::create("chargebacks", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->unsignedBigInteger("sale_id")->index("chargebacks_sale_id_foreign");
            $table->string("case_number", 100)->index();
            $table->tinyInteger("status_enum");
            $table->json("transaction")->nullable();
            $table->timestamps();
        });

        Schema::create("checkout_api_postbacks", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->unsignedInteger("company_id")->index("checkout_api_postbacks_company_id_foreign");
            $table->unsignedInteger("user_id")->index("checkout_api_postbacks_user_id_foreign");
            $table->json("sent_data")->nullable();
            $table->json("response")->nullable();
            $table->timestamps();
        });

        Schema::create("checkout_configs", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->unsignedInteger("project_id")->index();
            $table->integer("checkout_type_enum")->default(2);
            $table->boolean("checkout_logo_enabled")->default(false);
            $table->string("checkout_logo")->nullable();
            $table->boolean("checkout_favicon_enabled")->default(false);
            $table->integer("checkout_favicon_type")->default(1);
            $table->string("checkout_favicon")->nullable();
            $table->boolean("checkout_banner_enabled")->default(false);
            $table->integer("checkout_banner_type")->default(1);
            $table->string("checkout_banner")->nullable();
            $table->boolean("countdown_enabled")->default(false);
            $table->integer("countdown_time")->default(15);
            $table->string("countdown_finish_message")->nullable();
            $table->boolean("topbar_enabled")->default(false);
            $table->text("topbar_content")->nullable();
            $table->boolean("notifications_enabled")->default(false);
            $table->integer("notifications_interval")->default(30);
            $table->boolean("notification_buying_enabled")->default(false);
            $table->integer("notification_buying_minimum")->default(1);
            $table->boolean("notification_bought_30_minutes_enabled")->default(false);
            $table->integer("notification_bought_30_minutes_minimum")->default(1);
            $table->boolean("notification_bought_last_hour_enabled")->default(false);
            $table->integer("notification_bought_last_hour_minimum")->default(1);
            $table->boolean("notification_just_bought_enabled")->default(false);
            $table->integer("notification_just_bought_minimum")->default(1);
            $table->boolean("social_proof_enabled")->default(false);
            $table->text("social_proof_message")->nullable();
            $table->integer("social_proof_minimum")->default(15);
            $table->string("invoice_description")->nullable();
            $table->unsignedInteger("company_id")->index();
            $table->boolean("cpf_enabled")->default(true);
            $table->boolean("cnpj_enabled")->default(true);
            $table->boolean("credit_card_enabled")->default(true);
            $table->boolean("bank_slip_enabled")->default(true);
            $table->boolean("pix_enabled")->default(true);
            $table->boolean("quantity_selector_enabled")->default(true);
            $table->boolean("email_required")->default(true);
            $table->integer("installments_limit")->default(12);
            $table->integer("interest_free_installments")->default(1);
            $table->integer("preselected_installment")->default(12);
            $table->integer("bank_slip_due_days")->default(3);
            $table->integer("automatic_discount_credit_card")->default(0);
            $table->integer("automatic_discount_bank_slip")->default(0);
            $table->integer("automatic_discount_pix")->default(0);
            $table->boolean("post_purchase_message_enabled")->default(false);
            $table->string("post_purchase_message_title")->nullable();
            $table->text("post_purchase_message_content")->nullable();
            $table->boolean("whatsapp_enabled")->default(false);
            $table->string("support_phone")->nullable();
            $table->boolean("support_phone_verified")->default(false);
            $table->string("support_email")->nullable();
            $table->boolean("support_email_verified")->default(false);
            $table->integer("theme_enum")->default(1);
            $table->string("color_primary")->default("#4B8FEF");
            $table->string("color_secondary")->default("#313C52");
            $table->string("color_buy_button")->default("#23d07d");
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create("checkout_plans", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->unsignedBigInteger("checkout_id")->index("planos_checkout_checkout_foreign");
            $table->unsignedBigInteger("plan_id")->index("planos_checkout_plano_foreign");
            $table->string("amount");
            $table
                ->timestamp("created_at")
                ->nullable()
                ->index("checkout_plans_created_at_IDX");
            $table
                ->timestamp("updated_at")
                ->nullable()
                ->index("checkout_plans_updated_at_IDX");
            $table->softDeletes()->index("checkout_plans_deleted_at_IDX");
        });

        Schema::create("checkouts", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->unsignedInteger("project_id")->index("checkouts_projeto_foreign");
            $table
                ->unsignedBigInteger("affiliate_id")
                ->nullable()
                ->index("checkouts_affiliate_id_foreign");
            $table
                ->string("status")
                ->nullable()
                ->index("checkouts_status_IDX");
            $table
                ->integer("status_enum")
                ->default(0)
                ->index("checkouts_status_enum_IDX");
            $table
                ->string("operational_system")
                ->nullable()
                ->index("checkouts_operational_system_IDX");
            $table->integer("os_enum")->default(0);
            $table
                ->string("browser")
                ->nullable()
                ->index("checkouts_browser_IDX");
            $table
                ->char("id_log_session")
                ->nullable()
                ->index();
            $table
                ->string("ip")
                ->nullable()
                ->index("checkouts_ip_IDX");
            $table->text("ip_localization")->nullable();
            $table->string("ip_state")->nullable();
            $table
                ->string("city")
                ->nullable()
                ->index("checkouts_city_IDX");
            $table
                ->string("state")
                ->nullable()
                ->index("checkouts_state_IDX");
            $table
                ->string("state_name")
                ->nullable()
                ->index("checkouts_state_name_IDX");
            $table
                ->string("zip_code")
                ->nullable()
                ->index("checkouts_zip_code_IDX");
            $table
                ->string("country")
                ->nullable()
                ->index("checkouts_country_IDX");
            $table
                ->string("parameter")
                ->nullable()
                ->index("checkouts_parameter_IDX");
            $table->string("currency")->nullable();
            $table->string("lat")->nullable();
            $table->string("lon")->nullable();
            $table
                ->string("src")
                ->nullable()
                ->index("checkouts_src_IDX");
            $table
                ->boolean("is_mobile")
                ->default(true)
                ->index("checkouts_is_mobile_IDX");
            $table
                ->string("client_telephone")
                ->nullable()
                ->index("checkouts_client_telephone_IDX");
            $table
                ->string("client_name")
                ->nullable()
                ->index("checkouts_client_name_IDX");
            $table
                ->string("utm_source")
                ->nullable()
                ->index("checkouts_utm_source_IDX");
            $table
                ->string("utm_medium")
                ->nullable()
                ->index("checkouts_utm_medium_IDX");
            $table
                ->string("utm_campaign")
                ->nullable()
                ->index("checkouts_utm_campaign_IDX");
            $table
                ->string("utm_term")
                ->nullable()
                ->index("checkouts_utm_term_IDX");
            $table
                ->string("utm_content")
                ->nullable()
                ->index("checkouts_utm_content_IDX");
            $table
                ->integer("email_sent_amount")
                ->nullable()
                ->default(0)
                ->index("checkouts_email_sent_amount_IDX");
            $table
                ->integer("sms_sent_amount")
                ->nullable()
                ->default(0)
                ->index("checkouts_sms_sent_amount_IDX");
            $table
                ->unsignedInteger("template_type")
                ->default(1)
                ->index("checkouts_template_type_IDX");
            $table
                ->timestamp("updated_at")
                ->nullable()
                ->index("checkouts_updated_at_IDX");
            $table->softDeletes()->index("checkouts_deleted_at_IDX");
            $table
                ->timestamp("created_at")
                ->nullable()
                ->index("checkouts_created_at_IDX");
        });

        Schema::create("companies", function (Blueprint $table) {
            $table->increments("id");
            $table
                ->unsignedInteger("user_id")
                ->nullable()
                ->index("empresas_user_foreign");
            $table->string("fantasy_name")->index("companies_fantasy_name_IDX");
            $table->string("document", 100)->nullable();
            $table->string("zip_code")->nullable();
            $table->string("country")->default("brazil");
            $table->string("state")->nullable();
            $table->char("city")->nullable();
            $table->string("street")->nullable();
            $table->string("complement")->nullable();
            $table->string("neighborhood")->nullable();
            $table->string("number")->nullable();
            $table
                ->string("support_email")
                ->nullable()
                ->index("companies_support_email_IDX");
            $table
                ->string("support_telephone")
                ->nullable()
                ->index("companies_support_telephone_IDX");
            $table->bigInteger("cielo_balance")->default(0);
            $table->integer("asaas_balance")->default(0);
            $table->integer("vega_balance")->default(0);
            $table->tinyInteger("address_document_status")->default(1);
            $table->tinyInteger("contract_document_status")->default(1);
            $table->dateTime("date_last_document_notification")->nullable();
            $table
                ->unsignedInteger("company_type")
                ->nullable()
                ->index("companies_company_type_IDX");
            $table
                ->unsignedInteger("order_priority")
                ->default(0)
                ->index("companies_order_priority_IDX");
            $table->boolean("capture_transaction_enabled")->default(false);
            $table->unsignedInteger("account_type")->nullable();
            $table->boolean("active_flag")->default(true);
            $table
                ->string("gateway_tax")
                ->nullable()
                ->default("6.9");
            $table->boolean("tax_default")->default(true);
            $table->string("credit_card_tax")->default("6.9");
            $table->string("credit_card_rule")->default("percent");
            $table->string("pix_tax")->default("6.9");
            $table->string("pix_rule")->default("percent");
            $table->string("boleto_tax")->default("6.9");
            $table->string("boleto_rule")->default("percent");
            $table
                ->string("installment_tax")
                ->nullable()
                ->default("2.99");
            $table->string("checkout_tax")->default("0");
            $table
                ->integer("gateway_release_money_days")
                ->nullable()
                ->default(2);
            $table->dateTime("document_issue_date")->nullable();
            $table->string("document_issuer")->nullable();
            $table->string("document_issuer_state")->nullable();
            $table->string("extra_document")->nullable();
            $table->json("id_wall_result")->nullable();
            $table->date("id_wall_date_update")->nullable();
            $table->json("bureau_result")->nullable();
            $table
                ->string("transaction_tax")
                ->nullable()
                ->default("1.00");
            $table->boolean("block_checkout")->default(false);
            $table->integer("annual_income")->nullable();
            $table->json("situation")->nullable();
            $table
                ->timestamp("created_at")
                ->nullable()
                ->index("companies_created_at_IDX");
            $table
                ->timestamp("updated_at")
                ->nullable()
                ->index("companies_updated_at_IDX");
            $table->softDeletes()->index("companies_deleted_at_IDX");
        });

        Schema::create("company_adjustments", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->unsignedInteger("company_id")->index("company_adjustments_company_id_foreign");
            $table->bigInteger("adjustment_id");
            $table->string("adjustment_amount");
            $table->char("transaction_sign", 1)->nullable();
            $table->char("adjustment_type", 2)->nullable();
            $table->string("adjustment_amount_total");
            $table->string("adjustment_reason");
            $table->dateTime("date_adjustment");
            $table->dateTime("subseller_rate_closing_date")->nullable();
            $table->json("data")->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create("company_balance_logs", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->unsignedInteger("company_id")->index("company_balance_logs_company_id_foreign");
            $table->bigInteger("today_balance")->nullable();
            $table->bigInteger("pending_balance")->nullable();
            $table->bigInteger("available_balance")->nullable();
            $table->bigInteger("total_balance")->nullable();
            $table->timestamps();
        });

        Schema::create("company_balances", function (Blueprint $table) {
            $table->increments("id");
            $table->unsignedInteger("company_id")->index();
            $table->integer("vega_available_balance")->default(0);
            $table->integer("vega_pending_balance")->default(0);
            $table->integer("vega_blocked_balance")->default(0);
            $table->integer("vega_total_balance")->default(0);
            $table->integer("vega_available_balance_with_blocked")->default(0);
            $table->integer("vega_pending_balance_with_blocked")->default(0);
            $table->integer("vega_total_balance_with_blocked")->default(0);
            $table->integer("asaas_available_balance")->default(0);
            $table->integer("asaas_pending_balance")->default(0);
            $table->integer("asaas_blocked_balance")->default(0);
            $table->integer("asaas_total_balance")->default(0);
            $table->integer("asaas_available_balance_with_blocked")->default(0);
            $table->integer("asaas_pending_balance_with_blocked")->default(0);
            $table->integer("asaas_total_balance_with_blocked")->default(0);
            $table->integer("cielo_available_balance")->default(0);
            $table->integer("cielo_pending_balance")->default(0);
            $table->integer("cielo_blocked_balance")->default(0);
            $table->integer("cielo_total_balance")->default(0);
            $table->integer("cielo_available_balance_with_blocked")->default(0);
            $table->integer("cielo_pending_balance_with_blocked")->default(0);
            $table->integer("cielo_total_balance_with_blocked")->default(0);
            $table->integer("getnet_available_balance")->default(0);
            $table->integer("getnet_pending_balance")->default(0);
            $table->integer("getnet_blocked_balance")->default(0);
            $table->integer("getnet_total_balance")->default(0);
            $table->integer("getnet_available_balance_with_blocked")->default(0);
            $table->integer("getnet_pending_balance_with_blocked")->default(0);
            $table->integer("getnet_total_balance_with_blocked")->default(0);
            $table->integer("gerencianet_available_balance")->default(0);
            $table->integer("gerencianet_pending_balance")->default(0);
            $table->integer("gerencianet_blocked_balance")->default(0);
            $table->integer("gerencianet_total_balance")->default(0);
            $table->integer("total_balance")->default(0);
            $table->integer("total_balance_with_blocked")->default(0);
            $table->integer("gerencianet_available_balance_with_blocked")->default(0);
            $table->integer("gerencianet_pending_balance_with_blocked")->default(0);
            $table->integer("gerencianet_total_balance_with_blocked")->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create("company_bank_accounts", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table
                ->unsignedInteger("company_id")
                ->nullable()
                ->index("company_bank_accounts_company_id_foreign");
            $table->enum("transfer_type", ["PIX", "TED"])->default("PIX");
            $table->enum("type_key_pix", ["CHAVE_ALEATORIA", "EMAIL", "TELEFONE", "CPF", "CNPJ"])->nullable();
            $table->string("key_pix")->nullable();
            $table->string("bank", 10)->nullable();
            $table->string("agency", 10)->nullable();
            $table->string("agency_digit", 3)->nullable();
            $table->string("account", 15)->nullable();
            $table->string("account_digit", 3)->nullable();
            $table->tinyInteger("is_default")->default(0);
            $table->enum("status", ["PENDING", "VALIDATING", "VERIFIED", "REFUSED"]);
            $table->string("gateway_transaction_id")->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create("company_documents", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->unsignedInteger("company_id")->index();
            $table->string("document_url", 500);
            $table->tinyInteger("document_type_enum")->index("company_documents_document_type_enum_IDX");
            $table
                ->tinyInteger("status")
                ->nullable()
                ->index("company_documents_status_IDX");
            $table->string("refused_reason")->nullable();
            $table
                ->timestamp("created_at")
                ->nullable()
                ->index("company_documents_created_at_IDX");
            $table
                ->timestamp("updated_at")
                ->nullable()
                ->index("company_documents_updated_at_IDX");
        });

        Schema::create("convertax_integrations", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->text("link");
            $table->integer("value");
            $table->boolean("boleto_generated")->default(true);
            $table->boolean("boleto_paid")->default(true);
            $table->boolean("credit_card_refused")->default(true);
            $table->boolean("credit_card_paid")->default(true);
            $table->boolean("abandoned_cart")->default(true);
            $table->unsignedInteger("project_id")->index("convertax_integrations_project_id_foreign");
            $table->unsignedInteger("user_id")->index("convertax_integrations_user_id_foreign");
            $table
                ->timestamp("created_at")
                ->nullable()
                ->index("convertax_integrations_created_at_IDX");
            $table
                ->timestamp("updated_at")
                ->nullable()
                ->index("convertax_integrations_updated_at_IDX");
            $table->softDeletes()->index("convertax_integrations_deleted_at_IDX");
        });

        Schema::create("currency_quotations", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->string("currency");
            $table
                ->unsignedTinyInteger("currency_type")
                ->default(1)
                ->index("currency_quotations_currency_type_IDX");
            $table->text("http_response")->nullable();
            $table->string("value");
            $table
                ->timestamp("created_at")
                ->nullable()
                ->index("currency_quotations_created_at_IDX");
            $table
                ->timestamp("updated_at")
                ->nullable()
                ->index("currency_quotations_updated_at_IDX");
        });

        Schema::create("customer_bank_accounts", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->unsignedBigInteger("customer_id")->index("customer_bank_accounts_customer_id_foreign");
            $table->string("holder_name")->index("customer_bank_accounts_holder_name_IDX");
            $table->string("holder_document")->index("customer_bank_accounts_holder_document_IDX");
            $table->unsignedSmallInteger("account_type")->index("customer_bank_accounts_account_type_IDX");
            $table->unsignedSmallInteger("bank");
            $table->string("agency");
            $table->string("account");
            $table
                ->timestamp("created_at")
                ->nullable()
                ->index("customer_bank_accounts_created_at_IDX");
            $table
                ->timestamp("updated_at")
                ->nullable()
                ->index("customer_bank_accounts_updated_at_IDX");
            $table->softDeletes()->index("customer_bank_accounts_deleted_at_IDX");
        });

        Schema::create("customer_bureau_results", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->unsignedBigInteger("customer_id")->index("customer_bureau_results_customer_id_foreign");
            $table->string("vendor", 20);
            $table->json("send_data")->nullable();
            $table->json("result_data")->nullable();
            $table->json("exception")->nullable();
            $table->timestamps();
        });

        Schema::create("customer_cards", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->unsignedBigInteger("customer_id")->index("client_cards_client_id_foreign");
            $table->integer("first_six_digits")->index("customer_cards_first_six_digits_IDX");
            $table->integer("last_four_digits")->index("customer_cards_last_four_digits_IDX");
            $table->string("card_token");
            $table->string("association_code");
            $table
                ->timestamp("created_at")
                ->nullable()
                ->index("customer_cards_created_at_IDX");
            $table
                ->timestamp("updated_at")
                ->nullable()
                ->index("customer_cards_updated_at_IDX");
            $table
                ->boolean("deleted_by_user")
                ->default(false)
                ->index("customer_cards_deleted_by_user_IDX");
        });

        Schema::create("customer_idwall_results", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->unsignedBigInteger("customer_id")->index();
            $table->json("send_data");
            $table->json("received_data");
            $table->json("exception");
            $table
                ->timestamp("created_at")
                ->nullable()
                ->index("customer_idwall_results_created_at_IDX");
            $table
                ->timestamp("updated_at")
                ->nullable()
                ->index("customer_idwall_results_updated_at_IDX");
        });

        Schema::create("customer_withdrawals", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->unsignedBigInteger("customer_id")->index("customer_withdrawals_customer_id_foreign");
            $table->integer("value");
            $table->integer("status")->index("customer_withdrawals_status_IDX");
            $table->json("bank_account");
            $table->text("observation")->nullable();
            $table->string("file")->nullable();
            $table->dateTime("created_at")->nullable();
            $table->dateTime("updated_at")->nullable();
            $table->dateTime("deleted_at")->nullable();
        });

        Schema::create("customers", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->string("name", 100)->index("customers_name_IDX");
            $table->string("document", 21)->index();
            $table->string("email", 60)->index("customers_email_IDX");
            $table->boolean("email_verified")->default(false);
            $table->string("telephone", 20)->index("customers_telephone_IDX");
            $table->string("password")->nullable();
            $table->rememberToken();
            $table->bigInteger("balance")->default(0);
            $table->boolean("blocked_withdrawal")->default(false);
            $table->date("birthday")->nullable();
            $table
                ->unsignedBigInteger("id_kapsula_client")
                ->nullable()
                ->index("customers_id_kapsula_client_IDX");
            $table
                ->string("id_zoop_buyer")
                ->nullable()
                ->index("customers_id_zoop_buyer_IDX");
            $table->string("asaas_buyer_id")->nullable();
            $table
                ->timestamp("created_at")
                ->nullable()
                ->index("customers_created_at_IDX");
            $table
                ->timestamp("updated_at")
                ->nullable()
                ->index("customers_updated_at_IDX");
            $table->softDeletes()->index("customers_deleted_at_IDX");

            $table->index(["email"]);
        });

        Schema::create("dashboard_notifications", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->unsignedInteger("user_id")->index();
            $table->bigInteger("subject_id");
            $table->string("subject_type");
            $table->timestamp("read_at")->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create("deliveries", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table
                ->unsignedBigInteger("customer_id")
                ->nullable()
                ->index("deliveries_customer_id_foreign");
            $table
                ->string("receiver_name")
                ->nullable()
                ->index("deliveries_receiver_name_IDX");
            $table->string("zip_code", 150)->index("deliveries_zip_code_IDX");
            $table->char("country", 150);
            $table->string("state", 30);
            $table->string("city", 60);
            $table->string("neighborhood")->nullable();
            $table->string("street", 120);
            $table->string("number");
            $table->text("complement")->nullable();
            $table
                ->string("type")
                ->nullable()
                ->index("deliveries_type_IDX");
            $table
                ->integer("melhorenvio_carrier_id")
                ->nullable()
                ->default(0);
            $table
                ->string("melhorenvio_order_id")
                ->nullable()
                ->index();
            $table
                ->timestamp("created_at")
                ->nullable()
                ->index("deliveries_created_at_IDX");
            $table
                ->timestamp("updated_at")
                ->nullable()
                ->index("deliveries_updated_at_IDX");
            $table->softDeletes()->index("deliveries_deleted_at_IDX");
        });

        Schema::create("digitalmanager_integrations", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->unsignedInteger("user_id")->index();
            $table->unsignedInteger("project_id")->index();
            $table->string("api_token", 50);
            $table->string("url");
            $table->boolean("billet_generated")->default(true);
            $table->boolean("billet_paid")->default(true);
            $table->boolean("credit_card_refused")->default(true);
            $table->boolean("credit_card_paid")->default(true);
            $table->boolean("abandoned_cart")->default(true);
            $table->softDeletes()->index("digitalmanager_integrations_deleted_at_IDX");
            $table
                ->timestamp("created_at")
                ->nullable()
                ->index("digitalmanager_integrations_created_at_IDX");
            $table
                ->timestamp("updated_at")
                ->nullable()
                ->index("digitalmanager_integrations_updated_at_IDX");
        });

        Schema::create("digitalmanager_sent", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->text("data");
            $table->text("response");
            $table
                ->integer("sent_status")
                ->nullable()
                ->index("digitalmanager_sent_sent_status_IDX");
            $table
                ->unsignedInteger("event_sale")
                ->nullable()
                ->index("digitalmanager_sent_event_sale_IDX");
            $table
                ->unsignedBigInteger("instance_id")
                ->nullable()
                ->index("digitalmanager_sent_instance_id_IDX");
            $table
                ->string("instance")
                ->nullable()
                ->index("digitalmanager_sent_instance_IDX");
            $table
                ->unsignedBigInteger("digitalmanager_integration_id")
                ->nullable()
                ->index("digitalmanager_sent_digitalmanager_integration_id_foreign");
            $table
                ->timestamp("created_at")
                ->nullable()
                ->index("digitalmanager_sent_created_at_IDX");
            $table
                ->timestamp("updated_at")
                ->nullable()
                ->index("digitalmanager_sent_updated_at_IDX");
        });

        Schema::create("discount_coupons", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table
                ->unsignedInteger("project_id")
                ->nullable()
                ->index("cupons_projeto_foreign");
            $table->string("name", 20)->index("discount_coupons_name_IDX");
            $table->boolean("type")->index("discount_coupons_type_IDX");
            $table->string("value");
            $table->string("code", 200)->nullable();
            $table->dateTime("expires")->nullable();
            $table
                ->boolean("status")
                ->default(true)
                ->index("discount_coupons_status_IDX");
            $table
                ->integer("rule_value")
                ->default(0)
                ->index("discount_coupons_rule_value_IDX");
            $table->json("progressive_rules")->nullable();
            $table->json("plans")->nullable();
            $table->tinyInteger("discount")->default(0);
            $table
                ->timestamp("created_at")
                ->nullable()
                ->index("discount_coupons_created_at_IDX");
            $table
                ->timestamp("updated_at")
                ->nullable()
                ->index("discount_coupons_updated_at_IDX");
            $table->softDeletes()->index("discount_coupons_deleted_at_IDX");
        });

        Schema::create("domains", function (Blueprint $table) {
            $table->increments("id");
            $table
                ->unsignedInteger("project_id")
                ->nullable()
                ->index("dominios_projeto_foreign");
            $table->string("cloudflare_domain_id")->nullable();
            $table
                ->string("name", 200)
                ->nullable()
                ->index("domains_name_IDX");
            $table
                ->integer("status")
                ->nullable()
                ->default(1)
                ->index("domains_status_IDX");
            $table->string("sendgrid_id")->nullable();
            $table
                ->timestamp("created_at")
                ->nullable()
                ->index("domains_created_at_IDX");
            $table
                ->timestamp("updated_at")
                ->nullable()
                ->index("domains_updated_at_IDX");
            $table->softDeletes()->index("domains_deleted_at_IDX");
        });

        Schema::create("domains_records", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->unsignedInteger("domain_id")->index();
            $table->string("cloudflare_record_id");
            $table->string("type");
            $table->string("name")->index("domains_records_name_IDX");
            $table->text("content");
            $table
                ->tinyInteger("system_flag")
                ->default(1)
                ->index("domains_records_system_flag_IDX");
            $table
                ->integer("priority")
                ->default(0)
                ->index("domains_records_priority_IDX");
            $table
                ->timestamp("created_at")
                ->nullable()
                ->index("domains_records_created_at_IDX");
            $table
                ->timestamp("updated_at")
                ->nullable()
                ->index("domains_records_updated_at_IDX");
            $table->integer("proxy")->default(1);
        });

        Schema::create("ethoca_postbacks", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table
                ->unsignedBigInteger("sale_id")
                ->nullable()
                ->index("ethoca_postbacks_sale_id_foreign");
            $table->json("data");
            $table->tinyInteger("is_cloudfox")->default(0);
            $table->tinyInteger("processed_flag")->default(0);
            $table->json("machine_result")->nullable();
            $table->timestamps();
        });

        Schema::create("failed_jobs", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table
                ->string("uuid")
                ->nullable()
                ->unique();
            $table->text("connection");
            $table->text("queue");
            $table->longText("payload");
            $table->longText("exception");
            $table->timestamp("failed_at")->useCurrent();
        });

        Schema::create("gateway_configs", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->string("name");
            $table
                ->unsignedBigInteger("gateway_id")
                ->nullable()
                ->index();
            $table->string("type");
            $table->tinyInteger("type_enum");
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create("gateway_flag_taxes", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->unsignedBigInteger("gateway_flag_id")->index("gateway_flag_taxes_gateway_flag_id_foreign");
            $table->integer("installments");
            $table
                ->tinyInteger("type_enum")
                ->index("gateway_flag_taxes_type_enum_IDX")
                ->comment("1 - credit  2 - debit");
            $table->decimal("percent");
            $table
                ->boolean("active_flag")
                ->default(true)
                ->index("gateway_flag_taxes_active_flag_IDX");
            $table
                ->timestamp("created_at")
                ->nullable()
                ->index("gateway_flag_taxes_created_at_IDX");
            $table
                ->timestamp("updated_at")
                ->nullable()
                ->index("gateway_flag_taxes_updated_at_IDX");
        });

        Schema::create("gateway_flags", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table
                ->unsignedBigInteger("gateway_id")
                ->nullable()
                ->index("gateway_flags_gateway_id_foreign");
            $table->string("name")->index("gateway_flags_name_IDX");
            $table->string("slug");
            $table->tinyInteger("card_flag_enum")->index("gateway_flags_card_flag_enum_IDX");
            $table
                ->boolean("active_flag")
                ->default(true)
                ->index("gateway_flags_active_flag_IDX");
            $table
                ->timestamp("created_at")
                ->nullable()
                ->index("gateway_flags_created_at_IDX");
            $table
                ->timestamp("updated_at")
                ->nullable()
                ->index("gateway_flags_updated_at_IDX");
        });

        Schema::create("gateway_postbacks", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table
                ->unsignedBigInteger("sale_id")
                ->nullable()
                ->index();
            $table
                ->unsignedBigInteger("gateway_id")
                ->nullable()
                ->index();
            $table->string("reference_id")->nullable();
            $table->json("data");
            $table->unsignedTinyInteger("gateway_enum")->index();
            $table->string("gateway_postback_type")->nullable();
            $table->string("gateway_status")->nullable();
            $table->string("gateway_payment_type")->nullable();
            $table->string("description")->nullable();
            $table->unsignedInteger("amount")->nullable();
            $table
                ->unsignedTinyInteger("processed_flag")
                ->default(0)
                ->index();
            $table
                ->unsignedTinyInteger("postback_valid_flag")
                ->default(0)
                ->index();
            $table
                ->boolean("pay_postback_flag")
                ->default(false)
                ->index("gateway_postbacks_pay_postback_flag_IDX");
            $table->softDeletes()->index("gateway_postbacks_deleted_at_IDX");
            $table
                ->timestamp("created_at")
                ->nullable()
                ->index("gateway_postbacks_created_at_IDX");
            $table->timestamp("updated_at")->nullable();
            $table->text("machine_result_old")->nullable();
            $table->json("machine_result")->nullable();
        });

        Schema::create("gateways", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->unsignedTinyInteger("gateway_enum")->index();
            $table->string("name")->index("gateways_name_IDX");
            $table->text("json_config");
            $table
                ->tinyInteger("production_flag")
                ->default(0)
                ->index("gateways_production_flag_IDX");
            $table
                ->tinyInteger("enabled_flag")
                ->default(0)
                ->index("gateways_enabled_flag_IDX");
            $table->softDeletes()->index("gateways_deleted_at_IDX");
            $table
                ->timestamp("created_at")
                ->nullable()
                ->index("gateways_created_at_IDX");
            $table->timestamp("updated_at")->nullable();
        });

        Schema::create("gateways_backoffice_requests", function (Blueprint $table) {
            $table->increments("id");
            $table->unsignedInteger("company_id")->index("gateways_backoffice_requests_company_id_foreign");
            $table->unsignedBigInteger("gateway_id")->index("gateways_backoffice_requests_gateway_id_foreign");
            $table->json("sent_data")->nullable();
            $table->json("response")->nullable();
            $table->timestamps();
        });

        Schema::create("gateways_companies_credentials", function (Blueprint $table) {
            $table->increments("id");
            $table->unsignedInteger("company_id")->index("gateways_companies_credentials_company_id_foreign");
            $table->unsignedBigInteger("gateway_id")->index("gateways_companies_credentials_gateway_id_foreign");
            $table->tinyInteger("gateway_status")->nullable();
            $table->string("gateway_subseller_id", 50)->nullable();
            $table->string("gateway_api_key", 100)->nullable();
            $table->tinyInteger("capture_transaction_enabled")->nullable();
            $table->tinyInteger("has_transfers_webhook")->nullable();
            $table->tinyInteger("has_charges_webhook")->nullable();
            $table->timestamps();
        });

        Schema::create("getnet_backoffice_requests", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table
                ->unsignedInteger("company_id")
                ->nullable()
                ->index("getnet_backoffice_requests_company_id_foreign");
            $table->json("sent_data")->nullable();
            $table->json("response")->nullable();
            $table->timestamps();
        });

        Schema::create("getnet_chargeback_details", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->string("filters")->nullable();
            $table->json("body")->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table
                ->unsignedBigInteger("getnet_chargeback_id")
                ->nullable()
                ->index();
        });

        Schema::create("getnet_chargebacks", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->unsignedBigInteger("sale_id")->index();
            $table->unsignedInteger("company_id")->index();
            $table->unsignedInteger("project_id")->index();
            $table->unsignedInteger("user_id")->index();
            $table->date("transaction_date")->nullable();
            $table->date("installment_date")->nullable();
            $table->date("adjustment_date")->nullable();
            $table->decimal("adjustment_amount")->nullable();
            $table->integer("amount")->nullable();
            $table->boolean("is_debited")->default(false);
            $table->integer("tax")->default(0);
            $table->date("debited_at")->nullable();
            $table->json("body")->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create("getnet_postbacks", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->json("data");
            $table->timestamps();
        });

        Schema::create("getnet_searches", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table
                ->unsignedInteger("company_id")
                ->nullable()
                ->index("getnet_searches_company_id_foreign");
            $table->string("subseller_id")->nullable();
            $table->string("query_params")->nullable();
            $table->dateTime("requested_at")->nullable();
            $table->dateTime("ended_at")->nullable();
            $table->mediumInteger("time_get_api_data")->nullable();
            $table->mediumInteger("time_script_execution")->nullable();
            $table->unsignedInteger("list_transactions_count")->nullable();
            $table->longText("list_transactions_node")->nullable();
            $table->unsignedInteger("commission_count")->nullable();
            $table->longText("commission_node")->nullable();
            $table->unsignedInteger("adjustments_count")->nullable();
            $table->longText("adjustments_node")->nullable();
            $table->unsignedInteger("chargeback_count")->nullable();
            $table->longText("chargeback_node")->nullable();
            $table->timestamps();
        });

        Schema::create("getnet_transactions", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table
                ->unsignedInteger("company_id")
                ->nullable()
                ->index("getnet_transactions_company_id_foreign");
            $table
                ->unsignedBigInteger("sale_id")
                ->nullable()
                ->index("getnet_transactions_sale_id_foreign");
            $table->string("adjustment_id")->nullable();
            $table->string("hash_id")->nullable();
            $table->string("order_id")->nullable();
            $table->enum("type", [
                "WAITING_FOR_VALID_POST",
                "WAITING_LIQUIDATION",
                "WAITING_WITHDRAWAL",
                "WAITING_RELEASE",
                "LIQUIDATED",
                "REVERSED",
                "ADJUSTMENT_CREDIT",
                "ADJUSTMENT_DEBIT",
                "PENDING_DEBIT",
                "WRONG",
            ]);
            $table->enum("type_register", [
                "TRANSACTION_SUMMARY",
                "TRANSACTION_DETAIL",
                "COMMISSION",
                "ADJUST",
                "CHARGEBACK",
            ]);
            $table
                ->enum("status_code", [
                    "APPROVED",
                    "WAITING",
                    "PENDING",
                    "PENDING_PAYMENT",
                    "TIMEOUT",
                    "UNDONE",
                    "NONEXISTENT",
                    "ADMINISTRATOR_DENIED",
                    "RETURNED",
                    "REPEATED",
                    "CONCILIATION_REVERSED",
                    "CANCELED_WITHOUT_CONFIRMATION",
                    "DENIED_MGM",
                ])
                ->nullable();
            $table->string("bank")->nullable();
            $table->string("agency")->nullable();
            $table->string("account_number")->nullable();
            $table->string("release_status")->nullable();
            $table->dateTime("transaction_date")->nullable();
            $table->dateTime("confirmation_date")->nullable();
            $table->integer("amount")->nullable();
            $table->dateTime("payment_date")->nullable();
            $table->dateTime("subseller_rate_closing_date")->nullable();
            $table->dateTime("subseller_rate_confirm_date")->nullable();
            $table->string("transaction_sign")->nullable();
            $table->string("description")->nullable();
            $table->timestamps();
        });

        Schema::create("health_check_result_history_items", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->string("check_name");
            $table->string("check_label");
            $table->string("status");
            $table->text("notification_message")->nullable();
            $table->string("short_summary")->nullable();
            $table->json("meta");
            $table->timestamp("ended_at");
            $table->char("batch", 36)->index();
            $table
                ->timestamp("created_at")
                ->nullable()
                ->index();
            $table->timestamp("updated_at")->nullable();
        });

        Schema::create("hotbillet_integrations", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->string("link");
            $table->boolean("boleto_generated")->default(true);
            $table->boolean("boleto_paid")->default(true);
            $table->boolean("credit_card_refused")->default(true);
            $table->boolean("credit_card_paid")->default(true);
            $table->boolean("abandoned_cart")->default(true);
            $table->boolean("pix_generated")->default(true);
            $table->boolean("pix_paid")->default(true);
            $table->boolean("pix_expired")->default(true);
            $table->unsignedInteger("project_id")->index("hotbillet_integrations_project_id_foreign");
            $table->unsignedInteger("user_id")->index("hotbillet_integrations_user_id_foreign");
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create("hotzapp_integrations", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->text("link");
            $table->boolean("boleto_generated")->default(true);
            $table->boolean("boleto_paid")->default(true);
            $table->boolean("credit_card_refused")->default(true);
            $table->boolean("credit_card_paid")->default(true);
            $table->boolean("abandoned_cart")->default(true);
            $table
                ->tinyInteger("pix_generated")
                ->nullable()
                ->default(1);
            $table
                ->tinyInteger("pix_paid")
                ->nullable()
                ->default(1);
            $table->unsignedInteger("project_id")->index("hotzapp_integrations_project_id_foreign");
            $table->unsignedInteger("user_id")->index("hotzapp_integrations_user_id_foreign");
            $table
                ->timestamp("created_at")
                ->nullable()
                ->index("hotzapp_integrations_created_at_IDX");
            $table->timestamp("updated_at")->nullable();
            $table->softDeletes()->index("hotzapp_integrations_deleted_at_IDX");
        });

        Schema::create("integration_logs", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->string("source_table", 30);
            $table->integer("source_id");
            $table->mediumText("request")->nullable();
            $table->mediumText("response")->nullable();
            $table->string("api", 30);
            $table->timestamps();
        });

        Schema::create("invitations", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table
                ->unsignedInteger("invite")
                ->nullable()
                ->index("convites_user_convite_foreign");
            $table
                ->unsignedInteger("user_invited")
                ->nullable()
                ->index("convites_user_convidado_foreign");
            $table->string("email_invited")->index("invitations_email_invited_IDX");
            $table
                ->integer("status")
                ->nullable()
                ->default(2)
                ->index("invitations_status_IDX");
            $table->date("register_date")->nullable();
            $table
                ->unsignedInteger("company_id")
                ->nullable()
                ->index("convites_empresa_foreign");
            $table->date("expiration_date")->nullable();
            $table->string("parameter")->nullable();
            $table
                ->timestamp("created_at")
                ->nullable()
                ->index("invitations_created_at_IDX");
            $table->timestamp("updated_at")->nullable();
            $table->softDeletes()->index("invitations_deleted_at_IDX");
        });

        Schema::create("iugu_credit_card_charges", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table
                ->unsignedBigInteger("sale_id")
                ->nullable()
                ->index();
            $table->string("token_id", 100);
            $table->string("brand", 50)->nullable();
            $table->string("customer_id", 100);
            $table->string("payment_id", 100);
            $table->string("invoice_id", 100)->nullable();
            $table->timestamps();
        });

        Schema::create("jobs", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->string("queue")->index();
            $table->longText("payload");
            $table->unsignedTinyInteger("attempts");
            $table->unsignedInteger("reserved_at")->nullable();
            $table->unsignedInteger("available_at");
            $table->unsignedInteger("created_at");
        });

        Schema::create("logs", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->text("id_log_session")->nullable();
            $table->text("plan")->nullable();
            $table->text("event")->nullable();
            $table->text("user_agent")->nullable();
            $table->text("access_hour")->nullable();
            $table->text("horary")->nullable();
            $table->text("operational_system")->nullable();
            $table->text("browser")->nullable();
            $table->text("forward")->nullable();
            $table->text("reference")->nullable();
            $table->text("name")->nullable();
            $table->text("email")->nullable();
            $table->text("document")->nullable();
            $table->text("telephone")->nullable();
            $table->text("zip_code")->nullable();
            $table->text("street")->nullable();
            $table->text("number")->nullable();
            $table->text("neighborhood")->nullable();
            $table->text("city")->nullable();
            $table->text("state")->nullable();
            $table->text("shipment_value")->nullable();
            $table->text("cupon_value")->nullable();
            $table->text("total_value")->nullable();
            $table->boolean("card_number")->nullable();
            $table->text("card_name")->nullable();
            $table->text("card_document")->nullable();
            $table->boolean("card_month")->nullable();
            $table->boolean("card_year")->nullable();
            $table->text("installments")->nullable();
            $table->text("error")->nullable();
            $table
                ->timestamp("created_at")
                ->nullable()
                ->index("logs_created_at_IDX");
            $table->timestamp("updated_at")->nullable();
            $table
                ->unsignedBigInteger("checkout_id")
                ->nullable()
                ->index("logs_checkout_id_foreign");
        });

        Schema::create("manager_2auth_tokens", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->unsignedInteger("user_id")->index("manager_2auth_tokens_user_id_foreign");
            $table->string("code");
            $table->timestamp("expires_at");
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create("manager_to_sirius_logins", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->unsignedInteger("manager_user_id")->index("manager_to_sirius_logins_manager_user_id_foreign");
            $table->unsignedInteger("sirius_user_id")->index("manager_to_sirius_logins_sirius_user_id_foreign");
            $table->boolean("is_active")->default(true);
            $table
                ->string("token", 60)
                ->nullable()
                ->unique();
            $table->timestamps();
        });

        Schema::create("melhorenvio_integrations", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->unsignedInteger("user_id")->index();
            $table->string("name");
            $table->text("access_token")->nullable();
            $table->text("refresh_token")->nullable();
            $table->timestamp("expiration")->nullable();
            $table->boolean("completed")->default(false);
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create("model_has_permissions", function (Blueprint $table) {
            $table->unsignedInteger("permission_id");
            $table->string("model_type");
            $table->unsignedBigInteger("model_id");

            $table->index(["model_id", "model_type"]);
            $table->primary(["permission_id", "model_id", "model_type"]);
        });

        Schema::create("model_has_roles", function (Blueprint $table) {
            $table->unsignedInteger("role_id");
            $table->string("model_type");
            $table->unsignedBigInteger("model_id");

            $table->index(["model_id", "model_type"]);
            $table->primary(["role_id", "model_id", "model_type"]);
        });

        Schema::create("monitored_scheduled_task_log_items", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->unsignedBigInteger("monitored_scheduled_task_id")->index("fk_scheduled_task_id");
            $table->string("type");
            $table->json("meta")->nullable();
            $table->timestamps();
        });

        Schema::create("monitored_scheduled_tasks", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->string("name");
            $table->string("type")->nullable();
            $table->string("cron_expression");
            $table->string("timezone")->nullable();
            $table->string("ping_url")->nullable();
            $table->dateTime("last_started_at")->nullable();
            $table->dateTime("last_finished_at")->nullable();
            $table->dateTime("last_failed_at")->nullable();
            $table->dateTime("last_skipped_at")->nullable();
            $table->dateTime("registered_on_oh_dear_at")->nullable();
            $table->dateTime("last_pinged_at")->nullable();
            $table->integer("grace_time_in_minutes");
            $table->timestamps();
        });

        Schema::create("nethone_antifraud_transaction", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->unsignedBigInteger("sale_id")->index("nethone_antifraud_transaction_sale_id_foreign");
            $table
                ->string("inquiry_id")
                ->nullable()
                ->index();
            $table->unsignedBigInteger("transaction_id")->nullable();
            $table->json("result")->nullable();
            $table->timestamps();
        });

        Schema::create("notazz_integrations", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->unsignedInteger("project_id")->index();
            $table->unsignedInteger("user_id")->index();
            $table->text("token_webhook");
            $table->text("token_api");
            $table->text("token_logistics")->nullable();
            $table
                ->tinyInteger("invoice_type")
                ->default(1)
                ->index("notazz_integrations_invoice_type_IDX");
            $table->unsignedInteger("pending_days")->default(1);
            $table->unsignedTinyInteger("discount_plataform_tax_flag")->default(0);
            $table->unsignedTinyInteger("generate_zero_invoice_flag")->default(1);
            $table
                ->boolean("active_flag")
                ->default(true)
                ->index("notazz_integrations_active_flag_IDX");
            $table->dateTime("start_date")->nullable();
            $table->dateTime("retroactive_generated_date")->nullable();
            $table
                ->timestamp("created_at")
                ->nullable()
                ->index("notazz_integrations_created_at_IDX");
            $table->timestamp("updated_at")->nullable();
            $table->softDeletes()->index("notazz_integrations_deleted_at_IDX");
        });

        Schema::create("notazz_invoices", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->unsignedBigInteger("sale_id")->index("notazz_invoices_sale_id_foreign");
            $table
                ->unsignedBigInteger("currency_quotation_id")
                ->nullable()
                ->index("notazz_invoices_currency_quotation_id_foreign");
            $table->unsignedBigInteger("notazz_integration_id")->index("notazz_invoices_notazz_integration_id_foreign");
            $table->tinyInteger("invoice_type")->default(1);
            $table->text("notazz_id")->nullable();
            $table->string("external_id")->nullable();
            $table->tinyInteger("status")->index("notazz_invoices_status_IDX");
            $table->tinyInteger("canceled_flag")->index();
            $table->dateTime("schedule")->index("notazz_invoices_schedule_IDX");
            $table->integer("attempts")->default(0);
            $table->text("xml")->nullable();
            $table->text("pdf")->nullable();
            $table->string("logistic_id")->nullable();
            $table->string("notazz_status")->nullable();
            $table->text("data_json")->nullable();
            $table->integer("return_http_code")->nullable();
            $table->text("return_message")->nullable();
            $table->string("postback_message")->nullable();
            $table->string("date_rejected")->nullable();
            $table->string("date_canceled")->nullable();
            $table->string("return_status")->nullable();
            $table->dateTime("date_error")->nullable();
            $table->dateTime("date_completed")->nullable();
            $table->dateTime("date_sent")->nullable();
            $table->dateTime("date_pending")->nullable();
            $table->dateTime("date_last_attempt")->nullable();
            $table->integer("max_attempts")->default(20);
            $table
                ->timestamp("created_at")
                ->nullable()
                ->index("notazz_invoices_created_at_IDX");
            $table->timestamp("updated_at")->nullable();
            $table->softDeletes()->index("notazz_invoices_deleted_at_IDX");
        });

        Schema::create("notazz_sent_histories", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->unsignedBigInteger("notazz_invoice_id")->index();
            $table->tinyInteger("sent_type_enum")->index("notazz_sent_histories_sent_type_enum_IDX");
            $table->string("url");
            $table->text("data_sent")->nullable();
            $table->text("response")->nullable();
            $table
                ->timestamp("created_at")
                ->nullable()
                ->index("notazz_sent_histories_created_at_IDX");
            $table->timestamp("updated_at")->nullable();
        });

        Schema::create("notificacoes_inteligentes_integrations", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->string("link");
            $table->string("token");
            $table->boolean("boleto_generated")->default(true);
            $table->boolean("boleto_paid")->default(true);
            $table->boolean("credit_card_refused")->default(true);
            $table->boolean("credit_card_paid")->default(true);
            $table->boolean("abandoned_cart")->default(true);
            $table->boolean("pix_generated")->default(true);
            $table->boolean("pix_paid")->default(true);
            $table->boolean("pix_expired")->default(true);
            $table->unsignedInteger("project_id")->index("notificacoes_inteligentes_integrations_project_id_foreign");
            $table->unsignedInteger("user_id")->index("notificacoes_inteligentes_integrations_user_id_foreign");
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create("notifications", function (Blueprint $table) {
            $table->char("id", 36)->primary();
            $table->string("type");
            $table->string("notifiable_type");
            $table->unsignedBigInteger("notifiable_id");
            $table->text("data");
            $table->timestamp("read_at")->nullable();
            $table
                ->timestamp("created_at")
                ->nullable()
                ->index("notifications_created_at_IDX");
            $table->timestamp("updated_at")->nullable();

            $table->index(["notifiable_type", "notifiable_id"]);
        });

        Schema::create("order_bump_rules", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->unsignedInteger("project_id")->index("order_bump_rules_project_id_foreign");
            $table->text("description")->nullable();
            $table->double("discount");
            $table
                ->tinyInteger("type")
                ->nullable()
                ->default(0);
            $table->json("apply_on_shipping")->nullable();
            $table->boolean("use_variants")->default(true);
            $table->json("apply_on_plans")->nullable();
            $table->json("offer_plans");
            $table->boolean("active_flag")->default(false);
            $table->timestamps();
        });

        Schema::create("password_resets", function (Blueprint $table) {
            $table->string("email", 100)->index();
            $table->string("token");
            $table->timestamp("created_at")->nullable();
        });

        Schema::create("pending_debt_withdrawals", function (Blueprint $table) {
            $table->unsignedBigInteger("pending_debt_id");
            $table->unsignedBigInteger("withdrawal_id")->index("pending_debt_withdrawals_withdrawal_id_foreign");

            $table->primary(["pending_debt_id", "withdrawal_id"]);
        });

        Schema::create("pending_debts", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->unsignedInteger("company_id")->index("pending_debts_company_id_foreign");
            $table
                ->unsignedBigInteger("sale_id")
                ->nullable()
                ->index("pending_debts_sale_id_foreign");
            $table->enum("type", ["REVERSED", "ADJUSTMENT"]);
            $table->dateTime("request_date")->nullable();
            $table->date("confirm_date")->nullable();
            $table->date("payment_date")->nullable();
            $table->string("reason")->nullable();
            $table->unsignedInteger("value");
            $table->timestamps();
        });

        Schema::create("permissions", function (Blueprint $table) {
            $table->increments("id");
            $table->string("name");
            $table->string("title", 100)->nullable();
            $table->string("guard_name");
            $table->timestamps();
        });

        Schema::create("pix_charges", function (Blueprint $table) {
            $table->increments("id");
            $table->unsignedBigInteger("sale_id")->index("fk_sale_id");
            $table->unsignedBigInteger("gateway_id")->index("fk_gateway_id");
            $table->string("txid", 35);
            $table->string("e2eId")->nullable();
            $table->integer("location_id")->default(0);
            $table->string("location", 100)->nullable();
            $table->string("qrcode", 200)->nullable();
            $table->mediumText("qrcode_image")->nullable();
            $table->integer("total_pix_value")->nullable();
            $table->string("status", 10);
            $table->timestamp("expiration_date")->nullable();
            $table->integer("automatic_discount")->nullable();
            $table->timestamps();
        });

        Schema::create("pix_transfer_requests", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table
                ->unsignedInteger("company_id")
                ->nullable()
                ->index("pix_transfer_requests_company_id_foreign");
            $table
                ->unsignedBigInteger("withdrawal_id")
                ->nullable()
                ->index("pix_transfer_requests_withdrawal_id_foreign");
            $table->string("pix_key");
            $table->integer("value");
            $table->text("request")->nullable();
            $table->text("response")->nullable();
            $table->timestamps();
        });

        Schema::create("pix_transfers", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table
                ->unsignedBigInteger("withdrawal_id")
                ->nullable()
                ->index("pix_transfers_withdrawal_id_foreign");
            $table->string("pix_transaction_id");
            $table->integer("value");
            $table->dateTime("requested_in");
            $table->dateTime("latest_status_updated");
            $table->string("transaction_ids")->nullable();
            $table->enum("status", ["PROCESSING", "REALIZED", "UNREALIZED", "RETURNED"]);
            $table->json("postback")->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create("pixel_configs", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->text("metatags_facebook")->nullable();
            $table->unsignedInteger("project_id")->index();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create("pixels", function (Blueprint $table) {
            $table->increments("id");
            $table
                ->unsignedInteger("project_id")
                ->nullable()
                ->index("pixels_projeto_foreign");
            $table->string("name", 30);
            $table->string("code", 100);
            $table->string("platform", 40);
            $table
                ->boolean("status")
                ->nullable()
                ->default(true)
                ->index("pixels_status_IDX");
            $table
                ->boolean("checkout")
                ->nullable()
                ->default(true);
            $table->boolean("send_value_checkout")->default(false);
            $table->boolean("purchase_all")->default(true);
            $table->boolean("basic_data")->default(true);
            $table->boolean("delivery")->default(true);
            $table->boolean("coupon")->default(true);
            $table->boolean("payment_info")->default(true);
            $table->boolean("upsell")->default(true);
            $table->boolean("purchase_upsell")->default(true);
            $table
                ->boolean("purchase_boleto")
                ->nullable()
                ->default(true);
            $table
                ->boolean("purchase_card")
                ->nullable()
                ->default(true);
            $table
                ->boolean("purchase_pix")
                ->nullable()
                ->default(true);
            $table
                ->unsignedBigInteger("affiliate_id")
                ->nullable()
                ->index("pixels_affiliate_id_foreign");
            $table
                ->unsignedBigInteger("campaign_id")
                ->nullable()
                ->index("pixels_campanha_foreign");
            $table->json("apply_on_plans")->nullable();
            $table->string("purchase_event_name")->nullable();
            $table->boolean("is_api")->default(false);
            $table->text("facebook_token")->nullable();
            $table->string("url_facebook_domain")->nullable();
            $table->integer("value_percentage_purchase_boleto")->default(100);
            $table->integer("value_percentage_purchase_pix")->default(100);
            $table
                ->timestamp("created_at")
                ->nullable()
                ->index("pixels_created_at_IDX");
            $table->timestamp("updated_at")->nullable();
            $table->softDeletes()->index("pixels_deleted_at_IDX");
        });

        Schema::create("plan_sale_products", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->unsignedBigInteger("product_id")->index();
            $table->string("cost");
            $table->string("price");
            $table
                ->timestamp("created_at")
                ->nullable()
                ->index("plan_sale_products_created_at_IDX");
            $table->timestamp("updated_at")->nullable();
        });

        Schema::create("plans", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table
                ->unsignedInteger("project_id")
                ->nullable()
                ->index("planos_projeto_foreign");
            $table->string("name")->default("sem nome");
            $table->string("description", 300)->nullable();
            $table->binary("code")->nullable();
            $table->decimal("price", 30);
            $table
                ->boolean("status")
                ->default(true)
                ->index("plans_status_IDX");
            $table
                ->string("shopify_id")
                ->nullable()
                ->index();
            $table
                ->string("shopify_variant_id")
                ->nullable()
                ->index();
            $table->boolean("active_flag")->default(true);
            $table->integer("processing_cost")->default(0);
            $table
                ->timestamp("created_at")
                ->nullable()
                ->index("plans_created_at_IDX");
            $table->timestamp("updated_at")->nullable();
            $table->softDeletes()->index("plans_deleted_at_IDX");
        });

        Schema::create("plans_sales", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->unsignedBigInteger("plan_id")->index("planos_vendas_plano_foreign");
            $table->unsignedBigInteger("sale_id")->index("planos_vendas_venda_foreign");
            $table->string("plan_value")->nullable();
            $table->string("amount")->nullable();
            $table
                ->timestamp("created_at")
                ->nullable()
                ->index("plans_sales_created_at_IDX");
            $table->timestamp("updated_at")->nullable();
            $table->softDeletes()->index("plans_sales_deleted_at_IDX");
        });

        Schema::create("postback_logs", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->integer("origin")->index("postback_logs_origin_IDX");
            $table
                ->string("description")
                ->nullable()
                ->index("postback_logs_description_IDX");
            $table->json("data");
            $table
                ->timestamp("created_at")
                ->nullable()
                ->index("postback_logs_created_at_IDX");
            $table->timestamp("updated_at")->nullable();
        });

        Schema::create("products", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table
                ->unsignedInteger("user_id")
                ->nullable()
                ->index("produtos_user_foreign");
            $table
                ->string("name")
                ->default("sem nome")
                ->index("products_name_IDX");
            $table->string("description", 250)->nullable();
            $table->string("guarantee", 100)->nullable();
            $table->boolean("format")->nullable();
            $table
                ->unsignedBigInteger("category_id")
                ->nullable()
                ->index("produtos_categoria_foreign");
            $table
                ->string("cost", 191)
                ->nullable()
                ->default("0.00");
            $table->string("photo", 1000)->nullable();
            $table->string("height")->nullable();
            $table->string("width")->nullable();
            $table->string("length")->nullable();
            $table->string("weight")->nullable();
            $table->boolean("shopify")->default(false);
            $table
                ->timestamp("created_at")
                ->nullable()
                ->index("products_created_at_IDX");
            $table->timestamp("updated_at")->nullable();
            $table->softDeletes()->index("products_deleted_at_IDX");
            $table->string("digital_product_url")->nullable();
            $table->integer("url_expiration_time")->nullable();
            $table->string("price", 191)->nullable();
            $table
                ->string("shopify_id")
                ->nullable()
                ->index("products_shopify_id");
            $table
                ->string("shopify_variant_id")
                ->nullable()
                ->index();
            $table->string("sku")->nullable();
            $table
                ->unsignedInteger("project_id")
                ->nullable()
                ->index();
            $table->unsignedInteger("currency_type_enum")->default(1);
            $table->unsignedInteger("type_enum")->default(1);
            $table->tinyInteger("status_enum")->nullable();
            $table->boolean("active_flag")->default(true);
        });

        Schema::create("products_plans", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->unsignedBigInteger("product_id")->index("produtos_planos_produto_foreign");
            $table->unsignedBigInteger("plan_id")->index("produtos_planos_plano_foreign");
            $table->integer("amount");
            $table
                ->tinyInteger("is_custom")
                ->nullable()
                ->default(0);
            $table->json("custom_config")->nullable();
            $table
                ->timestamp("created_at")
                ->nullable()
                ->index("products_plans_created_at_IDX");
            $table->timestamp("updated_at")->nullable();
            $table->softDeletes()->index("products_plans_deleted_at_IDX");
            $table->unsignedInteger("cost")->nullable();
            $table->unsignedInteger("currency_type_enum")->default(1);
        });

        Schema::create("products_plans_sales", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table
                ->unsignedBigInteger("product_id")
                ->nullable()
                ->index();
            $table
                ->unsignedBigInteger("plan_id")
                ->nullable()
                ->index();
            $table
                ->unsignedBigInteger("products_sales_api_id")
                ->nullable()
                ->index("products_plans_sales_products_sales_api_id_foreign");
            $table->unsignedBigInteger("sale_id")->index();
            $table->integer("amount")->nullable();
            $table->string("name")->index("products_plans_sales_name_IDX");
            $table->string("description")->nullable();
            $table->string("guarantee")->nullable();
            $table->string("format")->nullable();
            $table->string("cost")->nullable();
            $table->string("photo")->nullable();
            $table->string("height")->nullable();
            $table->string("width")->nullable();
            $table->string("weight")->nullable();
            $table->string("shopify")->nullable();
            $table->string("digital_product_url")->nullable();
            $table->text("temporary_url")->nullable();
            $table->string("price")->nullable();
            $table->string("shopify_id")->nullable();
            $table->string("shopify_variant_id")->nullable();
            $table
                ->timestamp("created_at")
                ->nullable()
                ->index("products_plans_sales_created_at_IDX");
            $table->timestamp("updated_at")->nullable();
            $table->softDeletes()->index("products_plans_sales_deleted_at_IDX");
        });

        Schema::create("products_sales_api", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->unsignedBigInteger("sale_id")->index("products_sales_sale_id_foreign");
            $table->string("item_id");
            $table->string("name");
            $table->string("price", 191);
            $table->integer("quantity");
            $table->string("product_type");
            $table->timestamps();
        });

        Schema::create("project_notifications", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->unsignedInteger("project_id")->index();
            $table
                ->boolean("status")
                ->default(true)
                ->index("project_notifications_status_IDX");
            $table->tinyInteger("type_enum")->index("project_notifications_type_enum_IDX");
            $table->tinyInteger("event_enum")->index("project_notifications_event_enum_IDX");
            $table
                ->tinyInteger("notification_enum")
                ->nullable()
                ->index("project_notifications_notification_enum_IDX");
            $table->string("time");
            $table->text("message");
            $table
                ->timestamp("created_at")
                ->nullable()
                ->index("project_notifications_created_at_IDX");
            $table->timestamp("updated_at")->nullable();
            $table->softDeletes()->index("project_notifications_deleted_at_IDX");
        });

        Schema::create("project_reviews", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->unsignedInteger("project_id")->index();
            $table->json("apply_on_plans")->nullable();
            $table->string("photo")->nullable();
            $table->string("name")->nullable();
            $table->double("stars", 8, 2)->default(5);
            $table->string("description")->nullable();
            $table->boolean("active_flag")->default(false);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create("project_upsell_configs", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->unsignedInteger("project_id")->index();
            $table->string("header");
            $table
                ->integer("countdown_time")
                ->nullable()
                ->index("project_upsell_configs_countdown_time_IDX");
            $table
                ->boolean("countdown_flag")
                ->default(false)
                ->index("project_upsell_configs_countdown_flag_IDX");
            $table
                ->timestamp("created_at")
                ->nullable()
                ->index("project_upsell_configs_created_at_IDX");
            $table->timestamp("updated_at")->nullable();
            $table->softDeletes()->index("project_upsell_configs_deleted_at_IDX");
        });

        Schema::create("project_upsell_rules", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->unsignedInteger("project_id")->index();
            $table->text("description")->nullable();
            $table->json("apply_on_plans")->nullable();
            $table->json("offer_on_plans")->nullable();
            $table->double("discount")->nullable();
            $table
                ->tinyInteger("type")
                ->nullable()
                ->default(0);
            $table->json("apply_on_shipping")->nullable();
            $table->boolean("use_variants")->default(true);
            $table
                ->boolean("active_flag")
                ->default(false)
                ->index("project_upsell_rules_active_flag_IDX");
            $table
                ->timestamp("created_at")
                ->nullable()
                ->index("project_upsell_rules_created_at_IDX");
            $table->timestamp("updated_at")->nullable();
            $table->softDeletes()->index("project_upsell_rules_deleted_at_IDX");
        });

        Schema::create("projects", function (Blueprint $table) {
            $table->increments("id");
            $table->string("photo")->nullable();
            $table->string("visibility")->nullable();
            $table
                ->boolean("status")
                ->nullable()
                ->index("projects_status_IDX");
            $table
                ->string("name")
                ->nullable()
                ->index("projects_name_IDX");
            $table->string("description")->nullable();
            $table->string("percentage_affiliates")->nullable();
            $table->text("terms_affiliates")->nullable();
            $table->tinyInteger("status_url_affiliates")->default(0);
            $table->tinyInteger("commission_type_enum")->default(2);
            $table
                ->string("url_page")
                ->nullable()
                ->index("projects_url_page_IDX");
            $table->boolean("automatic_affiliation")->default(false);
            $table->string("shopify_id")->nullable();
            $table->string("woocommerce_id")->nullable();
            $table
                ->unsignedInteger("carrier_id")
                ->nullable()
                ->index("projetos_transportadora_foreign");
            $table->string("cookie_duration")->nullable();
            $table->boolean("url_cookies_checkout")->nullable();
            $table->string("boleto_redirect")->nullable();
            $table->string("pix_redirect")->nullable();
            $table->string("card_redirect")->nullable();
            $table->string("analyzing_redirect")->nullable();
            $table->unsignedInteger("cost_currency_type")->default(1);
            $table
                ->boolean("discount_recovery_status")
                ->default(false)
                ->comment("True (EstÃ¡ ativa a recobranÃ§a) - False (NÃ£o estÃ¡ ativa a recobranÃ§a)");
            $table->integer("discount_recovery_value")->default(0);
            $table->string("reviews_config_icon_type", 20)->default("star");
            $table->string("reviews_config_icon_color", 7)->default("#f8ce1c");
            $table->json("notazz_configs")->nullable();
            $table
                ->timestamp("created_at")
                ->nullable()
                ->index("projects_created_at_IDX");
            $table->softDeletes()->index("projects_deleted_at_IDX");
            $table->timestamp("updated_at")->nullable();
        });

        Schema::create("promotional_taxes", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->unsignedInteger("user_id")->index("promotional_taxes_user_id_foreign");
            $table->date("expiration")->nullable();
            $table->string("tax");
            $table->string("old_tax")->nullable();
            $table->boolean("active")->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create("push_notifications", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table
                ->unsignedBigInteger("sale_id")
                ->nullable()
                ->index();
            $table->unsignedInteger("user_id")->index();
            $table
                ->unsignedBigInteger("withdrawal_id")
                ->nullable()
                ->index();
            $table->json("postback_data");
            $table->text("onesignal_response")->nullable();
            $table
                ->unsignedTinyInteger("processed_flag")
                ->default(0)
                ->index();
            $table
                ->unsignedTinyInteger("postback_valid_flag")
                ->default(0)
                ->index();
            $table->json("machine_result")->nullable();
            $table
                ->timestamp("created_at")
                ->nullable()
                ->index("push_notifications_created_at_IDX");
            $table->timestamp("updated_at")->nullable();
            $table->softDeletes()->index("push_notifications_deleted_at_IDX");
        });

        Schema::create("regenerated_billets", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->unsignedBigInteger("sale_id")->index("regenerated_billets_sale_id_foreign");
            $table->string("billet_link");
            $table->string("billet_digitable_line");
            $table->string("billet_due_date");
            $table->string("gateway_transaction_id");
            $table
                ->string("gateway_billet_identificator")
                ->nullable()
                ->index();
            $table->unsignedBigInteger("gateway_id")->index("regenerated_billets_gateway_id_foreign");
            $table->bigInteger("owner_id");
            $table->timestamps();
        });

        Schema::create("registration_token", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->enum("type", ["sms", "email"])->default("email");
            $table->string("type_data");
            $table->string("token");
            $table->string("document")->nullable();
            $table->timestamp("expiration");
            $table->boolean("validated")->default(false);
            $table->unsignedInteger("number_wrong_attempts")->default(0);
            $table->string("ip", 45);
            $table->timestamps();
        });

        Schema::create("reportana_integrations", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->unsignedInteger("user_id")->index();
            $table->unsignedInteger("project_id")->index();
            $table->string("url_api");
            $table->boolean("abandoned_cart")->default(true);
            $table->boolean("billet_paid")->default(true);
            $table->boolean("billet_expired")->default(true);
            $table->boolean("billet_generated")->default(true);
            $table->boolean("credit_card_paid")->default(true);
            $table->boolean("pix_generated")->default(true);
            $table->boolean("pix_paid")->default(true);
            $table->boolean("pix_expired")->default(true);
            $table->boolean("credit_card_refused")->default(true);
            $table->softDeletes()->index("reportana_integrations_deleted_at_IDX");
            $table
                ->timestamp("created_at")
                ->nullable()
                ->index("reportana_integrations_created_at_IDX");
            $table->timestamp("updated_at")->nullable();
        });

        Schema::create("reportana_sent", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->text("data");
            $table->text("response");
            $table
                ->integer("sent_status")
                ->nullable()
                ->index("reportana_sent_sent_status_IDX");
            $table
                ->unsignedInteger("event_sale")
                ->nullable()
                ->index("reportana_sent_event_sale_IDX");
            $table
                ->unsignedBigInteger("instance_id")
                ->nullable()
                ->index("reportana_sent_instance_id_IDX");
            $table->string("instance")->nullable();
            $table
                ->unsignedBigInteger("reportana_integration_id")
                ->nullable()
                ->index("reportana_sent_reportana_integration_id_foreign");
            $table
                ->timestamp("created_at")
                ->nullable()
                ->index("reportana_sent_created_at_IDX");
            $table->timestamp("updated_at")->nullable();
        });

        Schema::create("role_has_permissions", function (Blueprint $table) {
            $table->unsignedInteger("permission_id");
            $table->unsignedInteger("role_id")->index("role_has_permissions_role_id_foreign");

            $table->primary(["permission_id", "role_id"]);
        });

        Schema::create("roles", function (Blueprint $table) {
            $table->increments("id");
            $table->string("name");
            $table->string("guard_name");
            $table->timestamps();
        });

        Schema::create("sale_additional_customer_informations", function (Blueprint $table) {
            $table->increments("id");
            $table->unsignedBigInteger("sale_id")->index("sale_additional_customer_informations_sale_id_foreign");
            $table->unsignedBigInteger("plan_id")->index("sale_additional_customer_informations_plan_id_foreign");
            $table->unsignedBigInteger("product_id")->index("sale_additional_customer_informations_product_id_foreign");
            $table
                ->enum("type_enum", ["File", "Image", "Text"])
                ->nullable()
                ->default("Text");
            $table->string("value", 250)->nullable();
            $table->string("file_name", 100)->nullable();
            $table
                ->tinyInteger("line")
                ->nullable()
                ->default(1);
            $table->string("label", 500)->nullable();
            $table
                ->tinyInteger("order")
                ->nullable()
                ->default(0);
            $table->timestamps();
        });

        Schema::create("sale_antifraud_results", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->unsignedBigInteger("sale_id")->index("sale_antifraud_results_sale_id_foreign");
            $table->unsignedBigInteger("antifraud_id")->index("sale_antifraud_results_antifraud_id_foreign");
            $table->json("send_data")->nullable();
            $table->json("antifraud_result")->nullable();
            $table
                ->string("status")
                ->nullable()
                ->index("sale_antifraud_results_status_IDX");
            $table->json("translated_codes")->nullable();
            $table->json("antifraud_exceptions")->nullable();
            $table
                ->timestamp("created_at")
                ->nullable()
                ->index("sale_antifraud_results_created_at_IDX");
            $table->timestamp("updated_at")->nullable();

            $table->index(["antifraud_id"], "sale_antifraud_results_antifraud_id_IDX");
        });

        Schema::create("sale_biometry_results", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->unsignedBigInteger("sale_id")->index("sale_biometry_results_sale_id_foreign");
            $table->string("vendor")->index();
            $table
                ->string("biometry_id")
                ->nullable()
                ->index();
            $table->string("score")->nullable();
            $table
                ->string("status")
                ->nullable()
                ->index();
            $table->json("request_data")->nullable();
            $table->json("response_data")->nullable();
            $table->json("postback_data")->nullable();
            $table->json("api_data")->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create("sale_contestation_files", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table
                ->unsignedBigInteger("contestation_sale_id")
                ->index("sale_contestation_files_contestation_sale_id_foreign");
            $table->unsignedInteger("user_id")->index("sale_contestation_files_user_id_foreign");
            $table->enum("type", [
                "NOTA_FISCAL",
                "POLITICA_VENDA",
                "ENTREGA",
                "INFO_ACORDO",
                "TERMOS_USO",
                "POLITICA_CANCEL",
                "COMPROVANTE_CANCEL",
                "OUTROS",
            ]);
            $table->string("file")->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create("sale_contestations", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table
                ->unsignedBigInteger("sale_id")
                ->nullable()
                ->index("sale_contestations_sale_id_foreign");
            $table
                ->unsignedBigInteger("gateway_id")
                ->default(15)
                ->index("sale_contestations_gateway_id_foreign");
            $table->integer("status")->default(1);
            $table->string("nsu")->nullable();
            $table->string("gateway_case_number", 120)->nullable();
            $table->date("file_date")->nullable();
            $table->date("transaction_date")->nullable();
            $table->date("request_date")->nullable();
            $table->date("expiration_date")->nullable();
            $table->string("reason")->nullable();
            $table->boolean("is_contested")->default(false);
            $table->text("observation")->nullable();
            $table->json("data")->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->boolean("file_user_completed")->default(false);
        });

        Schema::create("sale_gateway_requests", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->unsignedBigInteger("sale_id")->index("sale_gateway_requests_sale_id_foreign");
            $table
                ->unsignedBigInteger("gateway_id")
                ->nullable()
                ->index("sale_gateway_requests_gateway_id_foreign");
            $table->json("send_data")->nullable();
            $table->json("gateway_result")->nullable();
            $table->json("gateway_exceptions")->nullable();
            $table
                ->timestamp("created_at")
                ->nullable()
                ->index("sale_gateway_requests_created_at_IDX");
            $table->timestamp("updated_at")->nullable();
        });

        Schema::create("sale_idwall_questions", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->unsignedBigInteger("sale_id")->index();
            $table->json("question");
            $table->unsignedTinyInteger("correct_answer")->index();
            $table
                ->unsignedTinyInteger("client_answer")
                ->nullable()
                ->index();
            $table
                ->boolean("correct_flag")
                ->default(false)
                ->index();
            $table
                ->timestamp("expire_at")
                ->nullable()
                ->index();
            $table
                ->timestamp("created_at")
                ->nullable()
                ->index("sale_idwall_questions_created_at_IDX");
            $table->timestamp("updated_at")->nullable();
        });

        Schema::create("sale_informations", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table
                ->unsignedBigInteger("sale_id")
                ->nullable()
                ->index("sale_informations_sale_id_foreign");
            $table->json("request")->nullable();
            $table
                ->string("operational_system")
                ->nullable()
                ->index()
                ->comment("Sistema operacional do usuÃ¡rio");
            $table
                ->string("browser")
                ->nullable()
                ->index()
                ->comment("Browser usado para fazer a compra");
            $table
                ->string("browser_fingerprint")
                ->nullable()
                ->index()
                ->comment("Fingerprint do browser utilizado na compra");
            $table
                ->string("browser_token")
                ->nullable()
                ->comment("Token do browser (salvo no cookie) utilizado na compra");
            $table->string("browser_token_post")->nullable();
            $table
                ->string("ip")
                ->nullable()
                ->index()
                ->comment("Ip com comprador");
            $table
                ->string("url")
                ->nullable()
                ->index();
            $table
                ->string("attempt_reference")
                ->nullable()
                ->index();
            $table
                ->string("customer_name")
                ->nullable()
                ->index("sale_informations_customer_name_IDX")
                ->comment("Nome do comprador");
            $table
                ->string("customer_email")
                ->nullable()
                ->index("sale_informations_customer_email_IDX")
                ->comment("Email do comprador");
            $table
                ->string("customer_phone")
                ->nullable()
                ->index()
                ->comment("Telefone do comprador");
            $table
                ->string("customer_identification_number")
                ->nullable()
                ->index()
                ->comment("CPF/CNPJ do comprador");
            $table
                ->string("project_name")
                ->nullable()
                ->index()
                ->comment("Nome do projeto");
            $table
                ->string("transaction_amount")
                ->nullable()
                ->index()
                ->comment("Valor total da transaÃ§Ã£o");
            $table
                ->string("country")
                ->nullable()
                ->index()
                ->comment("PaÃ­s informado pelo comprador");
            $table
                ->string("zip_code")
                ->nullable()
                ->index("sale_informations_zip_code_IDX")
                ->comment("CEP informado pelo comprador");
            $table
                ->string("state")
                ->nullable()
                ->index()
                ->comment("Estado informado pelo comprador");
            $table
                ->string("city")
                ->nullable()
                ->index()
                ->comment("Cidade informada pelo comprador");
            $table
                ->string("district")
                ->nullable()
                ->comment("Bairro informado pelo comprador");
            $table
                ->string("street_name")
                ->nullable()
                ->comment("Rua informada pelo comprador");
            $table
                ->string("street_number")
                ->nullable()
                ->index()
                ->comment("NÃºmero da casa informada pelo comprador");
            $table
                ->string("card_holder")
                ->nullable()
                ->index();
            $table
                ->string("card_token")
                ->nullable()
                ->index()
                ->comment("Token do cartÃ£o");
            $table
                ->string("card_token_sha3_256", 64)
                ->nullable()
                ->index();
            $table
                ->string("card_brand")
                ->nullable()
                ->index()
                ->comment("Bandeira do cartÃ£o");
            $table
                ->unsignedInteger("installments")
                ->nullable()
                ->index()
                ->comment("NÃºmero de parcelas na compra");
            $table
                ->unsignedInteger("first_six_digits")
                ->nullable()
                ->index()
                ->comment("Primeiros 6 dÃ­gitos do cartÃ£o");
            $table
                ->unsignedInteger("last_four_digits")
                ->nullable()
                ->index()
                ->comment("Ãšltimos 4 dÃ­gitos do cartÃ£o");
            $table
                ->timestamp("created_at")
                ->nullable()
                ->index("sale_informations_created_at_IDX");
            $table->timestamp("updated_at")->nullable();
        });

        Schema::create("sale_logs", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->unsignedBigInteger("sale_id")->index("sale_logs_sale_id_IDX");
            $table->unsignedInteger("status_enum")->index("sale_logs_status_enum_IDX");
            $table->string("status", 50)->index("sale_logs_status_IDX");
            $table
                ->timestamp("created_at")
                ->nullable()
                ->index("sale_logs_created_at_IDX");
            $table->timestamp("updated_at")->nullable();
            $table->softDeletes()->index("sale_logs_deleted_at_IDX");
        });

        Schema::create("sale_refund_histories", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->unsignedBigInteger("sale_id")->index("sale_refund_histories_sale_id_foreign");
            $table
                ->unsignedInteger("user_id")
                ->nullable()
                ->index("sale_refund_histories_user_id_foreign");
            $table->integer("refunded_amount");
            $table
                ->timestamp("date_refunded")
                ->useCurrentOnUpdate()
                ->useCurrent()
                ->index("sale_refund_histories_date_refunded_IDX");
            $table->json("gateway_response");
            $table->unsignedInteger("refund_value")->default(0);
            $table->text("refund_observation")->nullable();
            $table->softDeletes()->index("sale_refund_histories_deleted_at_IDX");
            $table
                ->timestamp("created_at")
                ->nullable()
                ->index("sale_refund_histories_created_at_IDX");
            $table->timestamp("updated_at")->nullable();
        });

        Schema::create("sale_shopify_requests", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->string("project");
            $table->string("method");
            $table->unsignedBigInteger("sale_id")->index("sale_shopify_requests_sale_id_foreign");
            $table->json("send_data")->nullable();
            $table->json("received_data")->nullable();
            $table->json("exceptions")->nullable();
            $table
                ->timestamp("created_at")
                ->nullable()
                ->index("sale_shopify_requests_created_at_IDX");
            $table->timestamp("updated_at")->nullable();
        });

        Schema::create("sale_under_attacks", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->unsignedBigInteger("under_attack_id")->index("sale_under_attacks_under_attack_id_foreign");
            $table->unsignedBigInteger("sale_id")->index("sale_under_attacks_sale_id_foreign");
            $table->timestamps();
        });

        Schema::create("sale_white_black_list_results", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->unsignedBigInteger("sale_id")->index("sale_white_black_list_results_sale_id_foreign");
            $table
                ->boolean("whitelist")
                ->index("sale_white_black_list_results_whitelist_IDX")
                ->comment("True (EstÃ¡ na whitelist) - False (NÃ£o estÃ¡ na whitelist)");
            $table
                ->boolean("blacklist")
                ->index("sale_white_black_list_results_blacklist_IDX")
                ->comment("True (EstÃ¡ na blacklist) - False (NÃ£o estÃ¡ na blacklist)");
            $table->json("whiteblacklist_json")->comment("Regras que caÃ­ram no black/white list");
            $table
                ->timestamp("created_at")
                ->nullable()
                ->index("sale_white_black_list_results_created_at_IDX");
            $table->timestamp("updated_at")->nullable();
        });

        Schema::create("sale_woocommerce_requests", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->bigInteger("sale_id")->nullable();
            $table->bigInteger("project_id")->nullable();
            $table->bigInteger("order")->nullable();
            $table->string("method")->nullable();
            $table->tinyInteger("status")->default(0);
            $table->json("send_data")->nullable();
            $table->text("received_data")->nullable();
            $table->timestamps();
        });

        Schema::create("sales", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table
                ->unsignedInteger("owner_id")
                ->nullable()
                ->index("vendas_proprietario_foreign");
            $table
                ->unsignedBigInteger("gateway_id")
                ->nullable()
                ->index();
            $table->boolean("api_flag")->default(false);
            $table
                ->unsignedBigInteger("api_token_id")
                ->nullable()
                ->index("sales_api_token_id_foreign");
            $table
                ->unsignedBigInteger("checkout_id")
                ->nullable()
                ->index("sales_checkout_foreign");
            $table
                ->unsignedInteger("project_id")
                ->nullable()
                ->index("sales_project_foreign");
            $table
                ->unsignedBigInteger("affiliate_id")
                ->nullable()
                ->index("vendas_afiliado_foreign");
            $table->unsignedBigInteger("customer_id")->index("vendas_comprador_foreign");
            $table
                ->unsignedBigInteger("customer_card_id")
                ->nullable()
                ->index("sales_client_card_id_foreign");
            $table
                ->unsignedBigInteger("delivery_id")
                ->nullable()
                ->index("vendas_entrega_foreign");
            $table
                ->unsignedBigInteger("shipping_id")
                ->nullable()
                ->index("sales_shipping_foreign");
            $table
                ->unsignedBigInteger("upsell_id")
                ->nullable()
                ->index();
            $table->integer("attempts")->default(1);
            $table->string("payment_form", 150)->nullable();
            $table
                ->integer("payment_method")
                ->nullable()
                ->index("sales_payment_method_IDX");
            $table->decimal("total_paid_value")->nullable();
            $table->unsignedInteger("real_total_paid_value")->nullable();
            $table->unsignedInteger("recovery_discount_percent")->nullable();
            $table->integer("original_total_paid_value")->nullable();
            $table->decimal("sub_total")->nullable();
            $table->decimal("shipment_value", 6);
            $table->dateTime("start_date")->index("sales_start_date_IDX");
            $table
                ->dateTime("end_date")
                ->nullable()
                ->index("sales_end_date_IDX");
            $table
                ->dateTime("date_refunded")
                ->nullable()
                ->index("sales_date_refunded_IDX");
            $table
                ->string("gateway_transaction_id")
                ->nullable()
                ->index("sales_gateway_transaction_id_IDX");
            $table->string("gateway_order_id", 50)->nullable();
            $table->string("flag", 150)->nullable();
            $table->string("gateway_card_flag")->nullable();
            $table
                ->integer("status")
                ->nullable()
                ->index("sales_status_IDX");
            $table->string("gateway_status")->nullable();
            $table->decimal("gateway_tax_percent")->nullable();
            $table->integer("gateway_tax_value")->nullable();
            $table
                ->string("gateway_billet_identificator")
                ->nullable()
                ->index();
            $table->integer("installments_amount")->nullable();
            $table->unsignedInteger("real_installments_amount")->nullable();
            $table->string("installments_value", 150)->nullable();
            $table->unsignedInteger("real_installments_value")->nullable();
            $table->string("installment_tax_value")->nullable();
            $table->string("boleto_link")->nullable();
            $table->char("boleto_digitable_line")->nullable();
            $table
                ->timestamp("boleto_due_date")
                ->nullable()
                ->index();
            $table->string("cupom_code", 150)->nullable();
            $table
                ->string("shopify_order")
                ->nullable()
                ->index();
            $table
                ->string("woocommerce_order")
                ->nullable()
                ->index();
            $table->string("shopify_discount")->nullable();
            $table->string("dolar_quotation")->nullable();
            $table->boolean("first_confirmation")->default(true);
            $table->unsignedInteger("automatic_discount")->default(0);
            $table->integer("progressive_discount")->nullable();
            $table->unsignedInteger("interest_total_value")->nullable();
            $table->unsignedInteger("refund_value")->default(0);
            $table->boolean("is_chargeback")->default(false);
            $table->boolean("is_chargeback_recovered")->default(false);
            $table->boolean("has_valid_tracking")->default(false);
            $table->boolean("has_order_bump")->default(false);
            $table->text("observation")->nullable();
            $table
                ->string("antifraud_warning_level", 20)
                ->nullable()
                ->index();
            $table->text("antifraud_observation")->nullable();
            $table
                ->timestamp("created_at")
                ->nullable()
                ->index("sales_created_at_IDX");
            $table->timestamp("updated_at")->nullable();
            $table->softDeletes()->index("sales_deleted_at_IDX");
        });

        Schema::create("sent_emails", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->string("from_email", 320)->index("sent_emails_from_email_IDX");
            $table->string("from_name");
            $table->string("to_email", 320)->index("sent_emails_to_email_IDX");
            $table->string("to_name");
            $table->string("template_id")->nullable();
            $table->json("template_data")->nullable();
            $table->unsignedInteger("status_code")->index("sent_emails_status_code_IDX");
            $table->string("status")->index("sent_emails_status_IDX");
            $table->text("log_error")->nullable();
            $table
                ->timestamp("created_at")
                ->nullable()
                ->index("sent_emails_created_at_IDX");
            $table->timestamp("updated_at")->nullable();
        });

        Schema::create("shippings", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table
                ->unsignedInteger("project_id")
                ->nullable()
                ->index("shippings_project_foreign");
            $table
                ->string("name")
                ->nullable()
                ->index("shippings_name_IDX");
            $table->string("information")->nullable();
            $table->string("value")->nullable();
            $table->json("regions_values")->nullable();
            $table
                ->string("type")
                ->nullable()
                ->index("shippings_type_IDX");
            $table
                ->string("zip_code_origin")
                ->nullable()
                ->index("shippings_zip_code_origin_IDX");
            $table
                ->unsignedBigInteger("melhorenvio_integration_id")
                ->nullable()
                ->index("shippings_melhorenvio_integration_id_foreign");
            $table
                ->boolean("status")
                ->nullable()
                ->index("shippings_status_IDX");
            $table->integer("rule_value")->default(0);
            $table->boolean("pre_selected")->nullable();
            $table->boolean("use_variants")->default(true);
            $table->json("apply_on_plans")->nullable();
            $table->json("not_apply_on_plans")->nullable();
            $table
                ->timestamp("created_at")
                ->nullable()
                ->index("shippings_created_at_IDX");
            $table->timestamp("updated_at")->nullable();
            $table->softDeletes()->index("shippings_deleted_at_IDX");
            $table
                ->integer("type_enum")
                ->nullable()
                ->index("shippings_type_enum_IDX");
        });

        Schema::create("shopify_integrations", function (Blueprint $table) {
            $table->increments("id");
            $table->string("token")->nullable();
            $table->string("shared_secret")->nullable();
            $table->string("url_store")->index("shopify_integrations_url_store_IDX");
            $table->unsignedInteger("user_id")->index("integracoes_shopify_user_foreign");
            $table->unsignedInteger("project_id")->index("integracoes_shopify_projeto_foreign");
            $table
                ->timestamp("created_at")
                ->nullable()
                ->index("shopify_integrations_created_at_IDX");
            $table->timestamp("updated_at")->nullable();
            $table->softDeletes()->index("shopify_integrations_deleted_at_IDX");
            $table->integer("theme_type")->nullable();
            $table->string("theme_name")->nullable();
            $table->text("theme_file")->nullable();
            $table->text("theme_html")->nullable();
            $table->text("layout_theme_html")->nullable();
            $table
                ->tinyInteger("status")
                ->default(3)
                ->index("shopify_integrations_status_IDX");
            $table
                ->boolean("skip_to_cart")
                ->default(false)
                ->index("shopify_integrations_skip_to_cart_IDX");
        });

        Schema::create("site_invitations_requests", function (Blueprint $table) {
            $table->increments("id");
            $table->string("name")->index("site_invitations_requests_name_IDX");
            $table->string("surname");
            $table->string("email")->index("site_invitations_requests_email_IDX");
            $table->string("billing");
            $table->string("celphone");
            $table
                ->timestamp("created_at")
                ->nullable()
                ->index("site_invitations_requests_created_at_IDX");
            $table->timestamp("updated_at")->nullable();
            $table->softDeletes()->index("site_invitations_requests_deleted_at_IDX");
        });

        Schema::create("smartfunnel_integrations", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->unsignedInteger("user_id")->index();
            $table->unsignedInteger("project_id")->index();
            $table->string("api_url");
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create("smartfunnel_sent", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->text("data");
            $table->text("response");
            $table->integer("sent_status")->nullable();
            $table->unsignedInteger("event_sale")->nullable();
            $table
                ->unsignedBigInteger("sale_id")
                ->nullable()
                ->index("smartfunnel_sent_sale_id_foreign");
            $table
                ->unsignedBigInteger("smartfunnel_integration_id")
                ->nullable()
                ->index("smartfunnel_sent_smartfunnel_integration_id_foreign");
            $table->timestamps();
        });

        Schema::create("sms_messages", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->string("zenvia_id");
            $table->string("to")->index("sms_messages_to_IDX");
            $table->string("message");
            $table->string("date");
            $table->string("received_from")->nullable();
            $table
                ->string("status")
                ->nullable()
                ->index("sms_messages_status_IDX");
            $table
                ->unsignedBigInteger("plan")
                ->nullable()
                ->index("mensagens_sms_plano_foreign");
            $table->string("event")->nullable();
            $table->string("type")->nullable();
            $table
                ->unsignedInteger("user")
                ->nullable()
                ->index("mensagens_sms_user_foreign");
            $table
                ->timestamp("created_at")
                ->nullable()
                ->index("sms_messages_created_at_IDX");
            $table->timestamp("updated_at")->nullable();
            $table->softDeletes()->index("sms_messages_deleted_at_IDX");
        });

        Schema::create("suspect_bots", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->unsignedBigInteger("checkout_id")->index();
            $table
                ->timestamp("created_at")
                ->nullable()
                ->index("suspect_bots_created_at_IDX");
            $table->timestamp("updated_at")->nullable();
        });

        Schema::create("tags", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->string("description");
            $table->timestamps();
        });

        Schema::create("tags_tickets", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->unsignedBigInteger("tag_id")->index("tags_tickets_tag_id_foreign");
            $table->unsignedBigInteger("ticket_id")->index("tags_tickets_ticket_id_foreign");
            $table->timestamps();
        });

        Schema::create("tasks", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->string("name", 100);
            $table->integer("level");
            $table->integer("priority");
            $table->timestamps();
        });

        Schema::create("tasks_users", function (Blueprint $table) {
            $table->unsignedBigInteger("task_id");
            $table->unsignedInteger("user_id")->index("tasks_users_user_id_foreign");
            $table->timestamps();

            $table->primary(["task_id", "user_id"]);
        });

        Schema::create("ticket_attachments", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->unsignedBigInteger("ticket_id")->index("ticket_attachments_ticket_id_foreign");
            $table->string("file");
            $table->string("filename")->nullable();
            $table->integer("type_enum")->default(1);
            $table
                ->timestamp("created_at")
                ->nullable()
                ->index("ticket_attachments_created_at_IDX");
            $table->timestamp("updated_at")->nullable();
            $table->softDeletes()->index("ticket_attachments_deleted_at_IDX");
        });

        Schema::create("ticket_messages", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->unsignedBigInteger("ticket_id")->index("ticket_messages_ticket_id_foreign");
            $table->text("message");
            $table->integer("type_enum")->default(1);
            $table
                ->timestamp("created_at")
                ->nullable()
                ->index("ticket_messages_created_at_IDX");
            $table->timestamp("updated_at")->nullable();
            $table->softDeletes()->index("ticket_messages_deleted_at_IDX");
        });

        Schema::create("tickets", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->unsignedBigInteger("sale_id")->index("tickets_sale_id_foreign");
            $table->unsignedBigInteger("customer_id")->index("tickets_customer_id_foreign");
            $table
                ->unsignedInteger("manager_user_id")
                ->nullable()
                ->index();
            $table->dateTime("manager_user_assignment_date")->nullable();
            $table->string("subject");
            $table->integer("subject_enum")->nullable();
            $table->text("description");
            $table->integer("ticket_category_enum")->index("tickets_ticket_category_enum_IDX");
            $table->integer("ticket_status_enum")->index("tickets_ticket_status_enum_IDX");
            $table->integer("last_message_type_enum")->default(1);
            $table->timestamp("last_message_date")->useCurrent();
            $table->boolean("mediation_notified")->default(false);
            $table->boolean("ignore_balance_block")->default(false);
            $table->integer("average_response_time")->nullable();
            $table
                ->timestamp("created_at")
                ->nullable()
                ->index("tickets_created_at_IDX");
            $table->timestamp("updated_at")->nullable();
            $table->softDeletes()->index("tickets_deleted_at_IDX");
        });

        Schema::create("tracking_histories", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->unsignedBigInteger("tracking_id")->index("tracking_histories_tracking_id_foreign");
            $table->string("tracking_code")->nullable();
            $table
                ->timestamp("created_at")
                ->nullable()
                ->index("tracking_histories_created_at_IDX");
            $table->timestamp("updated_at")->nullable();
            $table
                ->tinyInteger("tracking_status_enum")
                ->nullable()
                ->index("tracking_histories_tracking_status_enum_IDX");
            $table->softDeletes()->index("tracking_histories_deleted_at_IDX");
        });

        Schema::create("trackings", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->unsignedBigInteger("sale_id")->index("trackings_sale_id_foreign");
            $table
                ->unsignedBigInteger("product_id")
                ->nullable()
                ->index("trackings_product_id_foreign");
            $table->integer("amount")->nullable();
            $table
                ->unsignedBigInteger("product_plan_sale_id")
                ->nullable()
                ->index();
            $table
                ->unsignedBigInteger("delivery_id")
                ->nullable()
                ->index();
            $table->string("tracking_code")->index();
            $table->integer("tracking_status_enum")->index("trackings_tracking_status_enum_IDX");
            $table->tinyInteger("system_status_enum")->default(1);
            $table
                ->timestamp("created_at")
                ->nullable()
                ->index("trackings_created_at_IDX");
            $table->timestamp("updated_at")->nullable();
            $table->softDeletes()->index("trackings_deleted_at_IDX");
        });

        Schema::create("transaction_cloudfox", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table
                ->unsignedBigInteger("sale_id")
                ->nullable()
                ->index("transaction_cloudfox_sale_id_foreign");
            $table
                ->unsignedBigInteger("gateway_id")
                ->nullable()
                ->index("transaction_cloudfox_gateway_id_foreign");
            $table
                ->unsignedInteger("company_id")
                ->nullable()
                ->index("transaction_cloudfox_company_id_foreign");
            $table
                ->unsignedInteger("user_id")
                ->nullable()
                ->index("transaction_cloudfox_user_id_foreign");
            $table->string("value");
            $table->string("value_total");
            $table->string("status")->default("paid");
            $table->integer("status_enum")->default(2);
            $table->date("release_date");
            $table->dateTime("gateway_released_at")->nullable();
            $table->dateTime("gateway_transferred_at")->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create("transactions", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table
                ->unsignedBigInteger("sale_id")
                ->nullable()
                ->index("transacoes_venda_foreign");
            $table
                ->unsignedBigInteger("gateway_id")
                ->nullable()
                ->index("transactions_gateway_id_foreign");
            $table
                ->unsignedInteger("company_id")
                ->nullable()
                ->index("transacoes_empresa_foreign");
            $table
                ->unsignedInteger("user_id")
                ->nullable()
                ->index("transactions_user_id_foreign");
            $table
                ->unsignedBigInteger("withdrawal_id")
                ->nullable()
                ->index("transactions_withdrawal_id_foreign");
            $table
                ->unsignedBigInteger("invitation_id")
                ->nullable()
                ->index("transactions_invitation_id_foreign");
            $table->string("value");
            $table
                ->integer("type")
                ->nullable()
                ->index("transactions_type_IDX");
            $table->string("status")->nullable();
            $table
                ->integer("status_enum")
                ->default(0)
                ->index("transactions_status_enum_IDX");
            $table
                ->date("release_date")
                ->nullable()
                ->index("transactions_release_date_IDX");
            $table->string("installment_tax")->nullable();
            $table->string("checkout_tax")->default("0");
            $table->string("tax")->nullable();
            $table->integer("tax_type")->default(1);
            $table->string("transaction_tax")->nullable();
            $table->timestamp("gateway_released_at")->nullable();
            $table->boolean("gateway_transferred")->default(false);
            $table->dateTime("gateway_transferred_at")->nullable();
            $table
                ->boolean("is_waiting_withdrawal")
                ->default(false)
                ->index();
            $table->boolean("tracking_required")->default(true);
            $table->boolean("is_security_reserve")->default(false);
            $table
                ->timestamp("created_at")
                ->nullable()
                ->index("transactions_created_at_IDX");
            $table->timestamp("updated_at")->nullable();
            $table->softDeletes()->index("transactions_deleted_at_IDX");
        });

        Schema::create("transfeera_postbacks", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table
                ->unsignedInteger("company_id")
                ->nullable()
                ->index("transfeera_postbacks_company_id_foreign");
            $table
                ->unsignedBigInteger("withdrawal_id")
                ->nullable()
                ->index("transfeera_postbacks_withdrawal_id_foreign");
            $table->enum("source", ["payment", "contacerta"])->default("payment");
            $table->json("data");
            $table->tinyInteger("processed_flag")->default(0);
            $table->json("machine_result")->nullable();
            $table->timestamps();
        });

        Schema::create("transfeera_requests", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table
                ->unsignedInteger("company_id")
                ->nullable()
                ->index("transfeera_requests_company_id_foreign");
            $table
                ->unsignedBigInteger("withdrawal_id")
                ->nullable()
                ->index("transfeera_requests_withdrawal_id_foreign");
            $table->json("sent_data")->nullable();
            $table->json("response")->nullable();
            $table->enum("source", ["payment", "contacerta"])->default("payment");
            $table->timestamps();
        });

        Schema::create("transfers", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table
                ->unsignedBigInteger("transaction_id")
                ->nullable()
                ->index("transferencias_transacao_foreign");
            $table->unsignedInteger("user_id")->index("transferencias_user_foreign");
            $table
                ->unsignedInteger("company_id")
                ->nullable()
                ->index("transfers_company_id_foreign");
            $table
                ->unsignedBigInteger("gateway_id")
                ->nullable()
                ->index("transfers_gateway_id_foreign");
            $table
                ->unsignedBigInteger("customer_id")
                ->nullable()
                ->index("transfers_customer_id_foreign");
            $table->string("value")->index();
            $table->string("type")->nullable();
            $table
                ->integer("type_enum")
                ->nullable()
                ->index("transfers_type_enum_IDX");
            $table->string("reason")->nullable();
            $table
                ->boolean("is_refund_tax")
                ->default(false)
                ->index();
            $table
                ->timestamp("created_at")
                ->nullable()
                ->index();
            $table->timestamp("updated_at")->nullable();
            $table
                ->unsignedBigInteger("anticipation_id")
                ->nullable()
                ->index("transfers_anticipation_id_foreign");
            $table->softDeletes();
        });

        Schema::create("under_attacks", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table
                ->unsignedInteger("domain_id")
                ->nullable()
                ->index("under_attacks_domain_id_foreign");
            $table
                ->unsignedInteger("user_id")
                ->nullable()
                ->index("under_attacks_user_id_foreign");
            $table->enum("type", ["DOMAIN", "CARD_DECLINED"])->default("DOMAIN");
            $table->string("percentage_card_refused")->nullable();
            $table->date("start_date_card_refused")->nullable();
            $table->date("end_date_card_refused")->nullable();
            $table->string("total_refused")->nullable();
            $table->timestamp("removed_at")->nullable();
            $table->timestamps();
        });

        Schema::create("unicodrop_integrations", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->unsignedInteger("user_id")->index();
            $table->unsignedInteger("project_id")->index();
            $table->string("token");
            $table->boolean("abandoned_cart")->default(true);
            $table->boolean("billet_paid")->default(true);
            $table->boolean("billet_generated")->default(true);
            $table->boolean("credit_card_paid")->default(true);
            $table->boolean("credit_card_refused")->default(true);
            $table->boolean("pix")->default(true);
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create("user_antifraud_results", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->unsignedInteger("user_id")->index("user_antifraud_results_user_id_foreign");
            $table->string("action")->index();
            $table->unsignedBigInteger("antifraud_id")->index("user_antifraud_results_antifraud_id_foreign");
            $table->json("send_data");
            $table->json("antifraud_result")->nullable();
            $table->string("status")->index();
            $table->json("translated_codes")->nullable();
            $table->json("antifraud_exceptions")->nullable();
            $table->timestamps();
        });

        Schema::create("user_benefits", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->unsignedInteger("user_id")->index("user_benefits_user_id_foreign");
            $table->unsignedBigInteger("benefit_id")->index("user_benefits_benefit_id_foreign");
            $table->boolean("enabled")->default(false);
            $table->timestamps();
        });

        Schema::create("user_biometry_results", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->unsignedInteger("user_id")->index("user_biometry_results_user_id_foreign");
            $table->string("vendor")->index();
            $table
                ->string("biometry_id")
                ->nullable()
                ->index();
            $table->string("score")->nullable();
            $table
                ->string("status")
                ->nullable()
                ->index();
            $table->json("request_data")->nullable();
            $table->json("response_data")->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create("user_devices", function (Blueprint $table) {
            $table->increments("id");
            $table->unsignedInteger("user_id")->index("user_devices_user_id_foreign");
            $table->string("player_id");
            $table->boolean("online");
            $table->string("identifier")->nullable();
            $table->integer("session_count")->nullable();
            $table->string("language")->nullable();
            $table->integer("timezone")->nullable();
            $table->string("game_version")->nullable();
            $table->string("device_os")->nullable();
            $table->string("device_type")->nullable();
            $table->string("device_model")->nullable();
            $table->string("ad_id")->nullable();
            $table->json("tags")->nullable();
            $table->integer("last_active")->nullable();
            $table->integer("playtime")->nullable();
            $table->string("amount_spent")->nullable();
            $table->integer("onsignal_created_at")->nullable();
            $table->boolean("invalid_identifier")->nullable();
            $table->integer("badge_count")->nullable();
            $table->string("sdk")->nullable();
            $table->integer("test_type")->nullable();
            $table
                ->string("ip")
                ->nullable()
                ->index("user_devices_ip_IDX");
            $table->string("external_user_id")->nullable();
            $table->boolean("sale_notification");
            $table->boolean("billet_notification");
            $table->boolean("payment_notification");
            $table->boolean("withdraw_notification");
            $table->boolean("invitation_sale_notification");
            $table
                ->timestamp("created_at")
                ->nullable()
                ->index("user_devices_created_at_IDX");
            $table->timestamp("updated_at")->nullable();
        });

        Schema::create("user_documents", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->unsignedInteger("user_id")->index();
            $table->string("document_url", 500);
            $table->tinyInteger("document_type_enum")->index("user_documents_document_type_enum_IDX");
            $table
                ->tinyInteger("status")
                ->nullable()
                ->index("user_documents_status_IDX");
            $table->string("refused_reason")->nullable();
            $table
                ->timestamp("created_at")
                ->nullable()
                ->index("user_documents_created_at_IDX");
            $table->timestamp("updated_at")->nullable();
        });

        Schema::create("user_informations", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->integer("last_step")->default(0);
            $table->integer("status")->default(0);
            $table->string("email");
            $table->string("name")->nullable();
            $table->string("document")->unique("abandoned_signups_document_unique");
            $table->string("phone")->nullable();
            $table->string("monthly_income")->nullable();
            $table->string("observation")->nullable();
            $table->json("niche")->nullable();
            $table->string("website_url")->nullable();
            $table->string("gateway")->nullable();
            $table->json("ecommerce")->nullable();
            $table->json("cloudfox_referer")->nullable();
            $table->string("zip_code")->nullable();
            $table->string("country")->nullable();
            $table->string("state")->nullable();
            $table->string("city")->nullable();
            $table->string("district")->nullable();
            $table->string("street")->nullable();
            $table->string("number")->nullable();
            $table->string("complement")->nullable();
            $table->string("company_document")->nullable();
            $table->string("company_zip_code")->nullable();
            $table->string("company_country")->nullable();
            $table->string("company_state")->nullable();
            $table->string("company_city")->nullable();
            $table->string("company_district")->nullable();
            $table->string("company_street")->nullable();
            $table->string("company_number")->nullable();
            $table->string("company_complement")->nullable();
            $table->string("bank")->nullable();
            $table->string("agency")->nullable();
            $table->string("agency_digit")->nullable();
            $table->string("account")->nullable();
            $table->string("account_digit")->nullable();
            $table->timestamps();
        });

        Schema::create("user_notifications", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->unsignedInteger("user_id")->index("user_notifications_user_id_foreign");
            $table->boolean("affiliation")->default(true);
            $table->boolean("boleto_compensated")->default(true);
            $table->boolean("billet_generated")->default(true);
            $table->boolean("sale_approved")->default(true);
            $table->boolean("withdrawal_approved")->default(true);
            $table->boolean("domain_approved")->default(true);
            $table->boolean("shopify")->default(true);
            $table->boolean("ticket_open")->default(true);
            $table
                ->timestamp("created_at")
                ->nullable()
                ->index("user_notifications_created_at_IDX");
            $table->timestamp("updated_at")->nullable();
        });

        Schema::create("user_terms", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->unsignedInteger("user_id")->index("user_terms_user_id_foreign");
            $table->string("term_version")->nullable();
            $table->json("device_data")->nullable();
            $table
                ->dateTime("accepted_at")
                ->nullable()
                ->index("user_terms_accepted_at_IDX");
            $table
                ->timestamp("created_at")
                ->nullable()
                ->index("user_terms_created_at_IDX");
            $table->timestamp("updated_at")->nullable();
            $table->softDeletes()->index("user_terms_deleted_at_IDX");
        });

        Schema::create("users", function (Blueprint $table) {
            $table->increments("id");
            $table->string("name");
            $table->string("email")->unique();
            $table->boolean("email_verified")->default(false);
            $table
                ->integer("status")
                ->default(1)
                ->index("users_status_IDX");
            $table->string("password");
            $table->rememberToken();
            $table
                ->string("cellphone")
                ->nullable()
                ->index("users_cellphone_IDX");
            $table->boolean("cellphone_verified")->default(false);
            $table->string("document", 11)->index("users_document_IDX");
            $table->string("zip_code")->nullable();
            $table->char("country", 20)->nullable();
            $table->string("state")->nullable();
            $table->string("city")->nullable();
            $table->string("neighborhood")->nullable();
            $table->string("street")->nullable();
            $table->string("number")->nullable();
            $table->string("complement")->nullable();
            $table->string("photo")->nullable();
            $table->date("date_birth")->nullable();
            $table->tinyInteger("address_document_status")->default(1);
            $table->tinyInteger("personal_document_status")->default(1);
            $table->dateTime("date_last_document_notification")->nullable();
            $table->timestamp("last_login")->nullable();
            $table->integer("invites_amount")->default(5);
            $table
                ->unsignedInteger("account_owner_id")
                ->nullable()
                ->index("users_account_owner_id_foreign");
            $table->integer("logged_id")->nullable();
            $table
                ->unsignedBigInteger("subseller_owner_id")
                ->nullable()
                ->index();
            $table->boolean("deleted_project_filter")->default(true);
            $table->json("id_wall_result")->nullable();
            $table->json("bureau_result")->nullable();
            $table->tinyInteger("bureau_check_count")->default(0);
            $table->dateTime("bureau_data_updated_at")->nullable();
            $table->string("sex", 50)->nullable();
            $table->string("mother_name")->nullable();
            $table->boolean("has_sale_before_getnet")->default(false);
            $table->boolean("onboarding")->default(false);
            $table->text("observation")->nullable();
            $table->boolean("account_is_approved")->default(false);
            $table->double("chargeback_rate", 8, 2)->nullable();
            $table->double("contestation_rate", 8, 2)->nullable();
            $table->double("account_score", 8, 2)->nullable();
            $table->double("chargeback_score", 8, 2)->nullable();
            $table->double("attendance_score", 8, 2)->nullable();
            $table->double("tracking_score", 8, 2)->nullable();
            $table->double("attendance_average_response_time", 8, 2)->nullable();
            $table->double("installment_cashback", 8, 2)->default(0);
            $table->boolean("get_faster")->default(false);
            $table->integer("release_count")->default(0);
            $table->boolean("has_security_reserve")->default(true);
            $table->integer("security_reserve_rule")->default(20);
            $table->boolean("contestation_penalty")->default(true);
            $table->json("contestation_penalties_taxes")->nullable();
            $table->integer("level")->default(1);
            $table->boolean("ignore_automatic_benefits_updates")->default(false);
            $table->bigInteger("total_commission_value")->default(0);
            $table->boolean("show_old_finances")->default(false);
            $table->json("mkt_information")->nullable();
            $table->boolean("block_attendance_balance")->default(true);
            $table->integer("pipefy_card_id")->nullable();
            $table->json("pipefy_card_data")->nullable();
            $table
                ->unsignedInteger("company_default")
                ->nullable()
                ->default(1);
            $table->string("role_default", 30)->nullable();
            $table->integer("biometry_status")->default(1);
            $table->tinyInteger("is_cloudfox")->nullable();
            $table->json("utm_srcs")->nullable();
            $table
                ->timestamp("created_at")
                ->nullable()
                ->index("users_created_at_IDX");
            $table->timestamp("updated_at")->nullable();
            $table->softDeletes()->index("users_deleted_at_IDX");
        });

        Schema::create("users_projects", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table
                ->unsignedInteger("user_id")
                ->nullable()
                ->index("users_projetos_user_foreign");
            $table->unsignedInteger("project_id")->index("users_projetos_projeto_foreign");
            $table
                ->unsignedInteger("company_id")
                ->nullable()
                ->index("users_projetos_empresa_foreign");
            $table
                ->integer("type_enum")
                ->default(0)
                ->index("users_projects_type_enum_IDX");
            $table->string("type")->nullable();
            $table->string("remuneration_value")->nullable();
            $table->boolean("access_permission")->nullable();
            $table->boolean("edit_permission")->nullable();
            $table
                ->integer("status_flag")
                ->default(0)
                ->index("users_projects_status_flag_IDX");
            $table->string("status")->index("users_projects_status_IDX");
            $table
                ->unsignedInteger("order_priority")
                ->default(0)
                ->index("users_projects_order_priority_IDX");
            $table
                ->timestamp("created_at")
                ->nullable()
                ->index("users_projects_created_at_IDX");
            $table->timestamp("updated_at")->nullable();
            $table->softDeletes()->index("users_projects_deleted_at_IDX");
        });

        Schema::create("webhook_logs", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->unsignedBigInteger("webhook_id")->index();
            $table->unsignedInteger("user_id")->index();
            $table->unsignedInteger("company_id")->index();
            $table
                ->unsignedBigInteger("sale_id")
                ->nullable()
                ->index();
            $table->string("url");
            $table->json("sent_data")->nullable();
            $table->json("response")->nullable();
            $table->integer("response_status")->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create("webhooks", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->unsignedInteger("user_id")->index();
            $table->unsignedInteger("company_id")->index();
            $table->string("description");
            $table->string("url");
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create("whatsapp2_integrations", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->unsignedInteger("user_id")->index();
            $table->unsignedInteger("project_id")->index();
            $table->string("api_token", 50);
            $table->string("url_checkout");
            $table->string("url_order");
            $table->boolean("billet_generated")->default(true);
            $table->boolean("billet_paid")->default(true);
            $table->boolean("credit_card_refused")->default(true);
            $table->boolean("credit_card_paid")->default(true);
            $table->boolean("abandoned_cart")->default(true);
            $table->integer("pix_expired")->default(1);
            $table->integer("pix_paid")->default(1);
            $table->softDeletes()->index("whatsapp2_integrations_deleted_at_IDX");
            $table
                ->timestamp("created_at")
                ->nullable()
                ->index("whatsapp2_integrations_created_at_IDX");
            $table->timestamp("updated_at")->nullable();
        });

        Schema::create("whatsapp2_sent", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->text("data");
            $table->text("response");
            $table
                ->integer("sent_status")
                ->nullable()
                ->index("whatsapp2_sent_sent_status_IDX");
            $table
                ->unsignedInteger("event_sale")
                ->nullable()
                ->index("whatsapp2_sent_event_sale_IDX");
            $table
                ->unsignedBigInteger("instance_id")
                ->nullable()
                ->index("whatsapp2_sent_instance_id_IDX");
            $table->string("instance")->nullable();
            $table
                ->unsignedBigInteger("whatsapp2_integration_id")
                ->nullable()
                ->index("whatsapp2_sent_whatsapp2_integration_id_foreign");
            $table
                ->timestamp("created_at")
                ->nullable()
                ->index("whatsapp2_sent_created_at_IDX");
            $table->timestamp("updated_at")->nullable();
        });

        Schema::create("white_black_list", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table
                ->unsignedInteger("type_enum")
                ->index("white_black_list_type_enum_IDX")
                ->comment("Tipo (1 - White/ 2 - Black)");
            $table->string("rule")->comment("Regra");
            $table
                ->unsignedInteger("rule_enum")
                ->index("white_black_list_rule_enum_IDX")
                ->comment("Enum da regra");
            $table
                ->string("rule_type")
                ->default("1")
                ->comment("Tipo de regra (Equals, More, Less)");
            $table
                ->unsignedInteger("rule_type_enum")
                ->index("white_black_list_rule_type_enum_IDX")
                ->comment("Enum do tipo da regra (1 - Igual, 2 - Maior/Menor)");
            $table->string("value")->comment("Valor a verificar na regra");
            $table
                ->date("expires_at")
                ->nullable()
                ->index("white_black_list_expires_at_IDX");
            $table->text("description")->nullable();
            $table->unsignedInteger("count")->default(0);
            $table
                ->timestamp("created_at")
                ->nullable()
                ->index("white_black_list_created_at_IDX");
            $table->timestamp("updated_at")->nullable();
            $table->softDeletes()->index("white_black_list_deleted_at_IDX");
        });

        Schema::create("withdrawal_settings", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->unsignedInteger("company_id")->index();
            $table->string("rule")->nullable();
            $table->string("frequency")->nullable();
            $table
                ->unsignedTinyInteger("weekday")
                ->nullable()
                ->default(0);
            $table
                ->unsignedTinyInteger("day")
                ->nullable()
                ->default(1);
            $table->integer("amount")->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create("withdrawals", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->unsignedInteger("company_id")->index("withdrawals_company_id_foreign");
            $table
                ->unsignedBigInteger("gateway_id")
                ->nullable()
                ->index("withdrawals_gateway_id_foreign");
            $table->string("gateway_transfer_id")->nullable();
            $table->integer("status")->index("withdrawals_status_IDX");
            $table->boolean("automatic_liquidation")->default(false);
            $table->string("value");
            $table->integer("value_transferred")->nullable();
            $table->integer("debt_pending_value")->nullable();
            $table
                ->dateTime("release_date")
                ->nullable()
                ->index("withdrawals_release_date_IDX");
            $table->boolean("is_released")->default(false);
            $table->string("currency")->default("real");
            $table->string("transfer_type", 15)->nullable();
            $table->string("type_key_pix", 15)->nullable();
            $table->string("key_pix", 50)->nullable();
            $table->string("bank")->nullable();
            $table->string("agency")->nullable();
            $table->string("agency_digit")->nullable();
            $table->string("account")->nullable();
            $table->string("account_digit")->nullable();
            $table->string("currency_quotation")->nullable();
            $table->integer("abroad_transfer_tax")->nullable();
            $table->integer("tax")->default(0);
            $table->text("observation")->nullable();
            $table->string("file")->nullable();
            $table->string("refused_reason")->nullable();
            $table
                ->timestamp("created_at")
                ->nullable()
                ->index("withdrawals_created_at_IDX");
            $table->timestamp("updated_at")->nullable();
            $table->softDeletes();
        });

        Schema::create("woo_commerce_integrations", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->string("url_store");
            $table->unsignedInteger("user_id")->index();
            $table->unsignedInteger("project_id")->index();
            $table->string("token_user");
            $table->string("token_pass");
            $table->tinyInteger("status");
            $table->dateTime("synced_at")->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create("woo_commerce_integrations_sync", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->integer("status")->default(0);
            $table->bigInteger("integration_id")->nullable();
            $table->json("data")->nullable();
            $table->string("method")->nullable();
            $table->json("result")->nullable();
            $table->integer("tries")->default(0);
            $table->unsignedInteger("project_id")->index("woo_commerce_integrations_sync_project_id_foreign");
            $table->unsignedInteger("user_id")->index("woo_commerce_integrations_sync_user_id_foreign");
            $table->timestamps();
        });

        Schema::table("achievement_user", function (Blueprint $table) {
            $table
                ->foreign(["achievement_id"])
                ->references(["id"])
                ->on("achievements")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
            $table
                ->foreign(["user_id"])
                ->references(["id"])
                ->on("users")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("activecampaign_customs", function (Blueprint $table) {
            $table
                ->foreign(["activecampaign_integration_id"])
                ->references(["id"])
                ->on("activecampaign_integrations")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("activecampaign_events", function (Blueprint $table) {
            $table
                ->foreign(["activecampaign_integration_id"])
                ->references(["id"])
                ->on("activecampaign_integrations")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("activecampaign_integrations", function (Blueprint $table) {
            $table
                ->foreign(["project_id"])
                ->references(["id"])
                ->on("projects")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
            $table
                ->foreign(["user_id"])
                ->references(["id"])
                ->on("users")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("activecampaign_sent", function (Blueprint $table) {
            $table
                ->foreign(["activecampaign_integration_id"])
                ->references(["id"])
                ->on("activecampaign_integrations")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("affiliate_links", function (Blueprint $table) {
            $table
                ->foreign(["affiliate_id"], "links_afiliados_afiliado_foreign")
                ->references(["id"])
                ->on("affiliates")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
            // $table
            //     ->foreign(["campaign_id"], "links_afiliados_campanha_foreign")
            //     ->references(["id"])
            //     ->on("campaigns")
            //     ->onUpdate("NO ACTION")
            //     ->onDelete("NO ACTION");
            $table
                ->foreign(["plan_id"], "links_afiliados_plano_foreign")
                ->references(["id"])
                ->on("plans")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("affiliate_requests", function (Blueprint $table) {
            $table
                ->foreign(["company_id"])
                ->references(["id"])
                ->on("companies")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
            $table
                ->foreign(["project_id"], "solicitacoes_afiliacoes_projeto_foreign")
                ->references(["id"])
                ->on("projects")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
            $table
                ->foreign(["user_id"], "solicitacoes_afiliacoes_user_foreign")
                ->references(["id"])
                ->on("users")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("affiliates", function (Blueprint $table) {
            $table
                ->foreign(["company_id"], "afiliados_empresa_foreign")
                ->references(["id"])
                ->on("companies")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
            $table
                ->foreign(["project_id"], "afiliados_projeto_foreign")
                ->references(["id"])
                ->on("projects")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
            $table
                ->foreign(["user_id"], "afiliados_user_foreign")
                ->references(["id"])
                ->on("users")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("anticipated_transactions", function (Blueprint $table) {
            $table
                ->foreign(["anticipation_id"])
                ->references(["id"])
                ->on("anticipations")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
            $table
                ->foreign(["transaction_id"])
                ->references(["id"])
                ->on("transactions")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("anticipations", function (Blueprint $table) {
            $table
                ->foreign(["company_id"])
                ->references(["id"])
                ->on("companies")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("antifraud_quiz_questions", function (Blueprint $table) {
            $table
                ->foreign(["sale_id"])
                ->references(["id"])
                ->on("sales")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("antifraud_sale_reviews", function (Blueprint $table) {
            $table
                ->foreign(["sale_id"])
                ->references(["id"])
                ->on("sales")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
            $table
                ->foreign(["user_id"])
                ->references(["id"])
                ->on("users")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("antifraud_warnings", function (Blueprint $table) {
            $table
                ->foreign(["sale_id"])
                ->references(["id"])
                ->on("sales")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("api_tokens", function (Blueprint $table) {
            $table
                ->foreign(["company_id"])
                ->references(["id"])
                ->on("companies")
                ->onUpdate("NO ACTION")
                ->onDelete("CASCADE");
            $table
                ->foreign(["user_id"])
                ->references(["id"])
                ->on("users")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("asaas_anticipation_requests", function (Blueprint $table) {
            $table
                ->foreign(["company_id"])
                ->references(["id"])
                ->on("companies")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
            $table
                ->foreign(["sale_id"])
                ->references(["id"])
                ->on("sales")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("asaas_backoffice_requests", function (Blueprint $table) {
            $table
                ->foreign(["company_id"])
                ->references(["id"])
                ->on("companies")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("asaas_transfers", function (Blueprint $table) {
            $table
                ->foreign(["transfer_id"])
                ->references(["id"])
                ->on("transfers")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
            $table
                ->foreign(["withdrawal_id"])
                ->references(["id"])
                ->on("withdrawals")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("astron_members_integrations", function (Blueprint $table) {
            $table
                ->foreign(["project_id"])
                ->references(["id"])
                ->on("projects")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
            $table
                ->foreign(["user_id"])
                ->references(["id"])
                ->on("users")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("biometry_postbacks", function (Blueprint $table) {
            $table
                ->foreign(["user_biometry_resut_id"])
                ->references(["id"])
                ->on("user_biometry_results")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
            $table
                ->foreign(["user_id"])
                ->references(["id"])
                ->on("users")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("block_reason_sales", function (Blueprint $table) {
            $table
                ->foreign(["blocked_reason_id"])
                ->references(["id"])
                ->on("block_reasons")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
            $table
                ->foreign(["sale_id"])
                ->references(["id"])
                ->on("sales")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("bonus_balances", function (Blueprint $table) {
            $table
                ->foreign(["user_id"])
                ->references(["id"])
                ->on("users")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("braspag_backoffice_requests", function (Blueprint $table) {
            $table
                ->foreign(["company_id"])
                ->references(["id"])
                ->on("companies")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("cashbacks", function (Blueprint $table) {
            $table
                ->foreign(["company_id"])
                ->references(["id"])
                ->on("companies")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
            $table
                ->foreign(["sale_id"])
                ->references(["id"])
                ->on("sales")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
            $table
                ->foreign(["transaction_id"])
                ->references(["id"])
                ->on("transactions")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
            $table
                ->foreign(["user_id"])
                ->references(["id"])
                ->on("users")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("chargebacks", function (Blueprint $table) {
            $table
                ->foreign(["sale_id"])
                ->references(["id"])
                ->on("sales")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("checkout_api_postbacks", function (Blueprint $table) {
            $table
                ->foreign(["company_id"])
                ->references(["id"])
                ->on("companies")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
            $table
                ->foreign(["user_id"])
                ->references(["id"])
                ->on("users")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("checkout_configs", function (Blueprint $table) {
            $table
                ->foreign(["company_id"])
                ->references(["id"])
                ->on("companies")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
            $table
                ->foreign(["project_id"])
                ->references(["id"])
                ->on("projects")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("checkout_plans", function (Blueprint $table) {
            $table
                ->foreign(["checkout_id"], "planos_checkout_checkout_foreign")
                ->references(["id"])
                ->on("checkouts")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
            $table
                ->foreign(["plan_id"], "planos_checkout_plano_foreign")
                ->references(["id"])
                ->on("plans")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("checkouts", function (Blueprint $table) {
            $table
                ->foreign(["affiliate_id"])
                ->references(["id"])
                ->on("affiliates")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
            $table
                ->foreign(["project_id"], "checkouts_projeto_foreign")
                ->references(["id"])
                ->on("projects")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("companies", function (Blueprint $table) {
            $table
                ->foreign(["user_id"], "empresas_user_foreign")
                ->references(["id"])
                ->on("users")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("company_adjustments", function (Blueprint $table) {
            $table
                ->foreign(["company_id"])
                ->references(["id"])
                ->on("companies")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("company_balance_logs", function (Blueprint $table) {
            $table
                ->foreign(["company_id"])
                ->references(["id"])
                ->on("companies")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("company_balances", function (Blueprint $table) {
            $table
                ->foreign(["company_id"])
                ->references(["id"])
                ->on("companies")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("company_bank_accounts", function (Blueprint $table) {
            $table
                ->foreign(["company_id"])
                ->references(["id"])
                ->on("companies")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("company_documents", function (Blueprint $table) {
            $table
                ->foreign(["company_id"])
                ->references(["id"])
                ->on("companies")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("convertax_integrations", function (Blueprint $table) {
            $table
                ->foreign(["project_id"])
                ->references(["id"])
                ->on("projects")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
            $table
                ->foreign(["user_id"])
                ->references(["id"])
                ->on("users")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("customer_bank_accounts", function (Blueprint $table) {
            $table
                ->foreign(["customer_id"])
                ->references(["id"])
                ->on("customers")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("customer_bureau_results", function (Blueprint $table) {
            $table
                ->foreign(["customer_id"])
                ->references(["id"])
                ->on("customers")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("customer_cards", function (Blueprint $table) {
            $table
                ->foreign(["customer_id"], "client_cards_client_id_foreign")
                ->references(["id"])
                ->on("customers")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("customer_idwall_results", function (Blueprint $table) {
            $table
                ->foreign(["customer_id"])
                ->references(["id"])
                ->on("customers")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("customer_withdrawals", function (Blueprint $table) {
            $table
                ->foreign(["customer_id"])
                ->references(["id"])
                ->on("customers")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("dashboard_notifications", function (Blueprint $table) {
            $table
                ->foreign(["user_id"])
                ->references(["id"])
                ->on("users")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("deliveries", function (Blueprint $table) {
            $table
                ->foreign(["customer_id"])
                ->references(["id"])
                ->on("customers")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("digitalmanager_integrations", function (Blueprint $table) {
            $table
                ->foreign(["project_id"])
                ->references(["id"])
                ->on("projects")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
            $table
                ->foreign(["user_id"])
                ->references(["id"])
                ->on("users")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("digitalmanager_sent", function (Blueprint $table) {
            $table
                ->foreign(["digitalmanager_integration_id"])
                ->references(["id"])
                ->on("digitalmanager_integrations")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("discount_coupons", function (Blueprint $table) {
            $table
                ->foreign(["project_id"], "cupons_projeto_foreign")
                ->references(["id"])
                ->on("projects")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("domains", function (Blueprint $table) {
            $table
                ->foreign(["project_id"], "dominios_projeto_foreign")
                ->references(["id"])
                ->on("projects")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("domains_records", function (Blueprint $table) {
            $table
                ->foreign(["domain_id"])
                ->references(["id"])
                ->on("domains")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("ethoca_postbacks", function (Blueprint $table) {
            $table
                ->foreign(["sale_id"])
                ->references(["id"])
                ->on("sales")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("gateway_configs", function (Blueprint $table) {
            $table
                ->foreign(["gateway_id"])
                ->references(["id"])
                ->on("gateways")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("gateway_flag_taxes", function (Blueprint $table) {
            $table
                ->foreign(["gateway_flag_id"])
                ->references(["id"])
                ->on("gateway_flags")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("gateway_flags", function (Blueprint $table) {
            $table
                ->foreign(["gateway_id"])
                ->references(["id"])
                ->on("gateways")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("gateway_postbacks", function (Blueprint $table) {
            $table
                ->foreign(["gateway_id"])
                ->references(["id"])
                ->on("gateways")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
            $table
                ->foreign(["sale_id"])
                ->references(["id"])
                ->on("sales")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("gateways_backoffice_requests", function (Blueprint $table) {
            $table
                ->foreign(["company_id"])
                ->references(["id"])
                ->on("companies")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
            $table
                ->foreign(["gateway_id"])
                ->references(["id"])
                ->on("gateways")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("gateways_companies_credentials", function (Blueprint $table) {
            $table
                ->foreign(["company_id"])
                ->references(["id"])
                ->on("companies")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
            $table
                ->foreign(["gateway_id"])
                ->references(["id"])
                ->on("gateways")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("getnet_backoffice_requests", function (Blueprint $table) {
            $table
                ->foreign(["company_id"])
                ->references(["id"])
                ->on("companies")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("getnet_chargeback_details", function (Blueprint $table) {
            $table
                ->foreign(["getnet_chargeback_id"])
                ->references(["id"])
                ->on("getnet_chargebacks")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("getnet_chargebacks", function (Blueprint $table) {
            $table
                ->foreign(["company_id"])
                ->references(["id"])
                ->on("companies")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
            $table
                ->foreign(["project_id"])
                ->references(["id"])
                ->on("projects")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
            $table
                ->foreign(["sale_id"])
                ->references(["id"])
                ->on("sales")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
            $table
                ->foreign(["user_id"])
                ->references(["id"])
                ->on("users")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("getnet_searches", function (Blueprint $table) {
            $table
                ->foreign(["company_id"])
                ->references(["id"])
                ->on("companies")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("getnet_transactions", function (Blueprint $table) {
            $table
                ->foreign(["company_id"])
                ->references(["id"])
                ->on("companies")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
            $table
                ->foreign(["sale_id"])
                ->references(["id"])
                ->on("sales")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("hotbillet_integrations", function (Blueprint $table) {
            $table
                ->foreign(["project_id"])
                ->references(["id"])
                ->on("projects")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
            $table
                ->foreign(["user_id"])
                ->references(["id"])
                ->on("users")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("hotzapp_integrations", function (Blueprint $table) {
            $table
                ->foreign(["project_id"])
                ->references(["id"])
                ->on("projects")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
            $table
                ->foreign(["user_id"])
                ->references(["id"])
                ->on("users")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("invitations", function (Blueprint $table) {
            $table
                ->foreign(["company_id"], "convites_empresa_foreign")
                ->references(["id"])
                ->on("companies")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
            $table
                ->foreign(["user_invited"], "convites_user_convidado_foreign")
                ->references(["id"])
                ->on("users")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
            $table
                ->foreign(["invite"], "convites_user_convite_foreign")
                ->references(["id"])
                ->on("users")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("iugu_credit_card_charges", function (Blueprint $table) {
            $table
                ->foreign(["sale_id"])
                ->references(["id"])
                ->on("sales")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("logs", function (Blueprint $table) {
            $table
                ->foreign(["checkout_id"])
                ->references(["id"])
                ->on("checkouts")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("manager_2auth_tokens", function (Blueprint $table) {
            $table
                ->foreign(["user_id"])
                ->references(["id"])
                ->on("users")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("manager_to_sirius_logins", function (Blueprint $table) {
            $table
                ->foreign(["manager_user_id"])
                ->references(["id"])
                ->on("users")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
            $table
                ->foreign(["sirius_user_id"])
                ->references(["id"])
                ->on("users")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("melhorenvio_integrations", function (Blueprint $table) {
            $table
                ->foreign(["user_id"])
                ->references(["id"])
                ->on("users")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("model_has_permissions", function (Blueprint $table) {
            $table
                ->foreign(["permission_id"])
                ->references(["id"])
                ->on("permissions")
                ->onUpdate("NO ACTION")
                ->onDelete("CASCADE");
        });

        Schema::table("model_has_roles", function (Blueprint $table) {
            $table
                ->foreign(["role_id"])
                ->references(["id"])
                ->on("roles")
                ->onUpdate("NO ACTION")
                ->onDelete("CASCADE");
        });

        Schema::table("monitored_scheduled_task_log_items", function (Blueprint $table) {
            $table
                ->foreign(["monitored_scheduled_task_id"], "fk_scheduled_task_id")
                ->references(["id"])
                ->on("monitored_scheduled_tasks")
                ->onUpdate("NO ACTION")
                ->onDelete("CASCADE");
        });

        Schema::table("nethone_antifraud_transaction", function (Blueprint $table) {
            $table
                ->foreign(["sale_id"])
                ->references(["id"])
                ->on("sales")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("notazz_integrations", function (Blueprint $table) {
            $table
                ->foreign(["project_id"])
                ->references(["id"])
                ->on("projects")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
            $table
                ->foreign(["user_id"])
                ->references(["id"])
                ->on("users")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("notazz_invoices", function (Blueprint $table) {
            $table
                ->foreign(["currency_quotation_id"])
                ->references(["id"])
                ->on("currency_quotations")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
            $table
                ->foreign(["notazz_integration_id"])
                ->references(["id"])
                ->on("notazz_integrations")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
            $table
                ->foreign(["sale_id"])
                ->references(["id"])
                ->on("sales")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("notazz_sent_histories", function (Blueprint $table) {
            $table
                ->foreign(["notazz_invoice_id"])
                ->references(["id"])
                ->on("notazz_invoices")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("notificacoes_inteligentes_integrations", function (Blueprint $table) {
            $table
                ->foreign(["project_id"])
                ->references(["id"])
                ->on("projects")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
            $table
                ->foreign(["user_id"])
                ->references(["id"])
                ->on("users")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("order_bump_rules", function (Blueprint $table) {
            $table
                ->foreign(["project_id"])
                ->references(["id"])
                ->on("projects")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("pending_debt_withdrawals", function (Blueprint $table) {
            $table
                ->foreign(["pending_debt_id"])
                ->references(["id"])
                ->on("pending_debts")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
            $table
                ->foreign(["withdrawal_id"])
                ->references(["id"])
                ->on("withdrawals")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("pending_debts", function (Blueprint $table) {
            $table
                ->foreign(["company_id"])
                ->references(["id"])
                ->on("companies")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
            $table
                ->foreign(["sale_id"])
                ->references(["id"])
                ->on("sales")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("pix_charges", function (Blueprint $table) {
            $table
                ->foreign(["gateway_id"], "fk_gateway_id")
                ->references(["id"])
                ->on("gateways")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
            $table
                ->foreign(["sale_id"], "fk_sale_id")
                ->references(["id"])
                ->on("sales")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("pix_transfer_requests", function (Blueprint $table) {
            $table
                ->foreign(["company_id"])
                ->references(["id"])
                ->on("companies")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
            $table
                ->foreign(["withdrawal_id"])
                ->references(["id"])
                ->on("withdrawals")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("pix_transfers", function (Blueprint $table) {
            $table
                ->foreign(["withdrawal_id"])
                ->references(["id"])
                ->on("withdrawals")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("pixel_configs", function (Blueprint $table) {
            $table
                ->foreign(["project_id"])
                ->references(["id"])
                ->on("projects")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("pixels", function (Blueprint $table) {
            $table
                ->foreign(["affiliate_id"])
                ->references(["id"])
                ->on("affiliates")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
            // $table
            //     ->foreign(["campaign_id"], "pixels_campanha_foreign")
            //     ->references(["id"])
            //     ->on("campaigns")
            //     ->onUpdate("NO ACTION")
            //     ->onDelete("NO ACTION");
            $table
                ->foreign(["project_id"], "pixels_projeto_foreign")
                ->references(["id"])
                ->on("projects")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("plan_sale_products", function (Blueprint $table) {
            $table
                ->foreign(["product_id"])
                ->references(["id"])
                ->on("products")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("plans", function (Blueprint $table) {
            $table
                ->foreign(["project_id"], "planos_projeto_foreign")
                ->references(["id"])
                ->on("projects")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("plans_sales", function (Blueprint $table) {
            $table
                ->foreign(["plan_id"], "planos_vendas_plano_foreign")
                ->references(["id"])
                ->on("plans")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
            $table
                ->foreign(["sale_id"], "planos_vendas_venda_foreign")
                ->references(["id"])
                ->on("sales")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("products", function (Blueprint $table) {
            $table
                ->foreign(["project_id"])
                ->references(["id"])
                ->on("projects")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
            $table
                ->foreign(["category_id"], "produtos_categoria_foreign")
                ->references(["id"])
                ->on("categories")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
            $table
                ->foreign(["user_id"], "produtos_user_foreign")
                ->references(["id"])
                ->on("users")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("products_plans", function (Blueprint $table) {
            $table
                ->foreign(["plan_id"], "produtos_planos_plano_foreign")
                ->references(["id"])
                ->on("plans")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
            $table
                ->foreign(["product_id"], "produtos_planos_produto_foreign")
                ->references(["id"])
                ->on("products")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("products_plans_sales", function (Blueprint $table) {
            $table
                ->foreign(["plan_id"])
                ->references(["id"])
                ->on("plans")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
            $table
                ->foreign(["products_sales_api_id"])
                ->references(["id"])
                ->on("products_sales_api")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
            $table
                ->foreign(["product_id"])
                ->references(["id"])
                ->on("products")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
            $table
                ->foreign(["sale_id"])
                ->references(["id"])
                ->on("sales")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("products_sales_api", function (Blueprint $table) {
            $table
                ->foreign(["sale_id"], "products_sales_sale_id_foreign")
                ->references(["id"])
                ->on("sales")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("project_notifications", function (Blueprint $table) {
            $table
                ->foreign(["project_id"])
                ->references(["id"])
                ->on("projects")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("project_reviews", function (Blueprint $table) {
            $table
                ->foreign(["project_id"])
                ->references(["id"])
                ->on("projects")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("project_upsell_configs", function (Blueprint $table) {
            $table
                ->foreign(["project_id"])
                ->references(["id"])
                ->on("projects")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("project_upsell_rules", function (Blueprint $table) {
            $table
                ->foreign(["project_id"])
                ->references(["id"])
                ->on("projects")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        // Schema::table("projects", function (Blueprint $table) {
        //     $table
        //         ->foreign(["carrier_id"], "projetos_transportadora_foreign")
        //         ->references(["id"])
        //         ->on("carriers")
        //         ->onUpdate("NO ACTION")
        //         ->onDelete("NO ACTION");
        // });

        Schema::table("promotional_taxes", function (Blueprint $table) {
            $table
                ->foreign(["user_id"])
                ->references(["id"])
                ->on("users")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("push_notifications", function (Blueprint $table) {
            $table
                ->foreign(["user_id"])
                ->references(["id"])
                ->on("users")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("regenerated_billets", function (Blueprint $table) {
            $table
                ->foreign(["gateway_id"])
                ->references(["id"])
                ->on("gateways")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
            $table
                ->foreign(["sale_id"])
                ->references(["id"])
                ->on("sales")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("reportana_integrations", function (Blueprint $table) {
            $table
                ->foreign(["project_id"])
                ->references(["id"])
                ->on("projects")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
            $table
                ->foreign(["user_id"])
                ->references(["id"])
                ->on("users")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("reportana_sent", function (Blueprint $table) {
            $table
                ->foreign(["reportana_integration_id"])
                ->references(["id"])
                ->on("reportana_integrations")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("role_has_permissions", function (Blueprint $table) {
            $table
                ->foreign(["permission_id"])
                ->references(["id"])
                ->on("permissions")
                ->onUpdate("NO ACTION")
                ->onDelete("CASCADE");
            $table
                ->foreign(["role_id"])
                ->references(["id"])
                ->on("roles")
                ->onUpdate("NO ACTION")
                ->onDelete("CASCADE");
        });

        Schema::table("sale_additional_customer_informations", function (Blueprint $table) {
            $table
                ->foreign(["plan_id"])
                ->references(["id"])
                ->on("plans")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
            $table
                ->foreign(["product_id"])
                ->references(["id"])
                ->on("products")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
            $table
                ->foreign(["sale_id"])
                ->references(["id"])
                ->on("sales")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("sale_antifraud_results", function (Blueprint $table) {
            $table
                ->foreign(["antifraud_id"])
                ->references(["id"])
                ->on("antifrauds")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
            $table
                ->foreign(["sale_id"])
                ->references(["id"])
                ->on("sales")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("sale_biometry_results", function (Blueprint $table) {
            $table
                ->foreign(["sale_id"])
                ->references(["id"])
                ->on("sales")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("sale_contestation_files", function (Blueprint $table) {
            $table
                ->foreign(["contestation_sale_id"])
                ->references(["id"])
                ->on("sale_contestations")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
            $table
                ->foreign(["user_id"])
                ->references(["id"])
                ->on("users")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("sale_contestations", function (Blueprint $table) {
            $table
                ->foreign(["gateway_id"])
                ->references(["id"])
                ->on("gateways")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
            $table
                ->foreign(["sale_id"])
                ->references(["id"])
                ->on("sales")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("sale_gateway_requests", function (Blueprint $table) {
            $table
                ->foreign(["gateway_id"])
                ->references(["id"])
                ->on("gateways")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
            $table
                ->foreign(["sale_id"])
                ->references(["id"])
                ->on("sales")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("sale_idwall_questions", function (Blueprint $table) {
            $table
                ->foreign(["sale_id"])
                ->references(["id"])
                ->on("sales")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("sale_informations", function (Blueprint $table) {
            $table
                ->foreign(["sale_id"])
                ->references(["id"])
                ->on("sales")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("sale_logs", function (Blueprint $table) {
            $table
                ->foreign(["sale_id"])
                ->references(["id"])
                ->on("sales")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("sale_refund_histories", function (Blueprint $table) {
            $table
                ->foreign(["sale_id"])
                ->references(["id"])
                ->on("sales")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
            $table
                ->foreign(["user_id"])
                ->references(["id"])
                ->on("users")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("sale_shopify_requests", function (Blueprint $table) {
            $table
                ->foreign(["sale_id"])
                ->references(["id"])
                ->on("sales")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("sale_under_attacks", function (Blueprint $table) {
            $table
                ->foreign(["sale_id"])
                ->references(["id"])
                ->on("sales")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
            $table
                ->foreign(["under_attack_id"])
                ->references(["id"])
                ->on("under_attacks")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("sale_white_black_list_results", function (Blueprint $table) {
            $table
                ->foreign(["sale_id"])
                ->references(["id"])
                ->on("sales")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("sales", function (Blueprint $table) {
            $table
                ->foreign(["api_token_id"])
                ->references(["id"])
                ->on("api_tokens")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
            $table
                ->foreign(["checkout_id"], "sales_checkout_foreign")
                ->references(["id"])
                ->on("checkouts")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
            $table
                ->foreign(["customer_card_id"], "sales_client_card_id_foreign")
                ->references(["id"])
                ->on("customer_cards")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
            $table
                ->foreign(["gateway_id"])
                ->references(["id"])
                ->on("gateways")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
            $table
                ->foreign(["project_id"], "sales_project_foreign")
                ->references(["id"])
                ->on("projects")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
            $table
                ->foreign(["shipping_id"], "sales_shipping_foreign")
                ->references(["id"])
                ->on("shippings")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
            $table
                ->foreign(["affiliate_id"], "vendas_afiliado_foreign")
                ->references(["id"])
                ->on("affiliates")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
            $table
                ->foreign(["customer_id"], "vendas_comprador_foreign")
                ->references(["id"])
                ->on("customers")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
            $table
                ->foreign(["delivery_id"], "vendas_entrega_foreign")
                ->references(["id"])
                ->on("deliveries")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
            $table
                ->foreign(["owner_id"], "vendas_proprietario_foreign")
                ->references(["id"])
                ->on("users")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("shippings", function (Blueprint $table) {
            $table
                ->foreign(["melhorenvio_integration_id"])
                ->references(["id"])
                ->on("melhorenvio_integrations")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
            $table
                ->foreign(["project_id"], "shippings_project_foreign")
                ->references(["id"])
                ->on("projects")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("shopify_integrations", function (Blueprint $table) {
            $table
                ->foreign(["project_id"], "integracoes_shopify_projeto_foreign")
                ->references(["id"])
                ->on("projects")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
            $table
                ->foreign(["user_id"], "integracoes_shopify_user_foreign")
                ->references(["id"])
                ->on("users")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("smartfunnel_integrations", function (Blueprint $table) {
            $table
                ->foreign(["project_id"])
                ->references(["id"])
                ->on("projects")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
            $table
                ->foreign(["user_id"])
                ->references(["id"])
                ->on("users")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("smartfunnel_sent", function (Blueprint $table) {
            $table
                ->foreign(["sale_id"])
                ->references(["id"])
                ->on("sales")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
            $table
                ->foreign(["smartfunnel_integration_id"])
                ->references(["id"])
                ->on("smartfunnel_integrations")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("sms_messages", function (Blueprint $table) {
            $table
                ->foreign(["plan"], "mensagens_sms_plano_foreign")
                ->references(["id"])
                ->on("plans")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
            $table
                ->foreign(["user"], "mensagens_sms_user_foreign")
                ->references(["id"])
                ->on("users")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("suspect_bots", function (Blueprint $table) {
            $table
                ->foreign(["checkout_id"])
                ->references(["id"])
                ->on("checkouts")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("tags_tickets", function (Blueprint $table) {
            $table
                ->foreign(["tag_id"])
                ->references(["id"])
                ->on("tags")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
            $table
                ->foreign(["ticket_id"])
                ->references(["id"])
                ->on("tickets")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("tasks_users", function (Blueprint $table) {
            $table
                ->foreign(["task_id"])
                ->references(["id"])
                ->on("tasks")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
            $table
                ->foreign(["user_id"])
                ->references(["id"])
                ->on("users")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("ticket_attachments", function (Blueprint $table) {
            $table
                ->foreign(["ticket_id"])
                ->references(["id"])
                ->on("tickets")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("ticket_messages", function (Blueprint $table) {
            $table
                ->foreign(["ticket_id"])
                ->references(["id"])
                ->on("tickets")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("tickets", function (Blueprint $table) {
            $table
                ->foreign(["customer_id"])
                ->references(["id"])
                ->on("customers")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
            $table
                ->foreign(["manager_user_id"])
                ->references(["id"])
                ->on("users")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
            $table
                ->foreign(["sale_id"])
                ->references(["id"])
                ->on("sales")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("tracking_histories", function (Blueprint $table) {
            $table
                ->foreign(["tracking_id"])
                ->references(["id"])
                ->on("trackings")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("trackings", function (Blueprint $table) {
            $table
                ->foreign(["delivery_id"])
                ->references(["id"])
                ->on("deliveries")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
            $table
                ->foreign(["product_id"])
                ->references(["id"])
                ->on("products")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
            $table
                ->foreign(["product_plan_sale_id"])
                ->references(["id"])
                ->on("products_plans_sales")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
            $table
                ->foreign(["sale_id"])
                ->references(["id"])
                ->on("sales")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("transaction_cloudfox", function (Blueprint $table) {
            $table
                ->foreign(["company_id"])
                ->references(["id"])
                ->on("companies")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
            $table
                ->foreign(["gateway_id"])
                ->references(["id"])
                ->on("gateways")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
            $table
                ->foreign(["sale_id"])
                ->references(["id"])
                ->on("sales")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
            $table
                ->foreign(["user_id"])
                ->references(["id"])
                ->on("users")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("transactions", function (Blueprint $table) {
            $table
                ->foreign(["company_id"], "transacoes_empresa_foreign")
                ->references(["id"])
                ->on("companies")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
            $table
                ->foreign(["sale_id"], "transacoes_venda_foreign")
                ->references(["id"])
                ->on("sales")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
            $table
                ->foreign(["gateway_id"])
                ->references(["id"])
                ->on("gateways")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
            $table
                ->foreign(["invitation_id"])
                ->references(["id"])
                ->on("invitations")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
            $table
                ->foreign(["user_id"])
                ->references(["id"])
                ->on("users")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
            $table
                ->foreign(["withdrawal_id"])
                ->references(["id"])
                ->on("withdrawals")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("transfeera_postbacks", function (Blueprint $table) {
            $table
                ->foreign(["company_id"])
                ->references(["id"])
                ->on("companies")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
            $table
                ->foreign(["withdrawal_id"])
                ->references(["id"])
                ->on("withdrawals")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("transfeera_requests", function (Blueprint $table) {
            $table
                ->foreign(["company_id"])
                ->references(["id"])
                ->on("companies")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
            $table
                ->foreign(["withdrawal_id"])
                ->references(["id"])
                ->on("withdrawals")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("transfers", function (Blueprint $table) {
            $table
                ->foreign(["transaction_id"], "transferencias_transacao_foreign")
                ->references(["id"])
                ->on("transactions")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
            $table
                ->foreign(["user_id"], "transferencias_user_foreign")
                ->references(["id"])
                ->on("users")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
            $table
                ->foreign(["anticipation_id"])
                ->references(["id"])
                ->on("anticipations")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
            $table
                ->foreign(["company_id"])
                ->references(["id"])
                ->on("companies")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
            $table
                ->foreign(["customer_id"])
                ->references(["id"])
                ->on("customers")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
            $table
                ->foreign(["gateway_id"])
                ->references(["id"])
                ->on("gateways")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("under_attacks", function (Blueprint $table) {
            $table
                ->foreign(["domain_id"])
                ->references(["id"])
                ->on("domains")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
            $table
                ->foreign(["user_id"])
                ->references(["id"])
                ->on("users")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("unicodrop_integrations", function (Blueprint $table) {
            $table
                ->foreign(["project_id"])
                ->references(["id"])
                ->on("projects")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
            $table
                ->foreign(["user_id"])
                ->references(["id"])
                ->on("users")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("user_antifraud_results", function (Blueprint $table) {
            $table
                ->foreign(["antifraud_id"])
                ->references(["id"])
                ->on("antifrauds")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
            $table
                ->foreign(["user_id"])
                ->references(["id"])
                ->on("users")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("user_benefits", function (Blueprint $table) {
            $table
                ->foreign(["benefit_id"])
                ->references(["id"])
                ->on("benefits")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
            $table
                ->foreign(["user_id"])
                ->references(["id"])
                ->on("users")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("user_biometry_results", function (Blueprint $table) {
            $table
                ->foreign(["user_id"])
                ->references(["id"])
                ->on("users")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("user_devices", function (Blueprint $table) {
            $table
                ->foreign(["user_id"])
                ->references(["id"])
                ->on("users")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("user_documents", function (Blueprint $table) {
            $table
                ->foreign(["user_id"])
                ->references(["id"])
                ->on("users")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("user_notifications", function (Blueprint $table) {
            $table
                ->foreign(["user_id"])
                ->references(["id"])
                ->on("users")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("user_terms", function (Blueprint $table) {
            $table
                ->foreign(["user_id"])
                ->references(["id"])
                ->on("users")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("users", function (Blueprint $table) {
            $table
                ->foreign(["account_owner_id"])
                ->references(["id"])
                ->on("users")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("users_projects", function (Blueprint $table) {
            $table
                ->foreign(["company_id"], "users_projetos_empresa_foreign")
                ->references(["id"])
                ->on("companies")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
            $table
                ->foreign(["project_id"], "users_projetos_projeto_foreign")
                ->references(["id"])
                ->on("projects")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
            $table
                ->foreign(["user_id"], "users_projetos_user_foreign")
                ->references(["id"])
                ->on("users")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("webhook_logs", function (Blueprint $table) {
            $table
                ->foreign(["company_id"])
                ->references(["id"])
                ->on("companies")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
            $table
                ->foreign(["sale_id"])
                ->references(["id"])
                ->on("sales")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
            $table
                ->foreign(["user_id"])
                ->references(["id"])
                ->on("users")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
            $table
                ->foreign(["webhook_id"])
                ->references(["id"])
                ->on("webhooks")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("webhooks", function (Blueprint $table) {
            $table
                ->foreign(["company_id"])
                ->references(["id"])
                ->on("companies")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
            $table
                ->foreign(["user_id"])
                ->references(["id"])
                ->on("users")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("whatsapp2_integrations", function (Blueprint $table) {
            $table
                ->foreign(["project_id"])
                ->references(["id"])
                ->on("projects")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
            $table
                ->foreign(["user_id"])
                ->references(["id"])
                ->on("users")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("whatsapp2_sent", function (Blueprint $table) {
            $table
                ->foreign(["whatsapp2_integration_id"])
                ->references(["id"])
                ->on("whatsapp2_integrations")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("withdrawal_settings", function (Blueprint $table) {
            $table
                ->foreign(["company_id"])
                ->references(["id"])
                ->on("companies")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("withdrawals", function (Blueprint $table) {
            $table
                ->foreign(["company_id"])
                ->references(["id"])
                ->on("companies")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
            $table
                ->foreign(["gateway_id"])
                ->references(["id"])
                ->on("gateways")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("woo_commerce_integrations", function (Blueprint $table) {
            $table
                ->foreign(["project_id"])
                ->references(["id"])
                ->on("projects")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
            $table
                ->foreign(["user_id"])
                ->references(["id"])
                ->on("users")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });

        Schema::table("woo_commerce_integrations_sync", function (Blueprint $table) {
            $table
                ->foreign(["project_id"])
                ->references(["id"])
                ->on("projects")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
            $table
                ->foreign(["user_id"])
                ->references(["id"])
                ->on("users")
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
        Schema::table("woo_commerce_integrations_sync", function (Blueprint $table) {
            $table->dropForeign("woo_commerce_integrations_sync_project_id_foreign");
            $table->dropForeign("woo_commerce_integrations_sync_user_id_foreign");
        });

        Schema::table("woo_commerce_integrations", function (Blueprint $table) {
            $table->dropForeign("woo_commerce_integrations_project_id_foreign");
            $table->dropForeign("woo_commerce_integrations_user_id_foreign");
        });

        Schema::table("withdrawals", function (Blueprint $table) {
            $table->dropForeign("withdrawals_company_id_foreign");
            $table->dropForeign("withdrawals_gateway_id_foreign");
        });

        Schema::table("withdrawal_settings", function (Blueprint $table) {
            $table->dropForeign("withdrawal_settings_company_id_foreign");
        });

        Schema::table("whatsapp2_sent", function (Blueprint $table) {
            $table->dropForeign("whatsapp2_sent_whatsapp2_integration_id_foreign");
        });

        Schema::table("whatsapp2_integrations", function (Blueprint $table) {
            $table->dropForeign("whatsapp2_integrations_project_id_foreign");
            $table->dropForeign("whatsapp2_integrations_user_id_foreign");
        });

        Schema::table("webhooks", function (Blueprint $table) {
            $table->dropForeign("webhooks_company_id_foreign");
            $table->dropForeign("webhooks_user_id_foreign");
        });

        Schema::table("webhook_logs", function (Blueprint $table) {
            $table->dropForeign("webhook_logs_company_id_foreign");
            $table->dropForeign("webhook_logs_sale_id_foreign");
            $table->dropForeign("webhook_logs_user_id_foreign");
            $table->dropForeign("webhook_logs_webhook_id_foreign");
        });

        Schema::table("users_projects", function (Blueprint $table) {
            $table->dropForeign("users_projetos_empresa_foreign");
            $table->dropForeign("users_projetos_projeto_foreign");
            $table->dropForeign("users_projetos_user_foreign");
        });

        Schema::table("users", function (Blueprint $table) {
            $table->dropForeign("users_account_owner_id_foreign");
        });

        Schema::table("user_terms", function (Blueprint $table) {
            $table->dropForeign("user_terms_user_id_foreign");
        });

        Schema::table("user_notifications", function (Blueprint $table) {
            $table->dropForeign("user_notifications_user_id_foreign");
        });

        Schema::table("user_documents", function (Blueprint $table) {
            $table->dropForeign("user_documents_user_id_foreign");
        });

        Schema::table("user_devices", function (Blueprint $table) {
            $table->dropForeign("user_devices_user_id_foreign");
        });

        Schema::table("user_biometry_results", function (Blueprint $table) {
            $table->dropForeign("user_biometry_results_user_id_foreign");
        });

        Schema::table("user_benefits", function (Blueprint $table) {
            $table->dropForeign("user_benefits_benefit_id_foreign");
            $table->dropForeign("user_benefits_user_id_foreign");
        });

        Schema::table("user_antifraud_results", function (Blueprint $table) {
            $table->dropForeign("user_antifraud_results_antifraud_id_foreign");
            $table->dropForeign("user_antifraud_results_user_id_foreign");
        });

        Schema::table("unicodrop_integrations", function (Blueprint $table) {
            $table->dropForeign("unicodrop_integrations_project_id_foreign");
            $table->dropForeign("unicodrop_integrations_user_id_foreign");
        });

        Schema::table("under_attacks", function (Blueprint $table) {
            $table->dropForeign("under_attacks_domain_id_foreign");
            $table->dropForeign("under_attacks_user_id_foreign");
        });

        Schema::table("transfers", function (Blueprint $table) {
            $table->dropForeign("transferencias_transacao_foreign");
            $table->dropForeign("transferencias_user_foreign");
            $table->dropForeign("transfers_anticipation_id_foreign");
            $table->dropForeign("transfers_company_id_foreign");
            $table->dropForeign("transfers_customer_id_foreign");
            $table->dropForeign("transfers_gateway_id_foreign");
        });

        Schema::table("transfeera_requests", function (Blueprint $table) {
            $table->dropForeign("transfeera_requests_company_id_foreign");
            $table->dropForeign("transfeera_requests_withdrawal_id_foreign");
        });

        Schema::table("transfeera_postbacks", function (Blueprint $table) {
            $table->dropForeign("transfeera_postbacks_company_id_foreign");
            $table->dropForeign("transfeera_postbacks_withdrawal_id_foreign");
        });

        Schema::table("transactions", function (Blueprint $table) {
            $table->dropForeign("transacoes_empresa_foreign");
            $table->dropForeign("transacoes_venda_foreign");
            $table->dropForeign("transactions_gateway_id_foreign");
            $table->dropForeign("transactions_invitation_id_foreign");
            $table->dropForeign("transactions_user_id_foreign");
            $table->dropForeign("transactions_withdrawal_id_foreign");
        });

        Schema::table("transaction_cloudfox", function (Blueprint $table) {
            $table->dropForeign("transaction_cloudfox_company_id_foreign");
            $table->dropForeign("transaction_cloudfox_gateway_id_foreign");
            $table->dropForeign("transaction_cloudfox_sale_id_foreign");
            $table->dropForeign("transaction_cloudfox_user_id_foreign");
        });

        Schema::table("trackings", function (Blueprint $table) {
            $table->dropForeign("trackings_delivery_id_foreign");
            $table->dropForeign("trackings_product_id_foreign");
            $table->dropForeign("trackings_product_plan_sale_id_foreign");
            $table->dropForeign("trackings_sale_id_foreign");
        });

        Schema::table("tracking_histories", function (Blueprint $table) {
            $table->dropForeign("tracking_histories_tracking_id_foreign");
        });

        Schema::table("tickets", function (Blueprint $table) {
            $table->dropForeign("tickets_customer_id_foreign");
            $table->dropForeign("tickets_manager_user_id_foreign");
            $table->dropForeign("tickets_sale_id_foreign");
        });

        Schema::table("ticket_messages", function (Blueprint $table) {
            $table->dropForeign("ticket_messages_ticket_id_foreign");
        });

        Schema::table("ticket_attachments", function (Blueprint $table) {
            $table->dropForeign("ticket_attachments_ticket_id_foreign");
        });

        Schema::table("tasks_users", function (Blueprint $table) {
            $table->dropForeign("tasks_users_task_id_foreign");
            $table->dropForeign("tasks_users_user_id_foreign");
        });

        Schema::table("tags_tickets", function (Blueprint $table) {
            $table->dropForeign("tags_tickets_tag_id_foreign");
            $table->dropForeign("tags_tickets_ticket_id_foreign");
        });

        Schema::table("suspect_bots", function (Blueprint $table) {
            $table->dropForeign("suspect_bots_checkout_id_foreign");
        });

        Schema::table("sms_messages", function (Blueprint $table) {
            $table->dropForeign("mensagens_sms_plano_foreign");
            $table->dropForeign("mensagens_sms_user_foreign");
        });

        Schema::table("smartfunnel_sent", function (Blueprint $table) {
            $table->dropForeign("smartfunnel_sent_sale_id_foreign");
            $table->dropForeign("smartfunnel_sent_smartfunnel_integration_id_foreign");
        });

        Schema::table("smartfunnel_integrations", function (Blueprint $table) {
            $table->dropForeign("smartfunnel_integrations_project_id_foreign");
            $table->dropForeign("smartfunnel_integrations_user_id_foreign");
        });

        Schema::table("shopify_integrations", function (Blueprint $table) {
            $table->dropForeign("integracoes_shopify_projeto_foreign");
            $table->dropForeign("integracoes_shopify_user_foreign");
        });

        Schema::table("shippings", function (Blueprint $table) {
            $table->dropForeign("shippings_melhorenvio_integration_id_foreign");
            $table->dropForeign("shippings_project_foreign");
        });

        Schema::table("sales", function (Blueprint $table) {
            $table->dropForeign("sales_api_token_id_foreign");
            $table->dropForeign("sales_checkout_foreign");
            $table->dropForeign("sales_client_card_id_foreign");
            $table->dropForeign("sales_gateway_id_foreign");
            $table->dropForeign("sales_project_foreign");
            $table->dropForeign("sales_shipping_foreign");
            $table->dropForeign("vendas_afiliado_foreign");
            $table->dropForeign("vendas_comprador_foreign");
            $table->dropForeign("vendas_entrega_foreign");
            $table->dropForeign("vendas_proprietario_foreign");
        });

        Schema::table("sale_white_black_list_results", function (Blueprint $table) {
            $table->dropForeign("sale_white_black_list_results_sale_id_foreign");
        });

        Schema::table("sale_under_attacks", function (Blueprint $table) {
            $table->dropForeign("sale_under_attacks_sale_id_foreign");
            $table->dropForeign("sale_under_attacks_under_attack_id_foreign");
        });

        Schema::table("sale_shopify_requests", function (Blueprint $table) {
            $table->dropForeign("sale_shopify_requests_sale_id_foreign");
        });

        Schema::table("sale_refund_histories", function (Blueprint $table) {
            $table->dropForeign("sale_refund_histories_sale_id_foreign");
            $table->dropForeign("sale_refund_histories_user_id_foreign");
        });

        Schema::table("sale_logs", function (Blueprint $table) {
            $table->dropForeign("sale_logs_sale_id_foreign");
        });

        Schema::table("sale_informations", function (Blueprint $table) {
            $table->dropForeign("sale_informations_sale_id_foreign");
        });

        Schema::table("sale_idwall_questions", function (Blueprint $table) {
            $table->dropForeign("sale_idwall_questions_sale_id_foreign");
        });

        Schema::table("sale_gateway_requests", function (Blueprint $table) {
            $table->dropForeign("sale_gateway_requests_gateway_id_foreign");
            $table->dropForeign("sale_gateway_requests_sale_id_foreign");
        });

        Schema::table("sale_contestations", function (Blueprint $table) {
            $table->dropForeign("sale_contestations_gateway_id_foreign");
            $table->dropForeign("sale_contestations_sale_id_foreign");
        });

        Schema::table("sale_contestation_files", function (Blueprint $table) {
            $table->dropForeign("sale_contestation_files_contestation_sale_id_foreign");
            $table->dropForeign("sale_contestation_files_user_id_foreign");
        });

        Schema::table("sale_biometry_results", function (Blueprint $table) {
            $table->dropForeign("sale_biometry_results_sale_id_foreign");
        });

        Schema::table("sale_antifraud_results", function (Blueprint $table) {
            $table->dropForeign("sale_antifraud_results_antifraud_id_foreign");
            $table->dropForeign("sale_antifraud_results_sale_id_foreign");
        });

        Schema::table("sale_additional_customer_informations", function (Blueprint $table) {
            $table->dropForeign("sale_additional_customer_informations_plan_id_foreign");
            $table->dropForeign("sale_additional_customer_informations_product_id_foreign");
            $table->dropForeign("sale_additional_customer_informations_sale_id_foreign");
        });

        Schema::table("role_has_permissions", function (Blueprint $table) {
            $table->dropForeign("role_has_permissions_permission_id_foreign");
            $table->dropForeign("role_has_permissions_role_id_foreign");
        });

        Schema::table("reportana_sent", function (Blueprint $table) {
            $table->dropForeign("reportana_sent_reportana_integration_id_foreign");
        });

        Schema::table("reportana_integrations", function (Blueprint $table) {
            $table->dropForeign("reportana_integrations_project_id_foreign");
            $table->dropForeign("reportana_integrations_user_id_foreign");
        });

        Schema::table("regenerated_billets", function (Blueprint $table) {
            $table->dropForeign("regenerated_billets_gateway_id_foreign");
            $table->dropForeign("regenerated_billets_sale_id_foreign");
        });

        Schema::table("push_notifications", function (Blueprint $table) {
            $table->dropForeign("push_notifications_user_id_foreign");
        });

        Schema::table("promotional_taxes", function (Blueprint $table) {
            $table->dropForeign("promotional_taxes_user_id_foreign");
        });

        Schema::table("projects", function (Blueprint $table) {
            $table->dropForeign("projetos_transportadora_foreign");
        });

        Schema::table("project_upsell_rules", function (Blueprint $table) {
            $table->dropForeign("project_upsell_rules_project_id_foreign");
        });

        Schema::table("project_upsell_configs", function (Blueprint $table) {
            $table->dropForeign("project_upsell_configs_project_id_foreign");
        });

        Schema::table("project_reviews", function (Blueprint $table) {
            $table->dropForeign("project_reviews_project_id_foreign");
        });

        Schema::table("project_notifications", function (Blueprint $table) {
            $table->dropForeign("project_notifications_project_id_foreign");
        });

        Schema::table("products_sales_api", function (Blueprint $table) {
            $table->dropForeign("products_sales_sale_id_foreign");
        });

        Schema::table("products_plans_sales", function (Blueprint $table) {
            $table->dropForeign("products_plans_sales_plan_id_foreign");
            $table->dropForeign("products_plans_sales_products_sales_api_id_foreign");
            $table->dropForeign("products_plans_sales_product_id_foreign");
            $table->dropForeign("products_plans_sales_sale_id_foreign");
        });

        Schema::table("products_plans", function (Blueprint $table) {
            $table->dropForeign("produtos_planos_plano_foreign");
            $table->dropForeign("produtos_planos_produto_foreign");
        });

        Schema::table("products", function (Blueprint $table) {
            $table->dropForeign("products_project_id_foreign");
            $table->dropForeign("produtos_categoria_foreign");
            $table->dropForeign("produtos_user_foreign");
        });

        Schema::table("plans_sales", function (Blueprint $table) {
            $table->dropForeign("planos_vendas_plano_foreign");
            $table->dropForeign("planos_vendas_venda_foreign");
        });

        Schema::table("plans", function (Blueprint $table) {
            $table->dropForeign("planos_projeto_foreign");
        });

        Schema::table("plan_sale_products", function (Blueprint $table) {
            $table->dropForeign("plan_sale_products_product_id_foreign");
        });

        Schema::table("pixels", function (Blueprint $table) {
            $table->dropForeign("pixels_affiliate_id_foreign");
            $table->dropForeign("pixels_campanha_foreign");
            $table->dropForeign("pixels_projeto_foreign");
        });

        Schema::table("pixel_configs", function (Blueprint $table) {
            $table->dropForeign("pixel_configs_project_id_foreign");
        });

        Schema::table("pix_transfers", function (Blueprint $table) {
            $table->dropForeign("pix_transfers_withdrawal_id_foreign");
        });

        Schema::table("pix_transfer_requests", function (Blueprint $table) {
            $table->dropForeign("pix_transfer_requests_company_id_foreign");
            $table->dropForeign("pix_transfer_requests_withdrawal_id_foreign");
        });

        Schema::table("pix_charges", function (Blueprint $table) {
            $table->dropForeign("fk_gateway_id");
            $table->dropForeign("fk_sale_id");
        });

        Schema::table("pending_debts", function (Blueprint $table) {
            $table->dropForeign("pending_debts_company_id_foreign");
            $table->dropForeign("pending_debts_sale_id_foreign");
        });

        Schema::table("pending_debt_withdrawals", function (Blueprint $table) {
            $table->dropForeign("pending_debt_withdrawals_pending_debt_id_foreign");
            $table->dropForeign("pending_debt_withdrawals_withdrawal_id_foreign");
        });

        Schema::table("order_bump_rules", function (Blueprint $table) {
            $table->dropForeign("order_bump_rules_project_id_foreign");
        });

        Schema::table("notificacoes_inteligentes_integrations", function (Blueprint $table) {
            $table->dropForeign("notificacoes_inteligentes_integrations_project_id_foreign");
            $table->dropForeign("notificacoes_inteligentes_integrations_user_id_foreign");
        });

        Schema::table("notazz_sent_histories", function (Blueprint $table) {
            $table->dropForeign("notazz_sent_histories_notazz_invoice_id_foreign");
        });

        Schema::table("notazz_invoices", function (Blueprint $table) {
            $table->dropForeign("notazz_invoices_currency_quotation_id_foreign");
            $table->dropForeign("notazz_invoices_notazz_integration_id_foreign");
            $table->dropForeign("notazz_invoices_sale_id_foreign");
        });

        Schema::table("notazz_integrations", function (Blueprint $table) {
            $table->dropForeign("notazz_integrations_project_id_foreign");
            $table->dropForeign("notazz_integrations_user_id_foreign");
        });

        Schema::table("nethone_antifraud_transaction", function (Blueprint $table) {
            $table->dropForeign("nethone_antifraud_transaction_sale_id_foreign");
        });

        Schema::table("monitored_scheduled_task_log_items", function (Blueprint $table) {
            $table->dropForeign("fk_scheduled_task_id");
        });

        Schema::table("model_has_roles", function (Blueprint $table) {
            $table->dropForeign("model_has_roles_role_id_foreign");
        });

        Schema::table("model_has_permissions", function (Blueprint $table) {
            $table->dropForeign("model_has_permissions_permission_id_foreign");
        });

        Schema::table("melhorenvio_integrations", function (Blueprint $table) {
            $table->dropForeign("melhorenvio_integrations_user_id_foreign");
        });

        Schema::table("manager_to_sirius_logins", function (Blueprint $table) {
            $table->dropForeign("manager_to_sirius_logins_manager_user_id_foreign");
            $table->dropForeign("manager_to_sirius_logins_sirius_user_id_foreign");
        });

        Schema::table("manager_2auth_tokens", function (Blueprint $table) {
            $table->dropForeign("manager_2auth_tokens_user_id_foreign");
        });

        Schema::table("logs", function (Blueprint $table) {
            $table->dropForeign("logs_checkout_id_foreign");
        });

        Schema::table("iugu_credit_card_charges", function (Blueprint $table) {
            $table->dropForeign("iugu_credit_card_charges_sale_id_foreign");
        });

        Schema::table("invitations", function (Blueprint $table) {
            $table->dropForeign("convites_empresa_foreign");
            $table->dropForeign("convites_user_convidado_foreign");
            $table->dropForeign("convites_user_convite_foreign");
        });

        Schema::table("hotzapp_integrations", function (Blueprint $table) {
            $table->dropForeign("hotzapp_integrations_project_id_foreign");
            $table->dropForeign("hotzapp_integrations_user_id_foreign");
        });

        Schema::table("hotbillet_integrations", function (Blueprint $table) {
            $table->dropForeign("hotbillet_integrations_project_id_foreign");
            $table->dropForeign("hotbillet_integrations_user_id_foreign");
        });

        Schema::table("getnet_transactions", function (Blueprint $table) {
            $table->dropForeign("getnet_transactions_company_id_foreign");
            $table->dropForeign("getnet_transactions_sale_id_foreign");
        });

        Schema::table("getnet_searches", function (Blueprint $table) {
            $table->dropForeign("getnet_searches_company_id_foreign");
        });

        Schema::table("getnet_chargebacks", function (Blueprint $table) {
            $table->dropForeign("getnet_chargebacks_company_id_foreign");
            $table->dropForeign("getnet_chargebacks_project_id_foreign");
            $table->dropForeign("getnet_chargebacks_sale_id_foreign");
            $table->dropForeign("getnet_chargebacks_user_id_foreign");
        });

        Schema::table("getnet_chargeback_details", function (Blueprint $table) {
            $table->dropForeign("getnet_chargeback_details_getnet_chargeback_id_foreign");
        });

        Schema::table("getnet_backoffice_requests", function (Blueprint $table) {
            $table->dropForeign("getnet_backoffice_requests_company_id_foreign");
        });

        Schema::table("gateways_companies_credentials", function (Blueprint $table) {
            $table->dropForeign("gateways_companies_credentials_company_id_foreign");
            $table->dropForeign("gateways_companies_credentials_gateway_id_foreign");
        });

        Schema::table("gateways_backoffice_requests", function (Blueprint $table) {
            $table->dropForeign("gateways_backoffice_requests_company_id_foreign");
            $table->dropForeign("gateways_backoffice_requests_gateway_id_foreign");
        });

        Schema::table("gateway_postbacks", function (Blueprint $table) {
            $table->dropForeign("gateway_postbacks_gateway_id_foreign");
            $table->dropForeign("gateway_postbacks_sale_id_foreign");
        });

        Schema::table("gateway_flags", function (Blueprint $table) {
            $table->dropForeign("gateway_flags_gateway_id_foreign");
        });

        Schema::table("gateway_flag_taxes", function (Blueprint $table) {
            $table->dropForeign("gateway_flag_taxes_gateway_flag_id_foreign");
        });

        Schema::table("gateway_configs", function (Blueprint $table) {
            $table->dropForeign("gateway_configs_gateway_id_foreign");
        });

        Schema::table("ethoca_postbacks", function (Blueprint $table) {
            $table->dropForeign("ethoca_postbacks_sale_id_foreign");
        });

        Schema::table("domains_records", function (Blueprint $table) {
            $table->dropForeign("domains_records_domain_id_foreign");
        });

        Schema::table("domains", function (Blueprint $table) {
            $table->dropForeign("dominios_projeto_foreign");
        });

        Schema::table("discount_coupons", function (Blueprint $table) {
            $table->dropForeign("cupons_projeto_foreign");
        });

        Schema::table("digitalmanager_sent", function (Blueprint $table) {
            $table->dropForeign("digitalmanager_sent_digitalmanager_integration_id_foreign");
        });

        Schema::table("digitalmanager_integrations", function (Blueprint $table) {
            $table->dropForeign("digitalmanager_integrations_project_id_foreign");
            $table->dropForeign("digitalmanager_integrations_user_id_foreign");
        });

        Schema::table("deliveries", function (Blueprint $table) {
            $table->dropForeign("deliveries_customer_id_foreign");
        });

        Schema::table("dashboard_notifications", function (Blueprint $table) {
            $table->dropForeign("dashboard_notifications_user_id_foreign");
        });

        Schema::table("customer_withdrawals", function (Blueprint $table) {
            $table->dropForeign("customer_withdrawals_customer_id_foreign");
        });

        Schema::table("customer_idwall_results", function (Blueprint $table) {
            $table->dropForeign("customer_idwall_results_customer_id_foreign");
        });

        Schema::table("customer_cards", function (Blueprint $table) {
            $table->dropForeign("client_cards_client_id_foreign");
        });

        Schema::table("customer_bureau_results", function (Blueprint $table) {
            $table->dropForeign("customer_bureau_results_customer_id_foreign");
        });

        Schema::table("customer_bank_accounts", function (Blueprint $table) {
            $table->dropForeign("customer_bank_accounts_customer_id_foreign");
        });

        Schema::table("convertax_integrations", function (Blueprint $table) {
            $table->dropForeign("convertax_integrations_project_id_foreign");
            $table->dropForeign("convertax_integrations_user_id_foreign");
        });

        Schema::table("company_documents", function (Blueprint $table) {
            $table->dropForeign("company_documents_company_id_foreign");
        });

        Schema::table("company_bank_accounts", function (Blueprint $table) {
            $table->dropForeign("company_bank_accounts_company_id_foreign");
        });

        Schema::table("company_balances", function (Blueprint $table) {
            $table->dropForeign("company_balances_company_id_foreign");
        });

        Schema::table("company_balance_logs", function (Blueprint $table) {
            $table->dropForeign("company_balance_logs_company_id_foreign");
        });

        Schema::table("company_adjustments", function (Blueprint $table) {
            $table->dropForeign("company_adjustments_company_id_foreign");
        });

        Schema::table("companies", function (Blueprint $table) {
            $table->dropForeign("empresas_user_foreign");
        });

        Schema::table("checkouts", function (Blueprint $table) {
            $table->dropForeign("checkouts_affiliate_id_foreign");
            $table->dropForeign("checkouts_projeto_foreign");
        });

        Schema::table("checkout_plans", function (Blueprint $table) {
            $table->dropForeign("planos_checkout_checkout_foreign");
            $table->dropForeign("planos_checkout_plano_foreign");
        });

        Schema::table("checkout_configs", function (Blueprint $table) {
            $table->dropForeign("checkout_configs_company_id_foreign");
            $table->dropForeign("checkout_configs_project_id_foreign");
        });

        Schema::table("checkout_api_postbacks", function (Blueprint $table) {
            $table->dropForeign("checkout_api_postbacks_company_id_foreign");
            $table->dropForeign("checkout_api_postbacks_user_id_foreign");
        });

        Schema::table("chargebacks", function (Blueprint $table) {
            $table->dropForeign("chargebacks_sale_id_foreign");
        });

        Schema::table("cashbacks", function (Blueprint $table) {
            $table->dropForeign("cashbacks_company_id_foreign");
            $table->dropForeign("cashbacks_sale_id_foreign");
            $table->dropForeign("cashbacks_transaction_id_foreign");
            $table->dropForeign("cashbacks_user_id_foreign");
        });

        Schema::table("braspag_backoffice_requests", function (Blueprint $table) {
            $table->dropForeign("braspag_backoffice_requests_company_id_foreign");
        });

        Schema::table("bonus_balances", function (Blueprint $table) {
            $table->dropForeign("bonus_balances_user_id_foreign");
        });

        Schema::table("block_reason_sales", function (Blueprint $table) {
            $table->dropForeign("block_reason_sales_blocked_reason_id_foreign");
            $table->dropForeign("block_reason_sales_sale_id_foreign");
        });

        Schema::table("biometry_postbacks", function (Blueprint $table) {
            $table->dropForeign("biometry_postbacks_user_biometry_resut_id_foreign");
            $table->dropForeign("biometry_postbacks_user_id_foreign");
        });

        Schema::table("astron_members_integrations", function (Blueprint $table) {
            $table->dropForeign("astron_members_integrations_project_id_foreign");
            $table->dropForeign("astron_members_integrations_user_id_foreign");
        });

        Schema::table("asaas_transfers", function (Blueprint $table) {
            $table->dropForeign("asaas_transfers_transfer_id_foreign");
            $table->dropForeign("asaas_transfers_withdrawal_id_foreign");
        });

        Schema::table("asaas_backoffice_requests", function (Blueprint $table) {
            $table->dropForeign("asaas_backoffice_requests_company_id_foreign");
        });

        Schema::table("asaas_anticipation_requests", function (Blueprint $table) {
            $table->dropForeign("asaas_anticipation_requests_company_id_foreign");
            $table->dropForeign("asaas_anticipation_requests_sale_id_foreign");
        });

        Schema::table("api_tokens", function (Blueprint $table) {
            $table->dropForeign("api_tokens_company_id_foreign");
            $table->dropForeign("api_tokens_user_id_foreign");
        });

        Schema::table("antifraud_warnings", function (Blueprint $table) {
            $table->dropForeign("antifraud_warnings_sale_id_foreign");
        });

        Schema::table("antifraud_sale_reviews", function (Blueprint $table) {
            $table->dropForeign("antifraud_sale_reviews_sale_id_foreign");
            $table->dropForeign("antifraud_sale_reviews_user_id_foreign");
        });

        Schema::table("antifraud_quiz_questions", function (Blueprint $table) {
            $table->dropForeign("antifraud_quiz_questions_sale_id_foreign");
        });

        Schema::table("anticipations", function (Blueprint $table) {
            $table->dropForeign("anticipations_company_id_foreign");
        });

        Schema::table("anticipated_transactions", function (Blueprint $table) {
            $table->dropForeign("anticipated_transactions_anticipation_id_foreign");
            $table->dropForeign("anticipated_transactions_transaction_id_foreign");
        });

        Schema::table("affiliates", function (Blueprint $table) {
            $table->dropForeign("afiliados_empresa_foreign");
            $table->dropForeign("afiliados_projeto_foreign");
            $table->dropForeign("afiliados_user_foreign");
        });

        Schema::table("affiliate_requests", function (Blueprint $table) {
            $table->dropForeign("affiliate_requests_company_id_foreign");
            $table->dropForeign("solicitacoes_afiliacoes_projeto_foreign");
            $table->dropForeign("solicitacoes_afiliacoes_user_foreign");
        });

        Schema::table("affiliate_links", function (Blueprint $table) {
            $table->dropForeign("links_afiliados_afiliado_foreign");
            $table->dropForeign("links_afiliados_campanha_foreign");
            $table->dropForeign("links_afiliados_plano_foreign");
        });

        Schema::table("activecampaign_sent", function (Blueprint $table) {
            $table->dropForeign("activecampaign_sent_activecampaign_integration_id_foreign");
        });

        Schema::table("activecampaign_integrations", function (Blueprint $table) {
            $table->dropForeign("activecampaign_integrations_project_id_foreign");
            $table->dropForeign("activecampaign_integrations_user_id_foreign");
        });

        Schema::table("activecampaign_events", function (Blueprint $table) {
            $table->dropForeign("activecampaign_events_activecampaign_integration_id_foreign");
        });

        Schema::table("activecampaign_customs", function (Blueprint $table) {
            $table->dropForeign("activecampaign_customs_activecampaign_integration_id_foreign");
        });

        Schema::table("achievement_user", function (Blueprint $table) {
            $table->dropForeign("achievement_user_achievement_id_foreign");
            $table->dropForeign("achievement_user_user_id_foreign");
        });

        Schema::dropIfExists("woo_commerce_integrations_sync");

        Schema::dropIfExists("woo_commerce_integrations");

        Schema::dropIfExists("withdrawals");

        Schema::dropIfExists("withdrawal_settings");

        Schema::dropIfExists("white_black_list");

        Schema::dropIfExists("whatsapp2_sent");

        Schema::dropIfExists("whatsapp2_integrations");

        Schema::dropIfExists("webhooks");

        Schema::dropIfExists("webhook_logs");

        Schema::dropIfExists("users_projects");

        Schema::dropIfExists("users");

        Schema::dropIfExists("user_terms");

        Schema::dropIfExists("user_notifications");

        Schema::dropIfExists("user_informations");

        Schema::dropIfExists("user_documents");

        Schema::dropIfExists("user_devices");

        Schema::dropIfExists("user_biometry_results");

        Schema::dropIfExists("user_benefits");

        Schema::dropIfExists("user_antifraud_results");

        Schema::dropIfExists("unicodrop_integrations");

        Schema::dropIfExists("under_attacks");

        Schema::dropIfExists("transfers");

        Schema::dropIfExists("transfeera_requests");

        Schema::dropIfExists("transfeera_postbacks");

        Schema::dropIfExists("transactions");

        Schema::dropIfExists("transaction_cloudfox");

        Schema::dropIfExists("trackings");

        Schema::dropIfExists("tracking_histories");

        Schema::dropIfExists("tickets");

        Schema::dropIfExists("ticket_messages");

        Schema::dropIfExists("ticket_attachments");

        Schema::dropIfExists("tasks_users");

        Schema::dropIfExists("tasks");

        Schema::dropIfExists("tags_tickets");

        Schema::dropIfExists("tags");

        Schema::dropIfExists("suspect_bots");

        Schema::dropIfExists("sms_messages");

        Schema::dropIfExists("smartfunnel_sent");

        Schema::dropIfExists("smartfunnel_integrations");

        Schema::dropIfExists("site_invitations_requests");

        Schema::dropIfExists("shopify_integrations");

        Schema::dropIfExists("shippings");

        Schema::dropIfExists("settings");

        Schema::dropIfExists("sent_emails");

        Schema::dropIfExists("sales");

        Schema::dropIfExists("sale_woocommerce_requests");

        Schema::dropIfExists("sale_white_black_list_results");

        Schema::dropIfExists("sale_under_attacks");

        Schema::dropIfExists("sale_shopify_requests");

        Schema::dropIfExists("sale_refund_histories");

        Schema::dropIfExists("sale_logs");

        Schema::dropIfExists("sale_informations");

        Schema::dropIfExists("sale_idwall_questions");

        Schema::dropIfExists("sale_gateway_requests");

        Schema::dropIfExists("sale_contestations");

        Schema::dropIfExists("sale_contestation_files");

        Schema::dropIfExists("sale_biometry_results");

        Schema::dropIfExists("sale_antifraud_results");

        Schema::dropIfExists("sale_additional_customer_informations");

        Schema::dropIfExists("roles");

        Schema::dropIfExists("role_has_permissions");

        Schema::dropIfExists("reportana_sent");

        Schema::dropIfExists("reportana_integrations");

        Schema::dropIfExists("registration_token");

        Schema::dropIfExists("regenerated_billets");

        Schema::dropIfExists("push_notifications");

        Schema::dropIfExists("promotional_taxes");

        Schema::dropIfExists("projects");

        Schema::dropIfExists("project_upsell_rules");

        Schema::dropIfExists("project_upsell_configs");

        Schema::dropIfExists("project_reviews");

        Schema::dropIfExists("project_notifications");

        Schema::dropIfExists("products_sales_api");

        Schema::dropIfExists("products_plans_sales");

        Schema::dropIfExists("products_plans");

        Schema::dropIfExists("products");

        Schema::dropIfExists("postback_logs");

        Schema::dropIfExists("plans_sales");

        Schema::dropIfExists("plans");

        Schema::dropIfExists("plan_sale_products");

        Schema::dropIfExists("pixels");

        Schema::dropIfExists("pixel_configs");

        Schema::dropIfExists("pix_transfers");

        Schema::dropIfExists("pix_transfer_requests");

        Schema::dropIfExists("pix_charges");

        Schema::dropIfExists("permissions");

        Schema::dropIfExists("pending_debts");

        Schema::dropIfExists("pending_debt_withdrawals");

        Schema::dropIfExists("password_resets");

        Schema::dropIfExists("order_bump_rules");

        Schema::dropIfExists("oauth_refresh_tokens");

        Schema::dropIfExists("oauth_personal_access_clients");

        Schema::dropIfExists("oauth_clients");

        Schema::dropIfExists("oauth_auth_codes");

        Schema::dropIfExists("oauth_access_tokens");

        Schema::dropIfExists("notifications");

        Schema::dropIfExists("notificacoes_inteligentes_integrations");

        Schema::dropIfExists("notazz_sent_histories");

        Schema::dropIfExists("notazz_invoices");

        Schema::dropIfExists("notazz_integrations");

        Schema::dropIfExists("nethone_antifraud_transaction");

        Schema::dropIfExists("monitored_scheduled_tasks");

        Schema::dropIfExists("monitored_scheduled_task_log_items");

        Schema::dropIfExists("model_has_roles");

        Schema::dropIfExists("model_has_permissions");

        Schema::dropIfExists("melhorenvio_integrations");

        Schema::dropIfExists("manager_to_sirius_logins");

        Schema::dropIfExists("manager_2auth_tokens");

        Schema::dropIfExists("logs");

        Schema::dropIfExists("jobs");

        Schema::dropIfExists("iugu_credit_card_charges");

        Schema::dropIfExists("invitations");

        Schema::dropIfExists("integration_logs");

        Schema::dropIfExists("hotzapp_integrations");

        Schema::dropIfExists("hotbillet_integrations");

        Schema::dropIfExists("health_check_result_history_items");

        Schema::dropIfExists("getnet_transactions");

        Schema::dropIfExists("getnet_searches");

        Schema::dropIfExists("getnet_postbacks");

        Schema::dropIfExists("getnet_chargebacks");

        Schema::dropIfExists("getnet_chargeback_details");

        Schema::dropIfExists("getnet_backoffice_requests");

        Schema::dropIfExists("gateways_companies_credentials");

        Schema::dropIfExists("gateways_backoffice_requests");

        Schema::dropIfExists("gateways");

        Schema::dropIfExists("gateway_postbacks");

        Schema::dropIfExists("gateway_flags");

        Schema::dropIfExists("gateway_flag_taxes");

        Schema::dropIfExists("gateway_configs");

        Schema::dropIfExists("failed_jobs");

        Schema::dropIfExists("ethoca_postbacks");

        Schema::dropIfExists("domains_records");

        Schema::dropIfExists("domains");

        Schema::dropIfExists("discount_coupons");

        Schema::dropIfExists("digitalmanager_sent");

        Schema::dropIfExists("digitalmanager_integrations");

        Schema::dropIfExists("deliveries");

        Schema::dropIfExists("dashboard_notifications");

        Schema::dropIfExists("customers");

        Schema::dropIfExists("customer_withdrawals");

        Schema::dropIfExists("customer_idwall_results");

        Schema::dropIfExists("customer_cards");

        Schema::dropIfExists("customer_bureau_results");

        Schema::dropIfExists("customer_bank_accounts");

        Schema::dropIfExists("currency_quotations");

        Schema::dropIfExists("convertax_integrations");

        Schema::dropIfExists("company_documents");

        Schema::dropIfExists("company_bank_accounts");

        Schema::dropIfExists("company_balances");

        Schema::dropIfExists("company_balance_logs");

        Schema::dropIfExists("company_adjustments");

        Schema::dropIfExists("companies");

        Schema::dropIfExists("checkouts");

        Schema::dropIfExists("checkout_plans");

        Schema::dropIfExists("checkout_configs");

        Schema::dropIfExists("checkout_api_postbacks");

        Schema::dropIfExists("chargebacks");

        Schema::dropIfExists("categories");

        Schema::dropIfExists("cashbacks");

        Schema::dropIfExists("braspag_backoffice_requests");

        Schema::dropIfExists("braspag_backoffice_postbacks");

        Schema::dropIfExists("bonus_balances");

        Schema::dropIfExists("block_reasons");

        Schema::dropIfExists("block_reason_sales");

        Schema::dropIfExists("biometry_postbacks");

        Schema::dropIfExists("benefits");

        Schema::dropIfExists("astron_members_integrations");

        Schema::dropIfExists("asaas_transfers");

        Schema::dropIfExists("asaas_backoffice_requests");

        Schema::dropIfExists("asaas_anticipation_requests");

        Schema::dropIfExists("api_tokens");

        Schema::dropIfExists("antifrauds");

        Schema::dropIfExists("antifraud_warnings");

        Schema::dropIfExists("antifraud_sale_reviews");

        Schema::dropIfExists("antifraud_quiz_questions");

        Schema::dropIfExists("antifraud_postbacks");

        Schema::dropIfExists("antifraud_device_data");

        Schema::dropIfExists("anticipations");

        Schema::dropIfExists("anticipated_transactions");

        Schema::dropIfExists("affiliates");

        Schema::dropIfExists("affiliate_requests");

        Schema::dropIfExists("affiliate_links");

        Schema::dropIfExists("activity_log");

        Schema::dropIfExists("activecampaign_sent");

        Schema::dropIfExists("activecampaign_integrations");

        Schema::dropIfExists("activecampaign_events");

        Schema::dropIfExists("activecampaign_customs");

        Schema::dropIfExists("achievements");

        Schema::dropIfExists("achievement_user");
    }
};
