let mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

// ActiveCampaign
mix.babel(['Modules/ActiveCampaign/Resources/assets/js/edit.js',], 'public/modules/activecampaign/js/edit.min.js');
mix.babel(['Modules/ActiveCampaign/Resources/assets/js/events.js',], 'public/modules/activecampaign/js/events.min.js');
mix.babel(['Modules/ActiveCampaign/Resources/assets/js/index.js',], 'public/modules/activecampaign/js/index.min.js');

// Affiliates
mix.babel(['Modules/Affiliates/Resources/assets/js/index.js',], 'public/modules/affiliates/js/index.min.js');
mix.babel(['Modules/Affiliates/Resources/assets/js/links.js',], 'public/modules/affiliates/js/links.min.js');
mix.babel(['Modules/Affiliates/Resources/assets/js/projectaffiliates.js',], 'public/modules/affiliates/js/projectaffiliates.min.js');

// Apps
mix.copy('Modules/Apps/Resources/assets/imgs', 'public/modules/apps/imgs');
mix.styles(['Modules/Apps/Resources/assets/css/index.css',], 'public/modules/apps/css/index.min.css');
mix.babel(['Modules/Apps/Resources/assets/js/index.js',], 'public/modules/apps/js/index.min.js');

// AstronMembers
// USADO AONDE ??? mix.styles(['Modules/AstronMembers/Resources/assets/css/index.css',], 'public/modules/astronmembers/css/index.min.css');
mix.babel(['Modules/AstronMembers/Resources/assets/js/index.js',], 'public/modules/astronmembers/js/index.min.js');

// Attendance
mix.styles(['Modules/Attendance/Resources/assets/css/index.css',], 'public/modules/attendance/css/index.min.css');
// USADO AONDE ??? mix.babel(['Modules/Attendance/Resources/assets/js/index.js',], 'public/modules/attendance/js/index.min.js');
// USADO AONDE ??? mix.babel(['Modules/Attendance/Resources/assets/js/show.js',], 'public/modules/attendance/js/show.min.js');

// Chargebacks
mix.copy('Modules/Chargebacks/Resources/assets/svg', 'public/modules/chargebacks/svg');
mix.styles(['Modules/Chargebacks/Resources/assets/css/contestations-index.css',], 'public/modules/chargebacks/css/contestations-index.min.css');
mix.babel(['Modules/Chargebacks/Resources/assets/js/contestations-detail.js',], 'public/modules/chargebacks/js/contestations-detail.min.js');
mix.babel(['Modules/Chargebacks/Resources/assets/js/contestations-index.js',], 'public/modules/chargebacks/js/contestations-index.min.js');
// USADO AONDE ??? mix.babel(['Modules/Chargebacks/Resources/assets/js/index.js',], 'public/modules/chargebacks/js/index.min.js');
// USADO AONDE ??? mix.babel(['Modules/Chargebacks/Resources/assets/js/old-index.js',], 'public/modules/chargebacks/js/old-index.min.js');

