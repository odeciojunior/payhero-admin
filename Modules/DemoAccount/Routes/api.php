<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'prefix'=>'demoaccount',
    'middleware'=>'auth:api'
],function(){

    //core
    Route::group(['prefix'=>'core'],function(){
        Route::get('verifydocuments', 'CoreApiDemoController@verifyDocuments');
    });

    //discountCoupons
    Route::group([],function(){
        Route::get('/project/{projectId}/couponsdiscounts', 'DiscountCouponsApiDemoController@index');
        Route::get('/project/{projectId}/couponsdiscounts/{id}', 'DiscountCouponsApiDemoController@show');
    });

    //ProjectReviews
    Route::group([],function(){
        Route::get('/projectreviews', 'ProjectReviewsApiDemoController@index');
        Route::get('/projectreviews/{id}', 'ProjectReviewsApiDemoController@show');
    });

    //ChekoutEditor
    Route::group([],function(){
        Route::apiResource('checkouteditor', 'CheckoutEditorApiDemoController')->only('show');
    });

    //Pixels
    Route::group([],function(){
        Route::get("/projects/{projectId}/pixels/configs", 'PixelsApiDemoController@getPixelConfigs')->name('pixels.getconfig');        
        Route::get('/project/{projectId}/pixels', 'PixelsApiDemoController@index');
        Route::get('/project/{projectId}/pixels/{id}', 'PixelsApiDemoController@show');
    });

    //ProjectNotification
    Route::group([],function(){
        Route::get('/project/{projectId}/projectnotification', 'ProjectNotificationApiDemoController@index');
        Route::get('/project/{projectId}/projectnotification/{id}', 'ProjectNotificationApiDemoController@show');
    });

    //Products
    Route::group([],function(){
        Route::get('/products', 'ProductsApiDemoController@index');
        Route::get('/products/{id}', 'ProductsApiDemoController@show');
        Route::get('/products/{id}/edit', 'ProductsApiDemoController@edit');
    });

    //Sales
    Route::group([],function(){
        Route::get('/filters', [
            'uses' => 'SalesApiDemoController@filters',
        ]);
        Route::get('/resume', [
            'as'   => 'sales.resume',
            'uses' => 'SalesApiDemoController@resume',
        ]);
        
        Route::get('/sales', 'SalesApiDemoController@index');
        Route::get('/sales/{id}', 'SalesApiDemoController@show');
    });

    //ActiveCampaign
    Route::group([],function(){
        Route::get('apps/activecampaign', 'ActiveCampaignApiDemoController@index');
        Route::get('apps/activecampaign/{id}', 'ActiveCampaignApiDemoController@show');
        Route::get('apps/activecampaign/{id}/edit', 'ActiveCampaignApiDemoController@edit');
        Route::get('apps/activecampaignevent', 'ActiveCampaignEventApiDemoController@index');
        Route::get('apps/activecampaignevent/{id}', 'ActiveCampaignEventApiDemoController@show');
        Route::get('apps/activecampaignevent/{id}/edit', 'ActiveCampaignEventApiDemoController@edit');
    });

    //Affiliate
    Route::group([],function(){
        Route::get('/affiliates/getaffiliates', 'AffiliatesApiDemoController@getAffiliates');
        Route::get('/affiliates/getaffiliaterequests', 'AffiliatesApiDemoController@getAffiliateRequests');

        Route::get('/affiliates', 'AffiliatesApiDemoController@index');
        Route::get('/affiliates/{id}', 'AffiliatesApiDemoController@show');
        Route::get('/affiliates/{id}/edit', 'AffiliatesApiDemoController@edit');

        Route::get('/affiliatelinks', 'AffiliateLinksApiDemoController@index');
        Route::get('/affiliatelinks/{id}', 'AffiliateLinksApiDemoController@show');
        Route::get('/affiliatelinks/{id}/edit', 'AffiliateLinksApiDemoController@edit');
    });

    //Apps
    Route::group([],function(){
        Route::apiResource('apps', 'AppsApiController')->only('index');
    });

    //AstronMemebers
    Route::group([],function(){
        Route::get('/apps/astronmembers', 'AstronMembersApiDemoController@index');
        Route::get('/apps/astronmembers/{id}', 'AstronMembersApiDemoController@show');
        Route::get('/apps/astronmembers/{id}/edit', 'AstronMembersApiDemoController@edit');
    });

    //Chargebacks
    Route::group([],function(){
        Route::get('/getcontestations', 'ContestationsApiDemoController@getContestations')->name('contestations.getchargebacks');
        Route::get('/gettotalvalues', 'ContestationsApiDemoController@getTotalValues')->name('contestations.gettotalvalues');
        Route::get('/get-contestation-files/{salecontestation}', 'ContestationsApiDemoController@getContestationFiles')
            ->name('contestations.getContestationFiles');
    });

    //Checkout
    Route::group([],function(){
        Route::apiResource('checkout', 'CheckoutApiDemoController')->only('index', 'show')->names('api.checkout');
    });

    //ConvertaX
    Route::group([],function(){
        Route::get('/apps/convertax', 'ConvertaXApiDemoController@index');
        Route::get('/apps/convertax/{id}', 'ConvertaXApiDemoController@show');
    });

    //Customers
    Route::group([],function(){
        Route::get('/customers/{id}','CustomersApiDemoController@show');
        Route::get('/customers/{id}/{sale_id}', 'CustomersApiDemoController@show');
    });

    //Dashboard
    Route::group([],function(){
        Route::post('/dashboard/getvalues', 'DashboardApiDemoController@getValues');
        Route::get('/dashboard/get-chart-data', 'DashboardApiDemoController@getChartData');
        Route::get('/dashboard/get-performance', 'DashboardApiDemoController@getPerformance');
        Route::get('/dashboard/get-account-health', 'DashboardApiDemoController@getAccountHealth');
        Route::get('/dashboard/verify-achievements', 'DashboardApiDemoController@getAchievements');
        Route::get('/dashboard/verify-pix-onboarding', 'DashboardApiDemoController@verifyPixOnboarding');

        Route::get('/dashboard', 'DashboardApiDemoController@index')->name('demo.dashboard');
    });

    //Domains
    Route::group([],function(){   
        Route::get('/project/{projectId}/domains', 'DomainsApiDemoController@index');        
        Route::get('/project/{projectId}/domains/{domainId}', 'DomainsApiDemoController@show');
    });

    //Deliveries
    Route::group([],function(){
        Route::apiResource('/delivery', 'DeliveryApiDemoController')->only('show')->names('api.client');
    });

    //Finances
    Route::group([],function(){
        Route::get('/finances/getbalances', 'FinancesApiDemoController@getBalances')->name('api.finances.balances');
        Route::get('/finances/acquirers/{companyId?}', 'FinancesApiDemoController@getAcquirers')->name('api.finances.acquirers');
        Route::get('/finances/get-statement-resumes/', 'FinancesApiDemoController@getStatementResume')->name('finances.statement-resumes');

        Route::get('/old_finances/getbalances', 'OldFinancesApiDemoController@getBalances')->name('api.finances.balances');
    });

    //Hotbillet
    Route::group([],function(){
        Route::get('/apps/hotbillet', 'HotBilletApiDemoController@index');
        Route::get('/apps/hotbillet/{id}', 'HotBilletApiDemoController@show');
        Route::get('/apps/hotbillet/{id}/edit', 'HotBilletApiDemoController@edit');
    });

    //HotZapp
    Route::group([],function(){
        Route::get('/apps/hotzapp', 'HotZappApiDemoController@index');
        Route::get('/apps/hotzapp/{id}', 'HotZappApiDemoController@show');
        Route::get('/apps/hotzapp/{id}/edit', 'HotZappApiDemoController@edit');
    });

    //Integrations
    Route::group([],function(){
        Route::get('integrations', 'IntegrationsApiDemoController@index');
        Route::get('integrations/{id}', 'IntegrationsApiDemoController@show');
    });

    //Invites
    Route::group([],function(){
        Route::get('invitations', 'InvitesApiDemoController@index');
        Route::get('/invitations/getinvitationdata', 'InvitesApiDemoController@getInvitationData')
             ->name('api.getinvitationdata');
    });

    //MelhorEnvio
    Route::group([],function(){
        Route::get('/apps/melhorenvio', 'MelhorenvioApiDemoController@index');
    });

    //Notazz
    Route::group([],function(){
        Route::get('apps/notazz', 'NotazzApiDemoController@index');
        Route::get('apps/notazz/{id}', 'NotazzApiDemoController@show');
        Route::get('apps/notazz/{id}/edit', 'NotazzApiDemoController@edit');
    });

    //OrderBump
    Route::group([],function(){
        Route::get('orderbump', 'OrderBumpApiDemoController@index');
        Route::get('orderbump/{id}', 'OrderBumpApiDemoController@show');
    });

    //Projects
    Route::group([],function(){        
        Route::get('/projects', 'ProjectsApiDemoController@index');
        Route::get('/projects/{id}', 'ProjectsApiDemoController@show');
        Route::get('/projects/{id}/edit', 'ProjectsApiDemoController@edit');
    });

    //Plans
    Route::group([],function(){
        Route::get('/project/{projectId}/plans', 'PlansApiDemoController@index');
    });

    //ProjectUpsellRule
    Route::group([],function(){
        Route::get('/projectupsellrule', 'ProjectUpsellRuleApiDemoController@index');
        Route::get('/projectupsellrule/{id}', 'ProjectUpsellRuleApiDemoController@show');
    });

    //ProjectReviewsConfig
    Route::group([],function(){
        Route::get('/projectreviewsconfig', 'ProjectReviewsConfigApiDemoController@index');
        Route::get('/projectreviewsconfig/{id}', 'ProjectReviewsConfigApiDemoController@show');
        Route::get('/projectreviewsconfig/{id}/edit', 'ProjectReviewsConfigApiDemoController@edit');
    });

    //ProjectUpsellConfig
    Route::group([],function(){
        Route::get('/projectupsellconfig', 'ProjectUpsellConfigApiDemoController@index');
        Route::get('/projectupsellconfig/{id}', 'ProjectUpsellConfigApiDemoController@show');
        Route::get('/projectupsellconfig/{id}/edit', 'ProjectUpsellConfigApiDemoController@edit');
    });

    //Reportana
    Route::group([],function(){
        Route::get('apps/reportana', 'ReportanaApiDemoController@index');
        Route::get('apps/reportana/{id}', 'ReportanaApiDemoController@show');
        Route::get('apps/reportana/{id}/edit', 'ReportanaApiDemoController@edit');
    });

    //Report
    Route::group([],function(){
        Route::get('reports', 'ReportsApiDemoController@index');
        Route::get('reports/getsalesbyorigin', 'ReportsApiDemoController@getSalesByOrigin');

        Route::get('/reports/checkouts', 'ReportsApiDemoController@checkouts');
        Route::get('reports/getcheckoutsbyorigin', 'ReportsApiDemoController@getCheckoutsByOrigin');

        Route::get('/reports/projections', 'ReportsApiDemoController@projections');

        Route::get('/reports/coupons', 'ReportsApiDemoController@coupons');

        Route::get('/reports/pending-balance', 'ReportsApiDemoController@pendingBalance');

        Route::get('/reports/resume-pending-balance', 'ReportsApiDemoController@resumePendingBalance');

        Route::get('/reports/blockedbalance', 'ReportsApiDemoController@blockedbalance');

        Route::get('/reports/blockedresume', 'ReportsApiDemoController@resumeBlockedBalance');
    });        

    //SalesRecovery
    Route::group([],function(){
        Route::apiResource('recovery', 'SalesRecoveryApiDemoController')->only('index')->names('api.recovery');

        Route::get('recovery/getrecoverydata', 'SalesRecoveryApiDemoController@getRecoveryData');
        Route::get('checkout/getrecoverydata', 'SalesRecoveryApiDemoController@getRecoveryData');

        Route::get('recovery/getabandonedcart', 'SalesRecoveryApiDemoController@getAbandonedCart');
        Route::get('recovery/getrefusedcart', 'SalesRecoveryApiDemoController@getCartRefused');
        Route::get('recovery/getboleto', 'SalesRecoveryApiDemoController@getBoletoOverdue');
        Route::get('recovery/get-pix', 'SalesRecoveryApiDemoController@getPixOverdue');

        Route::post('recovery/details', 'SalesRecoveryApiDemoController@getDetails');
    });

    //Shippings
    Route::group([],function(){
        Route::get('/project/{projectId}/shippings', 'ShippingApiDemoController@index');
        Route::get('/project/{projectId}/shippings/{id}', 'ShippingApiDemoController@show');
    });

    //Shopify
    Route::group([],function(){
        Route::get('/apps/shopify', 'ShopifyApiDemoController@index');
    });

    //Smarthfunnel
    Route::group([],function(){
        Route::get('apps/smartfunnel', 'SmartfunnelApiDemoController@index');
        Route::get('apps/smartfunnel/{id}', 'SmartfunnelApiDemoController@show');
        Route::get('apps/smartfunnel/{id}/edit', 'SmartfunnelApiDemoController@edit');
    });
        
    //Tickets
    Route::group([],function(){
        Route::get('tickets/getvalues', 'TicketsApiDemoController@getTotalValues')->name('api.tickets.getvalues');    
        Route::apiResource('tickets', 'TicketsApiDemoController')->only('index', 'show')->names('api.tickets');
    });

    //Trackings
    Route::group([],function(){
        Route::get('/tracking/resume', 'TrackingsApiDemoController@resume');
        Route::get('/tracking/blockedbalance', 'TrackingsApiDemoController@getBlockedBalance');
        
        Route::get('/tracking', 'TrackingsApiDemoController@index');
        Route::get('/tracking/{id}', 'TrackingsApiDemoController@show');        
    });

    //Transfers
    Route::group([],function(){
        Route::get('/transfers', 'TransfersApiDemoController@index');
        Route::get('/transfers/account-statement-data', 'TransfersApiDemoController@accountStatementData');
    });

    //Unicodrops
    Route::group([],function(){
        Route::apiResource('apps/unicodrop', 'UnicodropApiDemoController')
        ->only('index', 'edit','show');
    });

    //Whatsapp2
    Route::group([],function(){
        Route::apiResource('apps/whatsapp2', 'Whatsapp2ApiDemoController')
        ->only('index', 'edit','show');
    });

    //Withdrawals
    Route::group([],function(){
        Route::get('/withdrawals', 'WithdrawalsApiDemoController@index');        
        Route::post('/withdrawals/getaccountinformation', 'WithdrawalsApiDemoController@getAccountInformation');

        Route::post('/withdrawals/getWithdrawalValues', 'WithdrawalsApiDemoController@getWithdrawalValues');
        Route::get('/withdrawals/checkallowed', 'WithdrawalsApiDemoController@checkAllowed');

        Route::get('/withdrawals/get-transactions-by-brand/{withdrawal_id}', 'WithdrawalsApiDemoController@getTransactionsByBrand');
        Route::post('/withdrawals/get-transactions/{withdrawal_id}', 'WithdrawalsApiDemoController@getTransactions');
        Route::get('/withdrawals/get-resume/', 'WithdrawalsApiDemoController@getResume');

        Route::get('/withdrawals/settings', 'WithdrawalsSettingsApiDemoController@index');
        Route::get('/withdrawals/settings/{settingsId}', 'WithdrawalsSettingsApiDemoController@show');
        Route::get('/withdrawals/settings/{companyId}/{settingsId}', 'WithdrawalsSettingsApiDemoController@show');
    });

    //WooCommerce
    Route::group([],function(){
        Route::get('/apps/woocommerce', 'WooCommerceApiDemoController@index');
        Route::post('/apps/woocommerce/keys/get', [
            'uses' => 'WooCommerceApiDemoController@keysGet',
            'as'   => 'woocommerce.keys.get',
        ]);
    });

    Route::get('/not-authorized','DemoAccountController@notAuthorized')->name('demo.not_authorized');
});