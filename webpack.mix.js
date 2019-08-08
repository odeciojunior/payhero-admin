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

//auth
mix.babel([
    'resources/assets/modules/auth/js/reset.js',
], 'public/modules/auth/js/reset.js');

//companies
mix.babel([
    'resources/assets/modules/companies/js/create.js',
], 'public/modules/companies/js/create.js');
mix.babel([
    'resources/assets/modules/companies/js/edit.js',
], 'public/modules/companies/js/edit.js');
mix.babel([
    'resources/assets/modules/companies/js/index.js',
], 'public/modules/companies/js/index.js');

//dashboard
mix.babel([
    'resources/assets/modules/dashboard/js/dashboard.js',
], 'public/modules/dashboard/js/dashboard.js');

//discountcoupons
mix.babel([
    'resources/assets/modules/DiscountCoupons/js/discountCoupons.js',
], 'public/modules/DiscountCoupons/js/discountCoupons.js');

//domains
mix.babel([
    'resources/assets/modules/domain/js/domain.js',
], 'public/modules/domain/js/domain.js');

//finances
mix.babel([
    'resources/assets/modules/finances/js/index.js',
], 'public/modules/finances/js/index.js');

//Gifts
mix.babel([
    'resources/assets/modules/Gifts/js/gift.js',
], 'public/modules/Gifts/js/gift.js');

//hotzapp
mix.babel([
    'resources/assets/modules/hotzapp/js/index.js',
], 'public/modules/hotzapp/js/index.js');

//invites
mix.babel([
    'resources/assets/modules/invites/js/invites.js',
], 'public/modules/invites/js/invites.js');

//layouts
mix.babel([
    'resources/assets/modules/Layouts/js/layouts.js',
], 'public/modules/Layouts/js/layouts.js');

//partners
mix.babel([
    'resources/assets/modules/partners/js/partners.js',
], 'public/modules/partners/js/partners.js');

//partnes
mix.babel([
    'resources/assets/modules/Partnes/js/partnes.js',
], 'public/modules/Partnes/js/partnes.js');

//pixels
mix.babel([
    'resources/assets/modules/Pixels/js/pixels.js',
], 'public/modules/Pixels/js/pixels.js');

//plans
mix.babel([
    'resources/assets/modules/plans/js/plans.js',
], 'public/modules/plans/js/plans.js');

//products
mix.babel([
    'resources/assets/modules/products/js/edit.js',
], 'public/modules/products/js/edit.js');
mix.babel([
    'resources/assets/modules/products/js/index.js',
], 'public/modules/products/js/index.js');
mix.babel([
    'resources/assets/modules/products/js/products.js',
], 'public/modules/products/js/products.js');

//profile
mix.babel([
    'resources/assets/modules/profile/js/profile.js',
], 'public/modules/profile/js/profile.js');

//projects
mix.babel([
    'resources/assets/modules/projects/js/create.js',
], 'public/modules/projects/js/create.js');

//register
mix.babel([
    'resources/assets/modules/register/js/bootstrap.min.js',
], 'public/modules/register/js/bootstrap.min.js');
mix.babel([
    'resources/assets/modules/register/js/jquery-ui.min.js',
], 'public/modules/register/js/jquery-ui.min.js');
mix.babel([
    'resources/assets/modules/register/js/passwordStrength.js',
], 'public/modules/register/js/passwordStrength.js');
mix.babel([
    'resources/assets/modules/register/js/pesquisaCep.js',
], 'public/modules/register/js/pesquisaCep.js');
mix.babel([
    'resources/assets/modules/register/js/register.js',
], 'public/modules/register/js/register.js');
mix.babel([
    'resources/assets/modules/register/js/wow.min.js',
], 'public/modules/register/js/wow.min.js');

//reports
mix.babel([
    'resources/assets/modules/reports/js/chartist.min.js',
], 'public/modules/reports/js/chartist.min.js');
mix.babel([
    'resources/assets/modules/reports/js/chartist-plugin-legend.min.js',
], 'public/modules/reports/js/chartist-plugin-legend.min.js');
mix.babel([
    'resources/assets/modules/reports/js/chartist-plugin-tooltip.min.js',
], 'public/modules/reports/js/chartist-plugin-tooltip.min.js');
mix.babel([
    'resources/assets/modules/reports/js/moment.min.js',
], 'public/modules/reports/js/moment.min.js');
mix.babel([
    'resources/assets/modules/reports/js/reports.js',
], 'public/modules/reports/js/reports.js');
mix.babel([
    'resources/assets/modules/reports/js/sales_by_origin.js',
], 'public/modules/reports/js/sales_by_origin.js');

//sales
mix.babel([
    'resources/assets/modules/sales/js/index.js',
], 'public/modules/sales/js/index.js');

//salesRecovery
mix.babel([
    'resources/assets/modules/salesrecovery/js/salesrecovery.js',
], 'public/modules/salesrecovery/js/salesrecovery.js');

//Shipping
mix.babel([
    'resources/assets/modules/Shipping/js/shipping.js',
], 'public/modules/Shipping/js/shipping.js');

//shopify
mix.babel([
    'resources/assets/modules/shopify/js/index.js',
], 'public/modules/shopify/js/index.js');

//SmsMessage
mix.babel([
    'resources/assets/modules/SmsMessage/js/smsMessage.js',
], 'public/modules/SmsMessage/js/smsMessage.js');

//transfers
mix.babel([
    'resources/assets/modules/transfers/js/index.js',
], 'public/modules/transfers/js/index.js');

//withdrawals
mix.babel([
    'resources/assets/modules/withdrawals/js/index.js',
], 'public/modules/withdrawals/js/index.js');


if (mix.inProduction()) {
    mix.version();
}