// CheckoutEditor
mix.copy('Modules/CheckoutEditor/Resources/assets/img', 'public/modules/checkouteditor/img');
mix.copy('Modules/CheckoutEditor/Resources/assets/files', 'public/modules/checkouteditor/files');
mix.styles(['Modules/CheckoutEditor/Resources/assets/css/cropper.css',], 'public/modules/checkouteditor/css/cropper.min.css');
// ??? mix.styles(['Modules/CheckoutEditor/Resources/assets/css/cropper.min.css',], 'public/modules/checkouteditor/css/cropper.min.css');
mix.styles(['Modules/CheckoutEditor/Resources/assets/css/custom-inputs.css',], 'public/modules/checkouteditor/css/custom-inputs.min.css');
mix.styles(['Modules/CheckoutEditor/Resources/assets/css/dropfy.css',], 'public/modules/checkouteditor/css/dropfy.min.css');
mix.styles(['Modules/CheckoutEditor/Resources/assets/css/preview-styles.css',], 'public/modules/checkouteditor/css/preview-styles.min.css');
mix.styles(['Modules/CheckoutEditor/Resources/assets/css/quill.snow.css',], 'public/modules/checkouteditor/css/quill.snow.min.css');
mix.styles(['Modules/CheckoutEditor/Resources/assets/css/style.css',], 'public/modules/checkouteditor/css/style.min.css');
mix.babel(['Modules/CheckoutEditor/Resources/assets/js/checkoutEditor.js',], 'public/modules/checkouteditor/js/checkoutEditor.min.js');
mix.babel(['Modules/CheckoutEditor/Resources/assets/js/cropper.js',], 'public/modules/checkouteditor/js/cropper.min.js');
// ??? mix.babel(['Modules/CheckoutEditor/Resources/assets/js/cropper.min.js',], 'public/modules/checkouteditor/js/cropper.min.js');
mix.babel(['Modules/CheckoutEditor/Resources/assets/js/loadCheckoutData.js',], 'public/modules/checkouteditor/js/loadCheckoutData.min.js');
mix.babel(['Modules/CheckoutEditor/Resources/assets/js/quill.js',], 'public/modules/checkouteditor/js/quill.min.js');
// ??? mix.babel(['Modules/CheckoutEditor/Resources/assets/js/quill.min.js',], 'public/modules/checkouteditor/js/quill.min.js');
mix.babel(['Modules/CheckoutEditor/Resources/assets/js/scrollPreview.js',], 'public/modules/checkouteditor/js/scrollPreview.min.js');
mix.babel(['Modules/CheckoutEditor/Resources/assets/js/verifyPhone.js',], 'public/modules/checkouteditor/js/verifyPhone.min.js');

// Companies
mix.babel(['Modules/Companies/Resources/assets/js/create.js',], 'public/modules/companies/js/create.min.js');
mix.babel(['Modules/Companies/Resources/assets/js/edit.js',], 'public/modules/companies/js/edit.min.js');
mix.babel(['Modules/Companies/Resources/assets/js/edit_cnpj.js',], 'public/modules/companies/js/edit_cnpj.min.js');
mix.babel(['Modules/Companies/Resources/assets/js/edit_cpf.js',], 'public/modules/companies/js/edit_cpf.min.js');
mix.babel(['Modules/Companies/Resources/assets/js/index.js',], 'public/modules/companies/js/index.min.js');

// ConvertaX
mix.styles(['Modules/ConvertaX/Resources/assets/css/index.css',], 'public/modules/convertax/css/index.min.css');
mix.babel(['Modules/ConvertaX/Resources/assets/js/index.js',], 'public/modules/convertax/js/index.min.js');

// Dashboard
mix.styles(['Modules/Dashboard/Resources/assets/css/achievement-details.css',], 'public/modules/dashboard/css/achievement-details.min.css');
mix.styles(['Modules/Dashboard/Resources/assets/css/dashboard-account-health.css',], 'public/modules/dashboard/css/dashboard-account-health.min.css');
mix.styles(['Modules/Dashboard/Resources/assets/css/dashboard-performance.css',], 'public/modules/dashboard/css/dashboard-performance.min.css');
mix.styles(['Modules/Dashboard/Resources/assets/css/index.css',], 'public/modules/dashboard/css/index.min.css');
mix.styles(['Modules/Dashboard/Resources/assets/css/onboarding-details.css',], 'public/modules/dashboard/css/onboarding-details.min.css');
mix.styles(['Modules/Dashboard/Resources/assets/css/pix.css',], 'public/modules/dashboard/css/pix.min.css');
// USADO AONDE ??? mix.babel(['Modules/Dashboard/Resources/assets/js/chartist.min.js',], 'public/modules/dashboard/js/chartist.min.js');
// USADO AONDE ??? mix.babel(['Modules/Dashboard/Resources/assets/js/chartist-plugin-legend.min.js',], 'public/modules/dashboard/js/chartist-plugin-legend.min.js');
// USADO AONDE ??? mix.babel(['Modules/Dashboard/Resources/assets/js/chartist-plugin-tooltip.min.js',], 'public/modules/dashboard/js/chartist-plugin-tooltip.min.js');
mix.babel(['Modules/Dashboard/Resources/assets/js/dashboard.js',], 'public/modules/dashboard/js/dashboard.min.js');
mix.babel(['Modules/Dashboard/Resources/assets/js/dashboard-account-health.js',], 'public/modules/dashboard/js/dashboard-account-health.min.js');
mix.babel(['Modules/Dashboard/Resources/assets/js/dashboard-performance.js',], 'public/modules/dashboard/js/dashboard-performance.min.js');
mix.babel(['Modules/Dashboard/Resources/assets/js/gauge.js',], 'public/modules/dashboard/js/gauge.min.js');

