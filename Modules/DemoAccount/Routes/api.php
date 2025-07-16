<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        "prefix" => "demoaccount",
        "middleware" => "auth:api",
    ],
    function () {
        //core
        Route::group(["prefix" => "core"], function () {
            Route::get("verifydocuments", "CoreApiDemoController@verifyDocuments");
        });

        //discountCoupons
        Route::group([], function () {
            Route::get("/project/{projectId}/couponsdiscounts", "DiscountCouponsApiDemoController@index");
            Route::get("/project/{projectId}/couponsdiscounts/{id}", "DiscountCouponsApiDemoController@show");
            Route::get("/project/{projectId}/couponsdiscounts/{id}/edit", "DiscountCouponsApiDemoController@edit");
        });

        //ProjectReviews
        Route::group([], function () {
            Route::get("/projectreviews", "ProjectReviewsApiDemoController@index");
            Route::get("/projectreviews/{id}", "ProjectReviewsApiDemoController@show");
        });

        //ChekoutEditor
        Route::group([], function () {
            Route::get("/checkouteditor/{id}", "CheckoutEditorApiDemoController@show");
        });

        //Pixels
        Route::group([], function () {
            Route::get("/project/{projectId}/pixels", "PixelsApiDemoController@index");
            Route::get("/projects/{projectId}/pixels/configs", "PixelsApiDemoController@getPixelConfigs")->name(
                "pixels.getconfig"
            );
            Route::get("/project/{projectId}/pixels/{id}", "PixelsApiDemoController@show");
            Route::get("/project/{projectId}/pixels/{id}/edit", "PixelsApiDemoController@edit");
        });

        //ProjectNotification
        Route::group([], function () {
            Route::get("/project/{projectId}/projectnotification", "ProjectNotificationApiDemoController@index");
            Route::get("/project/{projectId}/projectnotification/{id}", "ProjectNotificationApiDemoController@show");
            Route::get(
                "/project/{projectId}/projectnotification/{id}/edit",
                "ProjectNotificationApiDemoController@edit"
            );
        });

        //Products
        Route::group([], function () {
            Route::post("/products/topselling", "ProductsApiDemoController@getTopSellingProducts")->name(
                "api.demo.products.topselling"
            );

            Route::get("/products", "ProductsApiDemoController@index");
            Route::get("/products/{id}", "ProductsApiDemoController@show");
            Route::get("/products/{id}/edit", "ProductsApiDemoController@edit");
            Route::get("/products/saleproducts/{saleId}", "ProductsApiDemoController@getProductBySale")->name(
                "api.demo.products.saleproducts"
            );

            Route::post("/products/products-variants", "ProductsApiDemoController@getProductsVariants")->name(
                "api.demo.products.productsvariants"
            );

            Route::get("/product/{id}", "ProductsApiDemoController@getProductById")->name("api.demo.products.getproduct");
        });

        //Sales
        Route::group([], function () {
            Route::get("/sales/filters", [
                "uses" => "SalesApiDemoController@filters",
            ]);
            Route::get("/sales/resume", [
                "as" => "sales.resume",
                "uses" => "SalesApiDemoController@resume",
            ]);
            Route::get("/sales/user-plans", "SalesApiDemoController@getPlans");

            Route::get("/sales", "SalesApiDemoController@index");
            Route::get("/sales/{id}", "SalesApiDemoController@show");
            Route::get("/sales/projects-with-sales", "SalesApiDemoController@getProjectsWithSales");
        });

        //ActiveCampaign
        Route::group([], function () {
            Route::get("apps/activecampaign", "ActiveCampaignApiDemoController@index");
            Route::get("apps/activecampaign/{id}", "ActiveCampaignApiDemoController@show");
            Route::get("apps/activecampaign/{id}/edit", "ActiveCampaignApiDemoController@edit");
            Route::get("apps/activecampaignevent", "ActiveCampaignEventApiDemoController@index");
            Route::get("apps/activecampaignevent/{id}", "ActiveCampaignEventApiDemoController@show");
            Route::get("apps/activecampaignevent/{id}/edit", "ActiveCampaignEventApiDemoController@edit");
        });

        //Affiliate
        Route::group([], function () {
            Route::get("/affiliates/getaffiliates", "AffiliatesApiDemoController@getAffiliates");
            Route::get("/affiliates/getaffiliaterequests", "AffiliatesApiDemoController@getAffiliateRequests");

            Route::get("/affiliates", "AffiliatesApiDemoController@index");
            Route::get("/affiliates/{id}", "AffiliatesApiDemoController@show");
            Route::get("/affiliates/{id}/edit", "AffiliatesApiDemoController@edit");

        });

        //Apps
        Route::group([], function () {
            Route::get("/apps", "AppsApiDemoController@index");
        });

        //AstronMemebers
        Route::group([], function () {
            Route::get("/apps/astronmembers", "AstronMembersApiDemoController@index");
            Route::get("/apps/astronmembers/{id}", "AstronMembersApiDemoController@show");
            Route::get("/apps/astronmembers/{id}/edit", "AstronMembersApiDemoController@edit");
        });

        //Chargebacks
        Route::group([], function () {
            Route::get(
                "/contestations/projects-with-contestations",
                "ContestationsApiDemoController@getProjectsWithContestations"
            );
            Route::get("/contestations/getcontestations", "ContestationsApiDemoController@getContestations")->name(
                "contestations.getchargebacks"
            );
            Route::get("/contestations/gettotalvalues", "ContestationsApiDemoController@getTotalValues")->name(
                "contestations.gettotalvalues"
            );
            Route::get(
                "/contestations/get-contestation-files/{salecontestation}",
                "ContestationsApiDemoController@getContestationFiles"
            )->name("contestations.getContestationFiles");
            Route::get("/contestations/{contestation_id}/contestation", "ContestationsApiDemoController@show")
                ->name("contestations.show")
                ->middleware("permission:contestations_manage");
        });

        //Checkout
        Route::group([], function () {
            Route::get("/checkout", "CheckoutApiDemoController@index");
            Route::get("/checkout/{id}", "CheckoutApiDemoController@show");
        });

        //ConvertaX
        Route::group([], function () {
            Route::get("/apps/convertax", "ConvertaXApiDemoController@index");
            Route::get("/apps/convertax/{id}", "ConvertaXApiDemoController@show");
        });

        //Customers
        Route::group([], function () {
            Route::get("/customers/{id}", "CustomersApiDemoController@show");
            Route::get("/customers/{id}/{sale_id}", "CustomersApiDemoController@show");
        });

        //Dashboard
        Route::group([], function () {
            Route::post("/dashboard/getvalues", "DashboardApiDemoController@getValues");
            Route::get("/dashboard/get-chart-data", "DashboardApiDemoController@getChartData");
            Route::get("/dashboard/get-performance", "DashboardApiDemoController@getPerformance");
            Route::get("/dashboard/get-account-health", "DashboardApiDemoController@getAccountHealth");
            Route::get("/dashboard/get-account-chargeback", "DashboardApiDemoController@getAccountChargeback");
            Route::get("/dashboard/get-account-attendance", "DashboardApiDemoController@getAccountAttendance");
            Route::get("/dashboard/get-account-tracking", "DashboardApiDemoController@getAccountTracking");
            Route::get("/dashboard/verify-achievements", "DashboardApiDemoController@getAchievements");
            Route::get("/dashboard/verify-pix-onboarding", "DashboardApiDemoController@verifyPixOnboarding");
            Route::get("/dashboard", "DashboardApiDemoController@index")->name("demo.dashboard");

            Route::get("/dashboard/verify-user-terms", "DashboardApiDemoController@getUserTerms");
            Route::put("/dashboard/update-user-terms", "DashboardApiDemoController@updateUserTerms");
        });

        //Domains
        Route::group([], function () {
            Route::get("/project/{projectId}/domains", "DomainsApiDemoController@index");
            Route::get("/project/{projectId}/domains/{domainId}", "DomainsApiDemoController@show");
        });

        //Deliveries
        Route::group([], function () {
            Route::get('/delivery/{$id}', "DeliveryApiDemoController@show");
        });

        //Finances
        Route::group([], function () {
            Route::get("/finances/getbalances", "FinancesApiDemoController@getBalances")->name("api.demo.finances.balances");
            Route::get("/finances/acquirers/{companyId?}", "FinancesApiDemoController@getAcquirers")->name(
                "api.demo.finances.acquirers"
            );
            Route::get("/finances/get-statement-resumes/", "FinancesApiDemoController@getStatementResume")->name(
                "demo.finances.statement-resumes"
            );

            Route::get("/old_finances/getbalances", "OldFinancesApiDemoController@getBalances")->name(
                "api.demo.old_finances.balances"
            );
        });

        //Hotbillet
        Route::group([], function () {
            Route::get("/apps/hotbillet", "HotBilletApiDemoController@index");
            Route::get("/apps/hotbillet/{id}", "HotBilletApiDemoController@show");
            Route::get("/apps/hotbillet/{id}/edit", "HotBilletApiDemoController@edit");
        });

        //HotZapp
        Route::group([], function () {
            Route::get("/apps/hotzapp", "HotZappApiDemoController@index");
            Route::get("/apps/hotzapp/{id}", "HotZappApiDemoController@show");
            Route::get("/apps/hotzapp/{id}/edit", "HotZappApiDemoController@edit");
        });

        //Integrations
        Route::group([], function () {
            Route::get("integrations", "IntegrationsApiDemoController@index");
            Route::get("integrations/{id}", "IntegrationsApiDemoController@show");
        });

        //Invites
        Route::group([], function () {
            Route::get("invitations", "InvitesApiDemoController@index");
            Route::get("/invitations/getinvitationdata", "InvitesApiDemoController@getInvitationData")->name(
                "api.getinvitationdata"
            );
        });

        //MelhorEnvio
        Route::group([], function () {
            Route::get("/apps/melhorenvio", "MelhorenvioApiDemoController@index");
        });

        //Notazz
        Route::group([], function () {
            Route::get("apps/notazz", "NotazzApiDemoController@index");
            Route::get("apps/notazz/{id}", "NotazzApiDemoController@show");
            Route::get("apps/notazz/{id}/edit", "NotazzApiDemoController@edit");
        });

        //Notificações Inteligentes
        Route::get("/apps/notificacoesinteligentes", "NotificacoesInteligentesApiDemoController@index");
        Route::get("/apps/notificacoesinteligentes/{id}", "NotificacoesInteligentesApiDemoController@show");
        Route::get("/apps/notificacoesinteligentes/{id}/edit", "NotificacoesInteligentesApiDemoController@edit");

        //OrderBump
        Route::group([], function () {
            Route::get("orderbump", "OrderBumpApiDemoController@index");
            Route::get("orderbump/{id}", "OrderBumpApiDemoController@show");
        });

        //Projects
        Route::group([], function () {
            Route::get("/projects", "ProjectsApiDemoController@index");
            Route::get("/projects/{id}", "ProjectsApiDemoController@show");
            Route::get("/projects/{id}/edit", "ProjectsApiDemoController@edit");
            Route::get("/projects/{id}/companie", "ProjectsApiDemoController@getCompanieByProject");
        });

        //Plans
        Route::group([], function () {
            Route::get("/project/{projectId}/plans", "PlansApiDemoController@index");
            Route::get("/project/{projectId}/plans/{planId}", "PlansApiDemoController@show");

            Route::get("/plans/user-plans", "PlansApiDemoController@getPlans");
        });

        //ProjectUpsellRule
        Route::group([], function () {
            Route::get("/projectupsellrule", "ProjectUpsellRuleApiDemoController@index");
            Route::get("/projectupsellrule/{id}", "ProjectUpsellRuleApiDemoController@show");
            Route::get("/projectupsellrule/{id}/edit", "ProjectUpsellRuleApiDemoController@edit");
        });

        Route::group([], function () {
            Route::get("/projectreviews/{id}/edit", "ProjectReviewsApiDemoController@edit");
        });

        //ProjectReviewsConfig
        Route::group([], function () {
            Route::get("/projectreviewsconfig", "ProjectReviewsConfigApiDemoController@index");
            Route::get("/projectreviewsconfig/{id}", "ProjectReviewsConfigApiDemoController@show");
            Route::get("/projectreviewsconfig/{id}/edit", "ProjectReviewsConfigApiDemoController@edit");
        });

        //ProjectUpsellConfig
        Route::group([], function () {
            Route::get("/projectupsellconfig", "ProjectUpsellConfigApiDemoController@index");
            Route::get("/projectupsellconfig/{id}", "ProjectUpsellConfigApiDemoController@show");
            Route::get("/projectupsellconfig/{id}/edit", "ProjectUpsellConfigApiDemoController@edit");
        });

        //Reportana
        Route::group([], function () {
            Route::get("apps/reportana", "ReportanaApiDemoController@index");
            Route::get("apps/reportana/{id}", "ReportanaApiDemoController@show");
            Route::get("apps/reportana/{id}/edit", "ReportanaApiDemoController@edit");
        });

        //Report
        Route::group([], function () {
            Route::get("reports", "ReportsApiDemoController@index");

            Route::get("reports/getsalesbyorigin", "ReportsApiDemoController@getSalesByOrigin");

            Route::get("reports/getcheckoutsbyorigin", "ReportsApiDemoController@getCheckoutsByOrigin");

            Route::get("/reports/projections", "ReportsApiDemoController@projections");

            Route::get("/reports/coupons", "ReportsApiDemoController@getDiscountCoupons");

            Route::get("/reports/pending-balance", "ReportsApiDemoController@pendingBalance");

            Route::get("/reports/resume-pending-balance", "ReportsApiDemoController@resumePendingBalance");

            Route::get("/reports/blockedbalance", "ReportsApiDemoController@blockedbalance");

            Route::get("/reports/blockedresume", "ReportsApiDemoController@resumeBlockedBalance");

            Route::get("/reports/blocked-balance", "ReportsApiDemoController@blockedBalance");
            Route::get("/reports/resume-blocked-balance", "ReportsApiDemoController@resumeblockedBalance");
            Route::get("/reports/block-reasons", "ReportsApiDemoController@getBlockReasons");

            Route::get("/reports/resume/commissions", "ReportsFinanceApiDemoController@getResumeCommissions");
            Route::get("/reports/resume/pendings", "ReportsFinanceApiDemoController@getResumePendings");
            Route::get("/reports/resume/cashbacks", "ReportsFinanceApiDemoController@getResumeCashbacks");
            Route::get("/reports/resume/sales", "ReportsSaleApiDemoController@getResumeSales");
            Route::get("/reports/resume/type-payments", "ReportsSaleApiDemoController@getResumeTypePayments");
            Route::get("/reports/resume/products", "ReportsSaleApiDemoController@getResumeProducts");
            Route::get("/reports/resume/coupons", "ReportsMarketingApiDemoController@getResumeCoupons");
            Route::get("/reports/resume/regions", "ReportsMarketingApiDemoController@getResumeRegions");
            Route::get("/reports/resume/origins", "ReportsMarketingApiDemoController@getResumeOrigins");

            Route::get("/reports/finances/resume", "ReportsFinanceApiDemoController@getFinancesResume");
            Route::get("/reports/finances/cashbacks", "ReportsFinanceApiDemoController@getFinancesCashbacks");
            Route::get("/reports/finances/pendings", "ReportsFinanceApiDemoController@getFinancesPendings");
            Route::get("/reports/finances/blockeds", "ReportsFinanceApiDemoController@getFinancesBlockeds");
            Route::get("/reports/finances/distribuitions", "ReportsFinanceApiDemoController@getFinancesDistribuitions");
            Route::get("/reports/finances/withdrawals", "ReportsFinanceApiDemoController@getFinancesWithdrawals");

            Route::get("/reports/sales/resume", "ReportsSaleApiDemoController@getSalesResume");
            Route::get("/reports/sales/distribuitions", "ReportsSaleApiDemoController@getSalesDistribuitions");
            Route::get("/reports/sales/abandoned-carts", "ReportsSaleApiDemoController@getAbandonedCarts");
            Route::get("/reports/sales/orderbump", "ReportsSaleApiDemoController@getOrderBump");
            Route::get("/reports/sales/upsell", "ReportsSaleApiDemoController@getUpsell");
            Route::get("/reports/sales/conversion", "ReportsSaleApiDemoController@getConversion");
            Route::get("/reports/sales/recurrence", "ReportsSaleApiDemoController@getRecurrence");

            Route::get("/reports/marketing/resume", "ReportsMarketingApiDemoController@getResume");
            Route::get("/reports/marketing/sales-by-state", "ReportsMarketingApiDemoController@getSalesByState");
            Route::get(
                "/reports/marketing/most-frequent-sales",
                "ReportsMarketingApiDemoController@getMostFrequentSales"
            );
            Route::get("/reports/marketing/devices", "ReportsMarketingApiDemoController@getDevices");
            Route::get(
                "/reports/marketing/operational-systems",
                "ReportsMarketingApiDemoController@getOperationalSystems"
            );
            Route::get("/reports/marketing/state-details", "ReportsMarketingApiDemoController@getStateDetail");
            Route::get("/reports/marketing/coupons", "ReportsMarketingApiDemoController@getResumeCoupons");
            Route::get("/reports/marketing/regions", "ReportsMarketingApiDemoController@getResumeRegions");
            Route::get("/reports/marketing/origins", "ReportsMarketingApiDemoController@getResumeOrigins");

            Route::get(
                "/reports/projects-with-blocked-balance",
                "ReportsApiDemoController@getProjectsWithBlockedBalance"
            );
            Route::get("/reports/projects-with-checkouts", "ReportsApiDemoController@getProjectsWithCheckouts");
            Route::get("/reports/projects-with-coupons", "ReportsApiDemoController@getProjectsWithCoupons");
            Route::get(
                "/reports/projects-with-pending-balance",
                "ReportsApiDemoController@getProjectsWithPendingBalance"
            );
        });

        //SalesRecovery
        Route::group([], function () {
            Route::get("/recovery", "SalesRecoveryApiDemoController@index");

            Route::get("recovery/getrecoverydata", "SalesRecoveryApiDemoController@getRecoveryData");
            Route::get("checkout/getrecoverydata", "SalesRecoveryApiDemoController@getRecoveryData");

            Route::get("recovery/getabandonedcart", "SalesRecoveryApiDemoController@getAbandonedCart");
            Route::get("recovery/getrefusedcart", "SalesRecoveryApiDemoController@getCartRefused");
            Route::get("recovery/getboleto", "SalesRecoveryApiDemoController@getBoletoOverdue");
            Route::get("recovery/get-pix", "SalesRecoveryApiDemoController@getPixOverdue");

            Route::post("recovery/details", "SalesRecoveryApiDemoController@getDetails");

            Route::get("/recovery/projects-with-recovery", "SalesRecoveryApiDemoController@getProjectsWithRecovery");
        });

        //Shippings
        Route::group([], function () {
            Route::get("/project/{projectId}/shippings", "ShippingApiDemoController@index");
            Route::get("/project/{projectId}/shippings/{id}", "ShippingApiDemoController@show");
        });

        //Shopify
        Route::group([], function () {
            Route::get("/apps/shopify", "ShopifyApiDemoController@index");
        });

        //Smarthfunnel
        Route::group([], function () {
            Route::get("apps/smartfunnel", "SmartfunnelApiDemoController@index");
            Route::get("apps/smartfunnel/{id}", "SmartfunnelApiDemoController@show");
            Route::get("apps/smartfunnel/{id}/edit", "SmartfunnelApiDemoController@edit");
        });

        //Tickets
        Route::group([], function () {
            Route::get("tickets/getvalues", "TicketsApiDemoController@getTotalValues")->name("api.tickets.getvalues");
            Route::get("/tickets", "TicketsApiDemoController@index");
            Route::get("/tickets/{id}", "TicketsApiDemoController@show");
        });

        //Trackings
        Route::group([], function () {
            Route::get("/tracking/resume", "TrackingsApiDemoController@resume");
            Route::get("/tracking/blockedbalance", "TrackingsApiDemoController@getBlockedBalance");

            Route::get("/tracking", "TrackingsApiDemoController@index");
            Route::get("/tracking/{id}", "TrackingsApiDemoController@show");
        });

        //Transfers
        Route::group([], function () {
            Route::get("/transfers", "TransfersApiDemoController@index");
            Route::get("/transfers/account-statement-data", "TransfersApiDemoController@accountStatementData");
        });

        //Unicodrops
        Route::group([], function () {
            Route::get("apps/unicodrop", "UnicodropApiDemoController@index");
            Route::get("apps/unicodrop/{id}", "UnicodropApiDemoController@show");
            Route::get("apps/unicodrop/{id}/edit", "UnicodropApiDemoController@edit");
        });

        //Whatsapp2
        Route::group([], function () {
            Route::get("apps/whatsapp2", "Whatsapp2ApiDemoController@index");
            Route::get("apps/whatsapp2/{id}", "Whatsapp2ApiDemoController@show");
            Route::get("apps/whatsapp2/{id}/edit", "Whatsapp2ApiDemoController@edit");
        });

        //Withdrawals
        Route::group([], function () {
            Route::get("/withdrawals", "WithdrawalsApiDemoController@index");
            Route::post("/withdrawals/getaccountinformation", "WithdrawalsApiDemoController@getAccountInformation");

            Route::post("/withdrawals/getWithdrawalValues", "WithdrawalsApiDemoController@getWithdrawalValues");
            Route::get("/withdrawals/checkallowed", "WithdrawalsApiDemoController@checkAllowed");

            Route::get(
                "/withdrawals/get-transactions-by-brand/{withdrawal_id}",
                "WithdrawalsApiDemoController@getTransactionsByBrand"
            );
            Route::post(
                "/withdrawals/get-transactions/{withdrawal_id}",
                "WithdrawalsApiDemoController@getTransactions"
            );
            Route::get("/withdrawals/get-resume/", "WithdrawalsApiDemoController@getResume");

            Route::get("/withdrawals/settings", "WithdrawalsSettingsApiDemoController@index");
            Route::get("/withdrawals/settings/{settingsId}", "WithdrawalsSettingsApiDemoController@show");
            Route::get("/withdrawals/settings/{companyId}/{settingsId}", "WithdrawalsSettingsApiDemoController@show");
        });

        //WooCommerce
        Route::group([], function () {
            Route::get("/apps/woocommerce", "WooCommerceApiDemoController@index");
            Route::post("/apps/woocommerce/keys/get", [
                "uses" => "WooCommerceApiDemoController@keysGet",
                "as" => "woocommerce.keys.get",
            ]);
        });

        Route::group([], function () {
            Route::get("/mobile/balances", "DashboardApiDemoController@getValues");
            Route::get("/mobile/sales", "MobileDemoController@sales");
            Route::get("/mobile/withdrawals", "WithdrawalsApiDemoController@index");
            Route::get("/mobile/statements-resume", "MobileDemoController@statementsResume");
        });

        Route::get("/not-authorized", "DemoAccountController@notAuthorized")->name("demo.not_authorized");
    }
);