// DiscountCoupons
mix.babel(['Modules/DiscountCoupons/Resources/assets/js/discountCoupons.js',], 'public/modules/discountcoupons/js/discountCoupons.min.js');

// Domains
// USADO AONDE ??? mix.babel(['Modules/Domains/Resources/assets/js/domain.js',], 'public/modules/domains/js/domain.min.js');
mix.babel(['Modules/Domains/Resources/assets/js/domainEdit.js',], 'public/modules/domains/js/domainEdit.min.js');

// Finances
mix.styles(['Modules/Finances/Resources/assets/css/jPages.css',], 'public/modules/finances/css/jPages.min.css');
mix.styles(['Modules/Finances/Resources/assets/css/multi-finances.css',], 'public/modules/finances/css/multi-finances.min.css');
mix.styles(['Modules/Finances/Resources/assets/css/new-finances.css',], 'public/modules/finances/css/new-finances.min.css');
mix.babel(['Modules/Finances/Resources/assets/js/balances.js',], 'public/modules/finances/js/balances.min.js');
mix.babel(['Modules/Finances/Resources/assets/js/detail.js',], 'public/modules/finances/js/detail.min.js');
mix.babel(['Modules/Finances/Resources/assets/js/jPages.min.js',], 'public/modules/finances/js/jPages.min.js'); // jPages
mix.babel(['Modules/Finances/Resources/assets/js/multi-finances.js',], 'public/modules/finances/js/multi-finances.min.js');
mix.babel(['Modules/Finances/Resources/assets/js/multi-finances-withdrawals.js',], 'public/modules/finances/js/multi-finances-withdrawals.min.js');
mix.babel(['Modules/Finances/Resources/assets/js/settings.js',], 'public/modules/finances/js/settings.min.js');
mix.babel(['Modules/Finances/Resources/assets/js/statement.js',], 'public/modules/finances/js/statement.min.js');
mix.babel(['Modules/Finances/Resources/assets/js/statement-index.js',], 'public/modules/finances/js/statement-index.min.js');
mix.babel(['Modules/Finances/Resources/assets/js/withdrawal-custom.js',], 'public/modules/finances/js/withdrawal-custom.min.js');
mix.babel(['Modules/Finances/Resources/assets/js/withdrawal-default.js',], 'public/modules/finances/js/withdrawal-default.min.js');
mix.babel(['Modules/Finances/Resources/assets/js/withdrawal-handler.js',], 'public/modules/finances/js/withdrawal-handler.min.js');
mix.babel(['Modules/Finances/Resources/assets/js/withdrawals-table.js',], 'public/modules/finances/js/withdrawals-table.min.js');

// HotBillet
// USADO AONDE ??? mix.styles(['Modules/HotBillet/Resources/assets/css/index.css',], 'public/modules/hotbillet/css/index.min.css');
mix.babel(['Modules/HotBillet/Resources/assets/js/index.js',], 'public/modules/hotbillet/js/index.min.js');

// HotZapp
// USADO AONDE ??? mix.styles(['Modules/HotZapp/Resources/assets/css/index.css',], 'public/modules/hotzapp/css/index.min.css');
mix.babel(['Modules/HotZapp/Resources/assets/js/index.js',], 'public/modules/hotzapp/js/index.min.js');

// Integrations
mix.styles(['Modules/Integrations/Resources/assets/css/edit-integrations.css',], 'public/modules/integrations/css/edit-integrations.min.css');
mix.babel(['Modules/Integrations/Resources/assets/js/index.js',], 'public/modules/integrations/js/index.min.js');

// Invites
mix.babel(['Modules/Invites/Resources/assets/js/invites.js',], 'public/modules/invites/js/invites.min.js');

// Melhorenvio
mix.copy('Modules/Melhorenvio/Resources/assets/img','public/modules/melhorenvio/img');
mix.styles(['Modules/Melhorenvio/Resources/assets/css/index.css',], 'public/modules/melhorenvio/css/index.min.css');
mix.styles(['Modules/Melhorenvio/Resources/assets/css/tutorial.css',], 'public/modules/melhorenvio/css/tutorial.min.css');
mix.babel(['Modules/Melhorenvio/Resources/assets/js/index.js',], 'public/modules/melhorenvio/js/index.min.js');

// Notazz
mix.styles(['Modules/Notazz/Resources/assets/css/index.css',], 'public/modules/notazz/css/index.min.css');
mix.babel(['Modules/Notazz/Resources/assets/js/detail.js',], 'public/modules/notazz/js/detail.min.js');
mix.babel(['Modules/Notazz/Resources/assets/js/index.js',], 'public/modules/notazz/js/index.min.js');
mix.babel(['Modules/Notazz/Resources/assets/js/show.js',], 'public/modules/notazz/js/show.min.js');

// OrderBump
mix.babel(['Modules/OrderBump/Resources/assets/js/index.js',], 'public/modules/orderbump/js/index.min.js');

// Pixels
mix.styles(['Modules/Pixels/Resources/assets/css/pixel-edit.css',], 'public/modules/pixels/css/pixel-edit.min.css');
mix.babel(['Modules/Pixels/Resources/assets/js/pixels.js',], 'public/modules/pixels/js/pixels.min.js');
mix.babel(['Modules/Pixels/Resources/assets/js/pixelsaffiliate.js',], 'public/modules/pixels/js/pixelsaffiliate.min.js');

// Plans
mix.babel(['Modules/Plans/Resources/assets/js/loading.js',], 'public/modules/plans/js/loading.min.js');
mix.babel(['Modules/Plans/Resources/assets/js/plans.js',], 'public/modules/plans/js/plans.min.js');

// Products
mix.styles(['Modules/Products/Resources/assets/css/create.css',], 'public/modules/products/css/create.min.css');
mix.styles(['Modules/Products/Resources/assets/css/edit.css',], 'public/modules/products/css/edit.min.css');
mix.styles(['Modules/Products/Resources/assets/css/products.css',], 'public/modules/products/css/products.min.css');
mix.babel(['Modules/Products/Resources/assets/js/create-digital.js',], 'public/modules/products/js/create-digital.min.js');
mix.babel(['Modules/Products/Resources/assets/js/create-physical.js',], 'public/modules/products/js/create-physical.min.js');
mix.babel(['Modules/Products/Resources/assets/js/create.js',], 'public/modules/products/js/create.min.js');
// USADO AONDE ??? mix.babel(['Modules/Products/Resources/assets/js/edit.js',], 'public/modules/products/js/edit.min.js');
mix.babel(['Modules/Products/Resources/assets/js/index.js',], 'public/modules/products/js/index.min.js');
mix.babel(['Modules/Products/Resources/assets/js/products.js',], 'public/modules/products/js/products.min.js');

// Profile
mix.styles(['Modules/Profile/Resources/assets/css/basic.css',], 'public/modules/profile/css/basic.min.css');
mix.styles(['Modules/Profile/Resources/assets/css/dropzone.css',], 'public/modules/profile/css/dropzone.min.css');
mix.babel(['Modules/Profile/Resources/assets/js/profile.js',], 'public/modules/profile/js/profile.min.js');

// ProjectNotification
mix.babel(['Modules/ProjectNotification/Resources/assets/js/projectNotification.js',], 'public/modules/projectNotification/js/projectNotification.min.js');

// ProjectReviews
mix.babel(['Modules/ProjectReviews/Resources/assets/js/index.js',], 'public/modules/projectreviews/js/index.min.js');

// Projects
mix.copy('Modules/Projects/Resources/assets/img','public/modules/projects/img');
mix.styles(['Modules/Projects/Resources/assets/css/create.css',], 'public/modules/projects/css/create.min.css');
mix.styles(['Modules/Projects/Resources/assets/css/edit.css',], 'public/modules/projects/css/edit.min.css');
mix.styles(['Modules/Projects/Resources/assets/css/index.css',], 'public/modules/projects/css/index.min.css');
mix.styles(['Modules/Projects/Resources/assets/css/style.css',], 'public/modules/projects/css/style.min.css');
mix.babel(['Modules/Projects/Resources/assets/js/create.js',], 'public/modules/projects/js/create.min.js');
mix.babel(['Modules/Projects/Resources/assets/js/index.js',], 'public/modules/projects/js/index.min.js');
mix.babel(['Modules/Projects/Resources/assets/js/projectaffiliate.js',], 'public/modules/projects/js/projectaffiliate.min.js');
mix.babel(['Modules/Projects/Resources/assets/js/projects.js',], 'public/modules/projects/js/projects.min.js');

// Register
mix.copy('Modules/Register/Resources/assets/img','public/modules/register/img');
mix.styles(['Modules/Register/Resources/assets/css/animate.css',], 'public/modules/register/css/animate.min.css');
mix.styles(['Modules/Register/Resources/assets/css/animateColor.css',], 'public/modules/register/css/animateColor.min.css');
mix.styles(['Modules/Register/Resources/assets/css/bootstrap.min.css',], 'public/modules/register/css/bootstrap.min.css'); // BOOTSTRAP
mix.styles(['Modules/Register/Resources/assets/css/jquery-ui.min.css',], 'public/modules/register/css/jquery-ui.min.css'); // JQUERY UI
mix.styles(['Modules/Register/Resources/assets/css/mootools.css',], 'public/modules/register/css/mootools.min.css'); // MOOTOOLS
mix.styles(['Modules/Register/Resources/assets/css/style.css',], 'public/modules/register/css/style.min.css');
mix.babel(['Modules/Register/Resources/assets/js/bootstrap.min.js',], 'public/modules/register/js/bootstrap.min.js'); // BOOTSTRAP
mix.babel(['Modules/Register/Resources/assets/js/jquery-ui.min.js',], 'public/modules/register/js/jquery-ui.min.js'); // JQUERY UI
mix.babel(['Modules/Register/Resources/assets/js/passwordStrength.js',], 'public/modules/register/js/passwordStrength.min.js');
mix.babel(['Modules/Register/Resources/assets/js/pesquisaCep.js',], 'public/modules/register/js/pesquisaCep.min.js');
mix.babel(['Modules/Register/Resources/assets/js/register.js',], 'public/modules/register/js/register.min.js');
mix.babel(['Modules/Register/Resources/assets/js/wow.min.js',], 'public/modules/register/js/wow.min.js'); // WOW

// Reportana
mix.babel(['Modules/Reportana/Resources/assets/js/index.js',], 'public/modules/reportana/js/index.min.js');

// Reports
mix.styles(['Modules/Reports/Resources/assets/css/chartist.min.css',], 'public/modules/reports/css/chartist.min.css');
mix.styles(['Modules/Reports/Resources/assets/css/chartist-plugin-tooltip.min.css',], 'public/modules/reports/css/chartist-plugin-tooltip.min.css');
mix.styles(['Modules/Reports/Resources/assets/css/coupons.css',], 'public/modules/reports/css/coupons.min.css');
// USADO AONDE ??? mix.styles(['Modules/Reports/Resources/assets/css/pending.css',], 'public/modules/reports/css/pending.min.css');
mix.styles(['Modules/Reports/Resources/assets/css/reports.css',], 'public/modules/reports/css/reports.min.css');
mix.babel(['Modules/Reports/Resources/assets/js/chartist.min.js',], 'public/modules/reports/js/chartist.min.js');
mix.babel(['Modules/Reports/Resources/assets/js/chartist-plugin-legend.min.js',], 'public/modules/reports/js/chartist-plugin-legend.min.js');
mix.babel(['Modules/Reports/Resources/assets/js/chartist-plugin-tooltip.min.js',], 'public/modules/reports/js/chartist-plugin-tooltip.min.js');
mix.babel(['Modules/Reports/Resources/assets/js/detail.js',], 'public/modules/reports/js/detail.min.js');
mix.babel(['Modules/Reports/Resources/assets/js/moment.min.js',], 'public/modules/reports/js/moment.min.js');
mix.babel(['Modules/Reports/Resources/assets/js/projections.js',], 'public/modules/reports/js/projections.min.js');
mix.babel(['Modules/Reports/Resources/assets/js/report-blockedbalance.js',], 'public/modules/reports/js/report-blockedbalance.min.js');
mix.babel(['Modules/Reports/Resources/assets/js/report-checkouts.js',], 'public/modules/reports/js/report-checkouts.min.js');
mix.babel(['Modules/Reports/Resources/assets/js/report-coupons.js',], 'public/modules/reports/js/report-coupons.min.js');
mix.babel(['Modules/Reports/Resources/assets/js/report-pending.js',], 'public/modules/reports/js/report-pending.min.js');
mix.babel(['Modules/Reports/Resources/assets/js/reports.js',], 'public/modules/reports/js/reports.min.js');
// USADO AONDE ??? mix.babel(['Modules/Reports/Resources/assets/js/sales_by_origin.js',], 'public/modules/reports/js/sales_by_origin.min.js');

// Sales
mix.styles(['Modules/Sales/Resources/assets/css/index.css',], 'public/modules/sales/css/index.min.css');
mix.babel(['Modules/Sales/Resources/assets/js/detail.js',], 'public/modules/sales/js/detail.min.js');
mix.babel(['Modules/Sales/Resources/assets/js/index.js',], 'public/modules/sales/js/index.min.js');

// SalesBlackListAntifraud
// USADO AONDE ??? mix.styles(['Modules/SalesBlackListAntifraud/Resources/assets/css/index.css',], 'public/modules/salesBlackListAntifraud/css/index.min.css');
// USADO AONDE ??? mix.babel(['Modules/SalesBlackListAntifraud/Resources/assets/js/detail.js',], 'public/modules/salesBlackListAntifraud/js/detail.min.js');
// USADO AONDE ??? mix.babel(['Modules/SalesBlackListAntifraud/Resources/assets/js/index.js',], 'public/modules/salesBlackListAntifraud/js/index.min.js');

// SalesRecovery
mix.babel(['Modules/SalesRecovery/Resources/assets/js/salesrecovery.js',], 'public/modules/salesrecovery/js/salesrecovery.min.js');
// USADO AONDE ??? mix.babel(['Modules/SalesRecovery/Resources/assets/js/salesy.js',], 'public/modules/salesrecovery/js/salesy.min.js');

// Shipping
mix.styles(['Modules/Shipping/Resources/assets/css/shipping-edit.css',], 'public/modules/shipping/css/shipping-edit.min.css');
mix.babel(['Modules/Shipping/Resources/assets/js/shipping.js',], 'public/modules/shipping/js/shipping.min.js');

// Shopify
mix.styles(['Modules/Shopify/Resources/assets/css/index.css',], 'public/modules/shopify/css/index.min.css');
mix.babel(['Modules/Shopify/Resources/assets/js/index.js',], 'public/modules/shopify/js/index.min.js');

// Smartfunnel
mix.babel(['Modules/Smartfunnel/Resources/assets/js/index.js',], 'public/modules/smartfunnel/js/index.min.js');

// Tickets
mix.babel(['Modules/Tickets/Resources/assets/js/emoji-button.min.js',], 'public/modules/tickets/js/emoji-button.min.js');
mix.babel(['Modules/Tickets/Resources/assets/js/index.js',], 'public/modules/tickets/js/index.min.js');

// Trackings
mix.copy('Modules/Trackings/Resources/assets/svg','public/modules/trackings/svg');
mix.styles(['Modules/Trackings/Resources/assets/css/index.css',], 'public/modules/trackings/css/index.min.css');
mix.babel(['Modules/Trackings/Resources/assets/js/index.js',], 'public/modules/trackings/js/index.min.js');

// Unicodrop
mix.babel(['Modules/Unicodrop/Resources/assets/js/index.js',], 'public/modules/unicodrop/js/index.min.js');

// Whatsapp2
mix.babel(['Modules/Whatsapp2/Resources/assets/js/index.js',], 'public/modules/whatsapp2/js/index.min.js');

// Withdrawals
// USADO AONDE ??? mix.babel(['Modules/Withdrawals/Resources/assets/js/index.js',], 'public/modules/withdrawals/js/index.min.js');

// WooCommerce
mix.copy('Modules/WooCommerce/Resources/assets/plugins','public/modules/wooCommerce/plugins');
// USADO AONDE ??? mix.styles(['Modules/WooCommerce/Resources/assets/css/index.css',], 'public/modules/wooCommerce/css/index.min.css');
mix.babel(['Modules/WooCommerce/Resources/assets/js/index.js',], 'public/modules/woocommerce/js/index.min.js');
mix.babel(['Modules/WooCommerce/Resources/assets/js/syncproducts.js',], 'public/modules/woocommerce/js/syncproducts.min.js');


if (mix.inProduction()) {
    mix.version();
}







