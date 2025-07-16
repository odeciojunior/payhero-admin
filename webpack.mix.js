let mix = require("laravel-mix");

mix.styles(
    ['resources/assets/input-copy-styles.css'],
    'public/build/input-copy-styles.min.css',
);

/**
 * Modules/ActiveCampaign/Resources/views/index.blade.php
 */
mix.styles(["resources/modules/global/css/empty.css"], "public/build/layouts/activecampaign/index.min.css");
mix.babel(["Modules/ActiveCampaign/Resources/assets/js/index.js"], "public/build/layouts/activecampaign/index.min.js");

/**
 * Modules/ActiveCampaign/Resources/views/show.blade.php
 */
mix.styles(
    [
        "resources/modules/global/css/switch.css",
        "resources/modules/global/css/table.css",
        "resources/cdn/select2.min.css",
    ],
    "public/build/layouts/activecampaign/show.min.css"
);
mix.babel(
    [
        "Modules/ActiveCampaign/Resources/assets/js/edit.js",
        "Modules/ActiveCampaign/Resources/assets/js/events.js",
        "resources/cdn/select2.min.js",
    ],
    "public/build/layouts/activecampaign/show.min.js"
);

/**
 * Modules/Affiliates/Resources/views/index.blade.php
 */
mix.babel(["Modules/Affiliates/Resources/assets/js/index.js"], "public/build/layouts/affiliates/index.min.js");

/**
 * Modules/Affiliates/Resources/views/projectaffiliates.blade.php
 */
mix.styles(
    ["resources/modules/global/css/table.css", "Modules/Affiliates/Resources/assets/css/index.css"],
    "public/build/layouts/affiliates/projectaffiliates.min.css"
);
mix.babel(
    ["Modules/Affiliates/Resources/assets/js/projectaffiliates.js"],
    "public/build/layouts/affiliates/projectaffiliates.min.js"
);

/**
 * Modules/Affiliates/Resources/views/layouts/master.blade.php
 */
mix.styles(
    [
        "resources/modules/global/adminremark/global/css/bootstrap.min.css",
        "resources/modules/global/adminremark/global/css/bootstrap-extend.min.css",
        "resources/modules/global/adminremark/assets/css/site.css",
        "resources/modules/global/css/loading.css",
        "resources/modules/global/css/checkAnimation.css",
        "resources/modules/global/css/ribbon.css",
        "resources/modules/global/adminremark/global/vendor/animsition/animsition.css",
        "resources/modules/global/adminremark/global/vendor/jquery-mmenu/jquery-mmenu.css",
        "resources/modules/global/jquery-imgareaselect/css/imgareaselect-default.css",
        "resources/modules/global/css/sweetalert2.min.css",
        "resources/modules/global/css/daterangepicker.css",
        "resources/modules/global/adminremark/global/fonts/web-icons/web-icons.css",
        "resources/modules/global/adminremark/global/fonts/font-awesome/font-awesome.css",
        "resources/modules/global/css/newFonts.css",
        "resources/modules/global/css/new-dashboard.css",
        "resources/modules/fonts-googleapis-material-icons.css",
        "resources/modules/global/css/materialdesignicons.min.css",
        "resources/modules/global/css/new-site.css",
        "resources/modules/global/css/finances.css",
        "resources/modules/global/css/global.css",
    ],
    "public/build/layouts/affiliates/master.min.css"
);
mix.babel(
    [
        "resources/modules/global/adminremark/global/vendor/babel-external-helpers/babel-external-helpers.js",
        "resources/modules/global/adminremark/global/vendor/popper-js/umd/popper.min.js",
        "resources/modules/global/adminremark/global/vendor/bootstrap/bootstrap.js",
        "resources/modules/global/adminremark/global/vendor/animsition/animsition.js",
        "resources/modules/global/adminremark/global/vendor/asscrollbar/jquery-asScrollbar.js",
        "resources/modules/global/adminremark/global/vendor/asscrollable/jquery-asScrollable.js",
        "resources/modules/global/adminremark/global/vendor/jquery-mmenu/jquery.mmenu.min.all.js",
        "resources/modules/global/adminremark/global/vendor/matchheight/jquery.matchHeight-min.js",
        "resources/modules/global/js-extra/jquery.mask.min.js",
        "resources/modules/global/js-extra/jquery.maskMoney.js",
        "resources/modules/global/js-extra/sweetalert2.all.min.js",
        "resources/modules/global/adminremark/global/js/Component.js",
        "resources/modules/global/adminremark/global/js/Plugin.js",
        "resources/modules/global/adminremark/global/js/Base.js",
        "resources/modules/global/adminremark/global/js/Config.js",
        "resources/modules/global/adminremark/assets/js/Section/Menubar.js",
        "resources/modules/global/adminremark/assets/js/Section/Sidebar.js",
        "resources/modules/global/adminremark/assets/js/Section/PageAside.js",
        "resources/modules/global/adminremark/assets/js/Section/GridMenu.js",
        "resources/modules/global/adminremark/assets/js/Site.js",
        "resources/modules/global/adminremark/assets/examples/js/dashboard/v1.js",
        "resources/modules/global/jquery-imgareaselect/scripts/jquery.imgareaselect.pack.js",
        "resources/modules/global/js/global.js",
    ],
    "public/build/layouts/affiliates/master.min.js"
);
mix.babel(
    [
        "resources/modules/global/adminremark/global/vendor/jquery/jquery.min.js",
        "resources/modules/global/adminremark/global/vendor/breakpoints/breakpoints.js",
    ],
    "public/build/layouts/affiliates/master2.min.js"
);

mix.copy(
    "resources/modules/global/jquery-imgareaselect/css/border-anim-h.gif",
    "public/build/layouts/affiliates/border-anim-h.gif"
);
mix.copy(
    "resources/modules/global/jquery-imgareaselect/css/border-anim-v.gif",
    "public/build/layouts/affiliates/border-anim-v.gif"
);
mix.copy(
    "resources/modules/global/jquery-imgareaselect/css/border-h.gif",
    "public/build/layouts/affiliates/border-h.gif"
);
mix.copy(
    "resources/modules/global/jquery-imgareaselect/css/border-v.gif",
    "public/build/layouts/affiliates/border-v.gif"
);

/**
 * Modules/AdooreiCheckout/Resources/views/index.blade.php
 */
mix.styles(["resources/modules/global/css/empty.css"], "public/build/layouts/adooreicheckout/index.min.css");
mix.babel(
    ["Modules/AdooreiCheckout/Resources/assets/js/index.js"],
    "public/build/layouts/adooreicheckout/index.min.js"
);

/**
 * Modules/Apps/Resources/views/index.blade.php
 */
mix.styles(["Modules/Apps/Resources/assets/css/index.css"], "public/build/layouts/apps/index.min.css");
mix.babel(
    ["Modules/Apps/Resources/assets/js/index.js", "resources/modules/global/js-extra/moment.min.js"],
    "public/build/layouts/apps/index.min.js"
);

mix.copy("Modules/Apps/Resources/assets/imgs", "public/build/layouts/apps/imgs");

/**
 * Modules/AstronMembers/Resources/views/index.blade.php
 */
mix.styles(
    ["Modules/ConvertaX/Resources/assets/css/index.css", "resources/modules/global/css/empty.css"],
    "public/build/layouts/astronmembers/index.min.css"
);
mix.babel(["Modules/AstronMembers/Resources/assets/js/index.js"], "public/build/layouts/astronmembers/index.min.js");

/**
 * Modules/NotificacoesInteligentes/Resources/views/index.blade.php
 */
mix.styles(
    [
        "Modules/ConvertaX/Resources/assets/css/index.css",
        "resources/modules/global/css/empty.css",
        "Modules/NotificacoesInteligentes/Resources/assets/css/index.css",
    ],
    "public/build/layouts/notificacoesInteligentes/index.min.css"
);
mix.babel(
    ["Modules/NotificacoesInteligentes/Resources/assets/js/index.js"],
    "public/build/layouts/notificacoesInteligentes/index.min.js"
);

/**
 * Modules/Attendance/Resources/views/index.blade.php
 */
mix.styles(
    [
        "resources/modules/global/css/new-dashboard.css",
        "resources/modules/global/select3/select3.css",
        "resources/modules/global/jquery-daterangepicker/daterangepicker.css",
        "Modules/Attendance/Resources/assets/css/index.css",
    ],
    "public/build/layouts/attendance/index.min.css"
);
mix.babel(
    [
        "resources/modules/global/select3/select3.js",
        "resources/modules/global/js-extra/moment.min.js",
        "resources/modules/global/jquery-daterangepicker/daterangepicker.js",
        "Modules/Tickets/Resources/assets/js/emoji-button.min.js",
        "Modules/Tickets/Resources/assets/js/index.js",
    ],
    "public/build/layouts/attendance/index.min.js"
);

/**
 * Modules/Chargebacks/Resources/views/contestations-files.blade.php
 */
mix.styles(
    ["Modules/Sales/Resources/assets/css/index.css"],
    "public/build/layouts/chargebacks/contestations-files.min.css"
);
mix.babel(
    ["Modules/Chargebacks/Resources/assets/js/contestations-detail.js"],
    "public/build/layouts/chargebacks/contestations-files.min.js"
);

/**
 * Modules/Chargebacks/Resources/views/contestations-index.blade.php
 */
mix.styles(
    [
        "resources/modules/global/css/table.css",
        "Modules/Sales/Resources/assets/css/index.css",
        "resources/modules/global/css/empty.css",
        "resources/modules/global/css/switch.css",
        "resources/modules/global/css/new-dashboard.css",
        "Modules/Chargebacks/Resources/assets/css/contestations-index.css",
        "resources/modules/global/jquery-daterangepicker/daterangepicker.css",
        "resources/cdn/select2.min.css",
    ],
    "public/build/layouts/chargebacks/contestations-index.min.css"
);
mix.babel(
    [
        "Modules/Chargebacks/Resources/assets/js/contestations-index.js",
        "resources/modules/global/js-extra/moment.min.js",
        "resources/modules/global/jquery-daterangepicker/daterangepicker.js",
        "resources/cdn/select2.min.js",
    ],
    "public/build/layouts/chargebacks/contestations-index.min.js"
);

mix.copy("Modules/Chargebacks/Resources/assets/svg", "public/build/layouts/chargebacks/svg");

/**
 * Modules/CheckoutEditor/Resources/views/index.blade.php
 */
mix.copy(
    ["resources/modules/global/adminremark/global/vendor/dropify/!(*.css)"],
    "public/build/layouts/checkouteditor"
);
mix.styles(
    [
        "resources/modules/global/css/empty.css",
        "resources/modules/global/adminremark/global/vendor/dropify/dropify.css",
        "Modules/CheckoutEditor/Resources/assets/css/quill.snow.css",
        "Modules/CheckoutEditor/Resources/assets/css/dropfy.css",
        "Modules/CheckoutEditor/Resources/assets/css/custom-inputs.css",
        "Modules/CheckoutEditor/Resources/assets/css/cropper.min.css",
        "Modules/CheckoutEditor/Resources/assets/css/style.css",
        "Modules/CheckoutEditor/Resources/assets/css/preview-styles.css",
    ],
    "public/build/layouts/checkouteditor/index.min.css"
);
mix.babel(
    [
        "resources/modules/global/adminremark/global/js/Plugin/cropper.js",
        "resources/modules/global/adminremark/global/vendor/dropify/dropify.min.js",
        "resources/modules/global/adminremark/global/js/Plugin/dropify.js",
        "Modules/CheckoutEditor/Resources/assets/js/quill.min.js",
        "Modules/CheckoutEditor/Resources/assets/js/cropper.min.js",
        "Modules/CheckoutEditor/Resources/assets/js/verifyPhone.js",
        "Modules/CheckoutEditor/Resources/assets/js/checkoutEditor.js",
        "Modules/CheckoutEditor/Resources/assets/js/loadCheckoutData.js",
        "Modules/CheckoutEditor/Resources/assets/js/scrollPreview.js",
    ],
    "public/build/layouts/checkouteditor/index.min.js"
);
mix.copy("Modules/CheckoutEditor/Resources/assets/img", "public/build/layouts/checkouteditor/img");
mix.copy("Modules/CheckoutEditor/Resources/assets/img/banner", "public/build/layouts/checkouteditor/img/banner");
mix.copy("Modules/CheckoutEditor/Resources/assets/img/svg", "public/build/layouts/checkouteditor/img/svg");
mix.copy(
    "Modules/CheckoutEditor/Resources/assets/img/svg/credit-card-icons",
    "public/build/layouts/checkouteditor/img/svg/credit-card-icons"
);
mix.copy("Modules/CheckoutEditor/Resources/assets/files", "public/build/layouts/checkouteditor/files");

/**
 * Modules/ConvertaX/Resources/views/include.blade.php
 */
mix.styles(["resources/modules/global/css/empty.css"], "public/build/layouts/convertax/include.min.css");

/**
 * Modules/ConvertaX/Resources/views/index.blade.php
 */
mix.styles(
    [
        "resources/modules/global/css/switch.css",
        "Modules/ConvertaX/Resources/assets/css/index.css",
        "resources/modules/global/css/empty.css",
    ],
    "public/build/layouts/convertax/index.min.css"
);
mix.babel(["Modules/ConvertaX/Resources/assets/js/index.js"], "public/build/layouts/convertax/index.min.js");

/**
 * Modules/Dashboard/Resources/views/onboarding/presentation.blade.php
 */
mix.styles(
    [
        "resources/cdn/slick.css",
        "resources/cdn/slick-theme.css",
        "Modules/Dashboard/Resources/assets/css/onboarding-details.css",
    ],
    "public/build/layouts/dashboard/onboarding-details.min.css"
);
mix.babel(["resources/cdn/slick.min.js"], "public/build/layouts/dashboard/onboarding-details.min.js");

/**
 * Modules/Dashboard/Resources/views/pix/pix.blade.php
 */
mix.styles(
    ["resources/cdn/slick.css", "resources/cdn/slick-theme.css", "Modules/Dashboard/Resources/assets/css/pix.css"],
    "public/build/layouts/dashboard/pix.min.css"
);
mix.babel(["resources/cdn/slick.min.js"], "public/build/layouts/dashboard/pix.min.js");

/**
 * Modules/Dashboard/Resources/views/achievement-details.blade.php
 */
mix.styles(
    ["Modules/Dashboard/Resources/assets/css/achievement-details.css"],
    "public/build/layouts/dashboard/achievement-details.min.css"
);

/**
 * Modules/Dashboard/Resources/views/app.blade.php
 */
mix.styles(["resources/cdn/bootstrap.min.css"], "public/build/layouts/dashboard/app.min.css");
mix.babel(
    ["resources/cdn/jquery-3.3.1.slim.min.js", "resources/cdn/popper.min.js", "resources/cdn/bootstrap.min.js"],
    "public/build/layouts/dashboard/app.min.js"
);

/**
 * Modules/Dashboard/Resources/views/dashboard.blade
 */
mix.styles(
    [
        "Modules/Dashboard/Resources/assets/css/skeleton-loading.css",
        "resources/modules/global/css/new-dashboard.css",
        "Modules/Dashboard/Resources/assets/css/chartist.min.css",
        "Modules/Dashboard/Resources/assets/css/chartist-plugin-tooltip.min.css",
        "Modules/Dashboard/Resources/assets/css/index.css",
        "Modules/Dashboard/Resources/assets/css/dashboard-performance.css",
        "Modules/Dashboard/Resources/assets/css/dashboard-account-health.css",
    ],
    "public/build/layouts/dashboard/stylesheets.min.css"
);
mix.babel(
    [
        "Modules/Dashboard/Resources/assets/js/gauge.js",
        "Modules/Dashboard/Resources/assets/js/chartist.min.js",
        "Modules/Dashboard/Resources/assets/js/chartist-plugin-tooltip.min.js",
        "Modules/Dashboard/Resources/assets/js/chartist-plugin-legend.min.js",
        "resources/modules/global/js/confetti.browser.min.js",
        "Modules/Dashboard/Resources/assets/js/dashboard-performance.js",
        "Modules/Dashboard/Resources/assets/js/dashboard.js",
        "Modules/Dashboard/Resources/assets/js/dashboard-account-health.js",
    ],
    "public/build/layouts/dashboard/scripts.min.js"
);

/**
 * Modules/Finances/Resources/assets/js/withdrawal-handler.js
 */
mix.babel(
    ["Modules/Finances/Resources/assets/js/withdrawal-custom.js"],
    "public/build/layouts/finances/withdrawal-custom.min.js"
);
mix.babel(
    ["Modules/Finances/Resources/assets/js/withdrawal-default.js"],
    "public/build/layouts/finances/withdrawal-default.min.js"
);

/**
 * Modules/Finances/Resources/views/components/details.blade.php
 */
mix.babel(["Modules/Finances/Resources/assets/js/detail.js"], "public/build/layouts/finances/detail.min.js");

/**
 * Modules/Finances/Resources/views/index.blade.php
 */
mix.styles(
    [
        "Modules/Finances/Resources/assets/css/jPages.css",
        "resources/modules/global/css/empty.css",
        "resources/modules/global/css/switch.css",
        "resources/modules/global/css/table.css",
        "Modules/Finances/Resources/assets/css/new-finances.css",
    ],
    "public/build/layouts/finances/index.min.css"
);
mix.babel(
    [
        "resources/modules/global/js-extra/moment.min.js",
        "resources/modules/global/js/daterangepicker.min.js",
        "Modules/Finances/Resources/assets/js/jPages.min.js",
        "Modules/Finances/Resources/assets/js/statement-index.js",
        "Modules/Finances/Resources/assets/js/balances.js",
        "Modules/Finances/Resources/assets/js/withdrawals-table.js",
        "Modules/Finances/Resources/assets/js/withdrawal-handler.js",
        "Modules/Finances/Resources/assets/js/statement.js",
    ],
    "public/build/layouts/finances/index.min.js"
);

/**
 * Modules/Finances/Resources/views/multi.blade.php
 */
mix.styles(
    [
        "resources/modules/global/adminremark/global/vendor/owl-carousel/owl.carousel.min.css",
        "resources/modules/global/css/empty.css",
        "resources/modules/global/css/switch.css",
        "Modules/Finances/Resources/assets/css/new-finances.css",
        "Modules/Finances/Resources/assets/css/multi-finances.css",
    ],
    "public/build/layouts/finances/multi.min.css"
);
mix.babel(
    [
        "resources/modules/global/js-extra/moment.min.js",
        "resources/modules/global/js/daterangepicker.min.js",
        "Modules/Finances/Resources/assets/js/jPages.min.js",
        "resources/modules/global/adminremark/global/vendor/owl-carousel/owl.carousel.min.js",
        "Modules/Finances/Resources/assets/js/multi-finances.js",
        "Modules/Finances/Resources/assets/js/multi-finances-withdrawals.js",
        "Modules/Finances/Resources/assets/js/settings.js",
    ],
    "public/build/layouts/finances/multi.min.js"
);

/**
 * Modules/HotBillet/Resources/views/index.blade.php
 */
mix.styles(
    ["Modules/ConvertaX/Resources/assets/css/index.css", "resources/modules/global/css/empty.css"],
    "public/build/layouts/hotbillet/index.min.css"
);
mix.styles(["resources/modules/global/css/empty.css"], "public/build/layouts/hotbillet/index2.min.css");
mix.babel(["Modules/HotBillet/Resources/assets/js/index.js"], "public/build/layouts/hotbillet/index.min.js");

/**
 * Modules/HotZapp/Resources/views/index.blade.php
 */
mix.styles(
    ["Modules/ConvertaX/Resources/assets/css/index.css", "resources/modules/global/css/empty.css"],
    "public/build/layouts/hotzapp/index.min.css"
);
mix.babel(["Modules/HotZapp/Resources/assets/js/index.js"], "public/build/layouts/hotzapp/index.min.js");

/**
 * Modules/Integrations/Resources/views/index.blade.php
 */
mix.styles(
    [
        "resources/modules/global/css/new-dashboard.css",
        "resources/modules/global/css/empty.css",
        "Modules/Integrations/Resources/assets/css/edit-integrations.css",
    ],
    "public/build/layouts/integrations/index.min.css"
);
mix.babel(["Modules/Integrations/Resources/assets/js/index.js"], "public/build/layouts/integrations/index.min.js");

/**
 * Modules/Invites/Resources/views/index.blade.php
 */
mix.styles(
    [
        "resources/modules/global/css/table.css",
        "resources/modules/global/css/new-dashboard.css",
        "resources/modules/global/css/empty.css",
        "Modules/Invites/Resources/assets/css/index.css",
    ],
    "public/build/layouts/invites/index.min.css"
);
mix.babel(["Modules/Invites/Resources/assets/js/invites.js"], "public/build/layouts/invites/index.min.js");

/**
 * Modules/Melhorenvio/Resources/views/index.blade.php
 */
mix.styles(["Modules/Melhorenvio/Resources/assets/css/index.css"], "public/build/layouts/melhorenvio/index.min.css");
mix.babel(["Modules/Melhorenvio/Resources/assets/js/index.js"], "public/build/layouts/melhorenvio/index.min.js");

/**
 * Modules/Melhorenvio/Resources/views/tutorial.blade.php
 */
mix.styles(
    ["Modules/Melhorenvio/Resources/assets/css/tutorial.css"],
    "public/build/layouts/melhorenvio/tutorial.min.css"
);

mix.copy("Modules/Melhorenvio/Resources/assets/img", "public/build/layouts/melhorenvio/img");

/**
 * Modules/Notazz/Resources/views/details.blade.php
 */
mix.babel(["Modules/Notazz/Resources/assets/js/detail.js"], "public/build/layouts/notazz/details.min.js");

/**
 * Modules/Notazz/Resources/views/include.blade.php
 */
mix.styles(["resources/modules/global/css/empty.css"], "public/build/layouts/notazz/include.min.css");

/**
 * Modules/Notazz/Resources/views/index.blade.php
 */
mix.styles(
    ["Modules/Notazz/Resources/assets/css/index.css", "resources/modules/global/css/empty.css"],
    "public/build/layouts/notazz/index.min.css"
);
mix.babel(
    ["Modules/Notazz/Resources/assets/js/index.js", "resources/modules/global/js-extra/moment.min.js"],
    "public/build/layouts/notazz/index.min.js"
);

/**
 * Modules/Notazz/Resources/views/show.blade.php
 */
mix.styles(
    [
        "resources/modules/global/css/table.css",
        "Modules/Sales/Resources/assets/css/index.css",
        "Modules/Notazz/Resources/assets/css/index.css",
        "resources/modules/global/css/empty.css",
        "resources/modules/global/css/switch.css",
        "resources/modules/global/css/new-dashboard.css",
    ],
    "public/build/layouts/notazz/show.min.css"
);
mix.babel(
    [
        "Modules/Notazz/Resources/assets/js/show.js",
        "resources/modules/global/js-extra/moment.min.js",
        "resources/modules/global/js/daterangepicker.min.js",
    ],
    "public/build/layouts/notazz/show.min.js"
);

/**
 * Modules/Pixels/Resources/views/edit.blade.php
 */
mix.styles(["Modules/Pixels/Resources/assets/css/pixel-edit.css"], "public/build/layouts/pixels/edit.min.css");

/**
 * Modules/Products/Resources/views/index.blade.php
 */
mix.styles(["resources/modules/global/css/empty.css"], "public/build/layouts/products/index.min.css");
mix.babel(["Modules/Products/Resources/assets/js/index.js"], "public/build/layouts/products/index.min.js");

/**
 * Modules/Products/Resources/views/create-digital.blade.php
 * Modules/Products/Resources/views/create-physical.blade.php
 */
mix.copy(["resources/modules/global/adminremark/global/vendor/dropify/!(*.css)"], "public/build/layouts/products");
mix.styles(
    [
        "resources/modules/global/adminremark/global/vendor/dropify/dropify.css",
        "Modules/Products/Resources/assets/css/create.css",
    ],
    "public/build/layouts/products/create.min.css"
);
mix.babel(
    [
        "Modules/Products/Resources/assets/js/create-digital.js",
        "resources/modules/global/adminremark/global/vendor/dropify/dropify.min.js",
        "resources/modules/global/adminremark/global/js/Plugin/dropify.js",
    ],
    "public/build/layouts/products/create-digital.min.js"
);
mix.babel(
    [
        "Modules/Products/Resources/assets/js/create-physical.js",
        "resources/modules/global/adminremark/global/vendor/dropify/dropify.min.js",
        "resources/modules/global/adminremark/global/js/Plugin/dropify.js",
    ],
    "public/build/layouts/products/create-physical.min.js"
);

/**
 * Modules/Products/Resources/views/edit-digital.blade.php
 * Modules/Products/Resources/views/edit-physical.blade.php
 */
mix.styles(
    [
        "resources/modules/global/adminremark/global/vendor/dropify/dropify.css",
        "Modules/Products/Resources/assets/css/edit.css",
    ],
    "public/build/layouts/products/edit.min.css"
);
mix.babel(
    [
        "Modules/Products/Resources/assets/js/products.js",
        "resources/modules/global/adminremark/global/vendor/dropify/dropify.min.js",
        "resources/modules/global/adminremark/global/js/Plugin/dropify.js",
    ],
    "public/build/layouts/products/edit.min.js"
);
mix.styles(["Modules/Products/Resources/assets/css/products.css"], "public/build/layouts/products/index.min.css");

/**
 * Modules/Projects/Resources/views/projectaffiliate.blade.php
 */
mix.styles(
    [
        "resources/cdn/select2.min.css",
        "resources/modules/global/css/switch.css",
        "Modules/Projects/Resources/assets/css/style.css",
    ],
    "public/build/layouts/projects/projectaffiliate.min.css"
);
mix.babel(
    [
        "Modules/Pixels/Resources/assets/js/pixelsaffiliate.js",
        "Modules/Projects/Resources/assets/js/projectaffiliate.js",
        "Modules/Affiliates/Resources/assets/js/links.js",
        "resources/modules/global/js/select2.min.js",
    ],
    "public/build/layouts/projects/projectaffiliate.min.js"
);

/**
 * Modules/Projects/Resources/views/project.blade.php
 */
mix.copy("resources/modules/svg/raty.svg", "public/build/layouts/projects/raty.svg");
mix.styles(
    [
        "resources/cdn/select2.min.css",
        "resources/cdn/jquery.raty.min.css",
        "resources/modules/global/scrollbar-plugin/jquery.mCustomScrollbar.css",
        "resources/modules/global/css/switch.css",
        "resources/modules/global/css/table.css",
        "Modules/Projects/Resources/assets/css/style.css",
        "Modules/DiscountCoupons/Resources/assets/css/styles.css",
        "resources/modules/global/jquery-daterangepicker/daterangepicker.css",
    ],
    "public/build/layouts/projects/project.min.css"
);
mix.copy("resources/cdn/ckeditor.js", "public/build/layouts/projects/ckeditor.js");
mix.babel(
    [
        "resources/cdn/pt-br.js",
        "resources/cdn/quill.js",
        "resources/cdn/slick.min.js",
        "resources/cdn/clipboard.min.js",
        "Modules/Domains/Resources/assets/js/domainEdit.js",
        "Modules/Plans/Resources/assets/js/loading.js",
        "Modules/Plans/Resources/assets/js/plans.js",
        "Modules/Shipping/Resources/assets/js/shipping.js",
        "Modules/Pixels/Resources/assets/js/pixels.js",
        "Modules/ProjectUpsellRule/Resources/assets/js/index.js",
        "Modules/OrderBump/Resources/assets/js/index.js",
        "Modules/ProjectReviews/Resources/assets/js/index.js",
        "Modules/ProjectNotification/Resources/assets/js/projectNotification.js",
        "Modules/Projects/Resources/assets/js/projects.js",
        "resources/modules/global/adminremark/global/vendor/dropify/dropify.min.js",
        "resources/modules/global/adminremark/global/js/Plugin/dropify.js",
        "resources/modules/global/js/select2.min.js",
        "resources/modules/global/js/jquery.raty.min.js",
        "resources/modules/global/js-extra/jquery-loading.min.js",
        "Modules/WooCommerce/Resources/assets/js/syncproducts.js",
        "resources/modules/global/scrollbar-plugin/jquery.mousewheel.min.js",
        "resources/modules/global/scrollbar-plugin/jquery.mCustomScrollbar.js",
        "resources/modules/global/js-extra/moment.min.js",
        "resources/modules/global/jquery-daterangepicker/daterangepicker.js",
        "Modules/DiscountCoupons/Resources/assets/js/discountCoupons.js",
    ],
    "public/build/layouts/projects/project.min.js"
);

/**
 * Modules/Projects/Resources/views/index.blade.php
 */
mix.styles(
    [
        "resources/cdn/jquery-ui.min.css",
        "Modules/Projects/Resources/assets/css/index.css",
        "resources/modules/global/css/switch.css",
    ],
    "public/build/layouts/projects/index.min.css"
);
mix.babel(
    ["resources/cdn/jquery-ui.min.js", "Modules/Projects/Resources/assets/js/index.js"],
    "public/build/layouts/projects/index.min.js"
);

/**
 * Modules/Projects/Resources/views/create-store-modal.blade.php
 */
mix.styles(
    ["resources/modules/global/css/create-store-modal.css"],
    "public/build/layouts/projects/create-store-modal.min.css"
);

/**
 * Modules/Projects/Resources/views/empty.blade.php
 */
mix.styles(["resources/modules/global/css/empty.css"], "public/build/layouts/projects/empty.min.css");

/**
 * Modules/Projects/Resources/views/empty-company.blade.php
 */
mix.styles(["resources/modules/global/css/empty.css"], "public/build/layouts/projects/empty-company.min.css");

/**
 * Modules/Projects/Resources/views/edit.blade.php
 */
mix.styles(["Modules/Projects/Resources/assets/css/edit.css"], "public/build/layouts/projects/edit.min.css");

/**
 * Modules/Projects/Resources/views/create.blade.php
 */
mix.copy(["resources/modules/global/adminremark/global/vendor/dropify/!(*.css)"], "public/build/layouts/projects");
mix.styles(
    [
        "resources/modules/global/css/empty.css",
        "resources/modules/global/adminremark/global/vendor/dropify/dropify.css",
        "Modules/Projects/Resources/assets/css/create.css",
    ],
    "public/build/layouts/projects/create.min.css"
);
mix.babel(
    [
        "Modules/Projects/Resources/assets/js/create.js",
        "resources/modules/global/adminremark/global/vendor/dropify/dropify.min.js",
        "resources/modules/global/adminremark/global/js/Plugin/dropify.js",
    ],
    "public/build/layouts/projects/create.min.js"
);

mix.copy("Modules/Projects/Resources/assets/img", "public/build/layouts/projects/img");

/**
 * Modules/ProjectUpsellConfig/Resources/views/previewupsellconfig.blade.php
 */
mix.styles(
    ["resources/modules/global/css/upsell.css"],
    "public/build/layouts/projectupsellconfig/previewupsellconfig.min.css"
);

/**
 * Modules/Reportana/Resources/views/index.blade.php
 */
mix.styles(
    ["resources/modules/digitalmanager/css/index.css", "resources/modules/global/css/empty.css"],
    "public/build/layouts/reportana/index.min.css"
);
mix.babel(["Modules/Reportana/Resources/assets/js/index.js"], "public/build/layouts/reportana/index.min.js");

/**
 * Modules/GeradorRastreio/Resources/views/index.blade.php
 */
mix.styles(
    ["resources/modules/digitalmanager/css/index.css", "resources/modules/global/css/empty.css"],
    "public/build/layouts/geradorrastreio/index.min.css"
);
mix.babel(
    ["Modules/GeradorRastreio/Resources/assets/js/index.js"],
    "public/build/layouts/geradorrastreio/index.min.js"
);

/**
 * Modules/Utmify/Resources/views/index.blade.php
 */
mix.styles(["resources/modules/global/css/empty.css"], "public/build/layouts/utmify/index.min.css");
mix.babel(["Modules/Utmify/Resources/assets/js/index.js"], "public/build/layouts/utmify/index.min.js");

/**
 * Modules/VegaCheckout/Resources/views/index.blade.php
 */
mix.styles(["resources/modules/global/css/empty.css"], "public/build/layouts/vegacheckout/index.min.css");
mix.babel(["Modules/VegaCheckout/Resources/assets/js/index.js"], "public/build/layouts/vegacheckout/index.min.js");

/**
 * Modules/Reports/Resources/views/layouts/details.blade.php
 */
mix.styles(["Modules/Sales/Resources/assets/css/index.css"], "public/build/layouts/reports/details.min.css");

/**
 * Modules/Reports/Resources/views/blockedbalance.blade.php
 */
mix.styles(
    [
        "Modules/Reports/Resources/assets/css/reports.css",
        "resources/modules/global/jquery-daterangepicker/daterangepicker.css",
        "resources/modules/global/css/empty.css",
        "resources/modules/global/css/switch.css",
        "resources/modules/global/css/new-dashboard.css",
        "resources/cdn/select2.min.css",
    ],
    "public/build/layouts/reports/blockedbalance.min.css"
);
mix.babel(
    [
        "Modules/Reports/Resources/assets/js/report-blockedbalance.js",
        "resources/modules/global/js-extra/moment.min.js",
        "resources/modules/global/jquery-daterangepicker/daterangepicker.js",
        "resources/cdn/select2.min.js",
    ],
    "public/build/layouts/reports/blockedbalance.min.js"
);

/**
 * Modules/Reports/Resources/views/coupons.blade.php
 */
mix.styles(
    [
        "resources/modules/global/css/table.css",
        "Modules/Reports/Resources/assets/css/coupons.css",
        "Modules/Reports/Resources/assets/css/reports.css",
        "resources/modules/global/jquery-daterangepicker/daterangepicker.css",
        "resources/modules/global/css/empty.css",
    ],
    "public/build/layouts/reports/coupons.min.css"
);
mix.babel(
    [
        "Modules/Reports/Resources/assets/js/report-coupons.js",
        "resources/modules/global/js-extra/moment.min.js",
        "resources/modules/global/jquery-daterangepicker/daterangepicker.js",
    ],
    "public/build/layouts/reports/coupons.min.js"
);

/**
 * Modules/Reports/Resources/views/new.blade.php
 */
mix.styles(
    [
        "Modules/Reports/Resources/assets/css/chartist.min.css",
        "Modules/Reports/Resources/assets/css/chartist-plugin-tooltip.min.css",
        "Modules/Reports/Resources/assets/css/reports.css",
        "resources/modules/global/css/empty.css",
        "resources/modules/global/jquery-daterangepicker/daterangepicker.css",
    ],
    "public/build/layouts/reports/index.min.css"
);
mix.babel(
    [
        "Modules/Reports/Resources/assets/js/chart-js/Chartjs-3.7-min.js",
        "Modules/Reports/Resources/assets/js/moment.min.js",
        "resources/modules/global/jquery-daterangepicker/daterangepicker.js",
        "Modules/Reports/Resources/assets/js/chartist.min.js",
        "Modules/Reports/Resources/assets/js/chartist-plugin-tooltip.min.js",
        "Modules/Reports/Resources/assets/js/chartist-plugin-legend.min.js",
        "Modules/Reports/Resources/assets/js/chartjs-plugin-datalabels.min.js",
        "Modules/Reports/Resources/assets/js/reports.js",
    ],
    "public/build/layouts/reports/index.min.js"
);

/**
 * Modules/Reports/Resources/views/sales.blade.php
 */
mix.babel(
    [
        "Modules/Reports/Resources/assets/js/chart-js/Chartjs-3.7-min.js",
        "Modules/Reports/Resources/assets/js/moment.min.js",
        "resources/modules/global/jquery-daterangepicker/daterangepicker.js",
        "Modules/Reports/Resources/assets/js/chartist.min.js",
        "Modules/Reports/Resources/assets/js/chartist-plugin-tooltip.min.js",
        "Modules/Reports/Resources/assets/js/chartist-plugin-legend.min.js",
        "Modules/Reports/Resources/assets/js/reports-sales.js",
    ],
    "public/build/layouts/reports/sales.min.js"
);

/**
 * Modules/Reports/Resources/views/finances.blade.php
 */
mix.babel(
    [
        "Modules/Reports/Resources/assets/js/chart-js/Chartjs-3.7-min.js",
        "Modules/Reports/Resources/assets/js/moment.min.js",
        "resources/modules/global/jquery-daterangepicker/daterangepicker.js",
        "Modules/Reports/Resources/assets/js/chartist.min.js",
        "Modules/Reports/Resources/assets/js/chartist-plugin-tooltip.min.js",
        "Modules/Reports/Resources/assets/js/chartist-plugin-legend.min.js",
        "Modules/Reports/Resources/assets/js/reports-finance.js",
    ],
    "public/build/layouts/reports/finances.min.js"
);

/**
 * Modules/Reports/Resources/views/marketing.blade.php
 */
mix.babel(
    [
        "Modules/Reports/Resources/assets/js/chart-js/Chartjs-3.7-min.js",
        "Modules/Reports/Resources/assets/js/moment.min.js",
        "resources/modules/global/jquery-daterangepicker/daterangepicker.js",
        "Modules/Reports/Resources/assets/js/chartist.min.js",
        "Modules/Reports/Resources/assets/js/chartist-plugin-tooltip.min.js",
        "Modules/Reports/Resources/assets/js/chartist-plugin-legend.min.js",
        "Modules/Reports/Resources/assets/js/reports-marketing.js",
    ],
    "public/build/layouts/reports/marketing.min.js"
);

/**
 * Modules/Reports/Resources/views/pending.blade.php
 */
mix.styles(
    [
        "Modules/Reports/Resources/assets/css/reports.css",
        "resources/modules/global/jquery-daterangepicker/daterangepicker.css",
        "resources/modules/global/css/empty.css",
        "resources/modules/global/css/new-dashboard.css",
        "resources/modules/global/css/switch.css",
    ],
    "public/build/layouts/reports/pending.min.css"
);
mix.babel(
    [
        "Modules/Reports/Resources/assets/js/moment.min.js",
        "resources/modules/global/jquery-daterangepicker/daterangepicker.js",
        "Modules/Reports/Resources/assets/js/report-pending.js",
    ],
    "public/build/layouts/reports/pending.min.js"
);

/**
 * Modules/Reports/Resources/views/projections.blade.php
 */
mix.styles(
    [
        "Modules/Reports/Resources/assets/css/chartist.min.css",
        "Modules/Reports/Resources/assets/css/chartist-plugin-tooltip.min.css",
        "Modules/Reports/Resources/assets/css/reports.css",
        "resources/modules/global/css/empty.css",
    ],
    "public/build/layouts/reports/projections.min.css"
);
mix.babel(
    [
        "Modules/Reports/Resources/assets/js/chartist.min.js",
        "Modules/Reports/Resources/assets/js/chartist-plugin-tooltip.min.js",
        "Modules/Reports/Resources/assets/js/chartist-plugin-legend.min.js",
        "Modules/Reports/Resources/assets/js/projections.js",
    ],
    "public/build/layouts/reports/projections.min.js"
);

/**
 * Modules/Sales/Resources/views/details.blade.php
 */
mix.styles(["Modules/Sales/Resources/assets/css/index.css"], "public/build/layouts/sales/details.min.css");
mix.babel(["Modules/Sales/Resources/assets/js/detail.js"], "public/build/layouts/sales/details.min.js");

/**
 * Modules/Sales/Resources/views/index.blade.php
 */
mix.styles(
    [
        "Modules/Sales/Resources/assets/css/index.css",
        "resources/modules/global/css/empty.css",
        "resources/modules/global/css/switch.css",
        "resources/modules/global/css/table.css",
        "resources/modules/global/css/new-dashboard.css",
        "resources/cdn/select2.min.css",
    ],
    "public/build/layouts/sales/index.min.css"
);
mix.babel(
    [
        "Modules/Sales/Resources/assets/js/index.js",
        "resources/modules/global/js-extra/moment.min.js",
        "resources/modules/global/js/daterangepicker.min.js",
        "resources/cdn/select2.min.js",
    ],
    "public/build/layouts/sales/index.min.js"
);

/**
 * Modules/SalesBlackListAntifraud/Resources/views/details.blade.php
 */
mix.styles(
    ["Modules/Sales/Resources/assets/css/index.css"],
    "public/build/layouts/salesblacklistantifraud/details.min.css"
);

/**
 * Modules/SalesBlackListAntifraud/Resources/views/index.blade.php
 */
mix.styles(
    [
        "Modules/SalesBlackListAntifraud/Resources/assets/css/index.css",
        "resources/modules/global/css/empty.css",
        "resources/modules/global/css/switch.css",
        "resources/modules/global/css/new-dashboard.css",
    ],
    "public/build/layouts/salesblacklistantifraud/index.min.css"
);
mix.babel(
    [
        "Modules/SalesBlackListAntifraud/Resources/assets/js/index.js",
        "Modules/SalesBlackListAntifraud/Resources/assets/js/detail.js",
        "resources/modules/global/js-extra/moment.min.js",
        "resources/modules/global/js/daterangepicker.min.js",
    ],
    "public/build/layouts/salesblacklistantifraud/index.min.js"
);

/**
 * Modules/SalesRecovery/Resources/views/index.blade.php
 */
mix.styles(
    [
        "resources/modules/global/css/table.css",
        "Modules/Sales/Resources/assets/css/index.css",
        "Modules/SalesRecovery/Resources/assets/css/index.css",
        "resources/modules/global/css/switch.css",
        "resources/cdn/select2.min.css",
    ],
    "public/build/layouts/salesrecovery/index.min.css"
);
mix.babel(
    [
        "Modules/SalesRecovery/Resources/assets/js/salesrecovery.js",
        "resources/modules/global/js-extra/moment.min.js",
        "resources/modules/global/js/daterangepicker.min.js",
        "resources/cdn/select2.min.js",
    ],
    "public/build/layouts/salesrecovery/index.min.js"
);

/**
 * Modules/Shipping/Resources/views/create.blade.php
 */
mix.styles(["Modules/Shipping/Resources/assets/css/create.css"], "public/build/layouts/shipping/create.min.css");

/**
 * Modules/Shipping/Resources/views/edit.blade.php
 */
mix.styles(["Modules/Shipping/Resources/assets/css/edit.css"], "public/build/layouts/shipping/edit.min.css");

/**
 * Modules/Shopify/Resources/views/index.blade.php
 */
mix.styles(
    [
        "Modules/Shopify/Resources/assets/css/index.css",
        "resources/modules/global/css/empty.css",
        "resources/modules/global/css/switch.css",
    ],
    "public/build/layouts/shopify/index.min.css"
);
mix.babel(["Modules/Shopify/Resources/assets/js/index.js"], "public/build/layouts/shopify/index.min.js");

/**
 * Modules/Smartfunnel/Resources/views/index.blade.php
 */
mix.styles(["resources/modules/digitalmanager/css/index.css"], "public/build/layouts/smartfunnel/index.min.css");
mix.babel(["Modules/Smartfunnel/Resources/assets/js/index.js"], "public/build/layouts/smartfunnel/index.min.js");

/**
 * Modules/Trackings/Resources/views/index.blade.php
 */
mix.styles(
    [
        "resources/modules/global/css/empty.css",
        "resources/modules/global/css/switch.css",
        "resources/modules/global/css/new-dashboard.css",
        "resources/modules/global/css/table.css",
        "Modules/Trackings/Resources/assets/css/index.css",
        "resources/cdn/select2.min.css",
    ],
    "public/build/layouts/trackings/index.min.css"
);
mix.babel(
    [
        "resources/modules/global/js-extra/moment.min.js",
        "resources/modules/global/js/daterangepicker.min.js",
        "Modules/Trackings/Resources/assets/js/index.js",
        "resources/cdn/select2.min.js",
        "resources/cdn/chart.js",
    ],
    "public/build/layouts/trackings/index.min.js"
);
mix.copy("Modules/Trackings/Resources/assets/svg", "public/build/layouts/trackings/svg");
/**
 * Modules/Unicodrop/Resources/views/index.blade.php
 */
mix.styles(["resources/modules/digitalmanager/css/index.css"], "public/build/layouts/unicodrop/index.min.css");
mix.babel(["Modules/Unicodrop/Resources/assets/js/index.js"], "public/build/layouts/unicodrop/index.min.js");

/**
 * Modules/Webhooks/Resources/views/index.blade.php
 */
mix.styles(
    ["resources/modules/global/css/new-dashboard.css", "resources/modules/global/css/empty.css"],
    "public/build/layouts/webhooks/index.min.css"
);
mix.babel(["Modules/Webhooks/Resources/assets/js/index.js"], "public/build/layouts/webhooks/index.min.js");

/**
 * Modules/Whatsapp2/Resources/views/index.blade.php
 */
mix.styles(["resources/modules/digitalmanager/css/index.css"], "public/build/layouts/whatsapp2/index.min.css");
mix.babel(["Modules/Whatsapp2/Resources/assets/js/index.js"], "public/build/layouts/whatsapp2/index.min.js");

/**
 * Modules/WooCommerce/Resources/views/index.blade.php
 */
mix.styles(["resources/modules/global/css/empty.css"], "public/build/layouts/wooCommerce/index.min.css");
mix.babel(["Modules/WooCommerce/Resources/assets/js/index.js"], "public/build/layouts/wooCommerce/index.min.js");
mix.copy("Modules/WooCommerce/Resources/assets/plugins", "public/build/layouts/woocommerce/plugins");
/**
 * resources/assets/modules/finances/js/index.js
 */
mix.babel(
    ["Modules/Withdrawals/Resources/assets/js/index.js"],
    "public/build/layouts/finances/withdrawal-index.min.js"
);

/**
 * Modules/Nuvemshop/Resources/views/index.blade.php
 */
mix.styles(
    [
        "Modules/Nuvemshop/Resources/assets/css/index.css",
        "resources/modules/global/css/empty.css",
        "resources/modules/global/css/switch.css",
    ],
    "public/build/layouts/nuvemshop/index.min.css"
);
mix.babel(["Modules/Nuvemshop/Resources/assets/js/index.js"], "public/build/layouts/nuvemshop/index.min.js");

mix.styles(
    [
        "resources/modules/global/css/sweetalert2.min.css",
        "Modules/Nuvemshop/Resources/assets/css/finalize-integration.css",
    ],
    "public/build/layouts/nuvemshop/finalize-integration.min.css"
);
mix.babel(
    [
        "resources/modules/global/js-extra/sweetalert2.all.min.js",
        "Modules/Nuvemshop/Resources/assets/js/finalize-integration.js",
    ],
    "public/build/layouts/nuvemshop/finalize-integration.min.js"
);

/**
 * resources/views/layouts/master.blade
 */
mix.copy(["resources/modules/global/adminremark/global/fonts/web-icons/!(*.css)"], "public/build/layouts/master");
mix.copy(["resources/modules/global/adminremark/global/fonts/font-awesome/!(*.css)"], "public/build/layouts/master");
mix.copy(
    ["resources/modules/global/adminremark/global/fonts/orion-icons/!(*.css|*.html)"],
    "public/build/layouts/master"
);

/**
 * resources/views/layouts/auth.blade.php
 */
mix.styles(
    [
        "resources/modules/global/adminremark/assets/css/bootstrap.min.css",
        "resources/modules/global/adminremark/assets/css/new-login.css",
        "resources/modules/global/css/sweetalert2.min.css",
        "resources/modules/global/css/loading.css",
    ],
    "public/build/layouts/auth/auth.min.css"
);
mix.babel(
    [
        "resources/modules/global/adminremark/global/vendor/breakpoints/breakpoints.js",
        "resources/modules/global/js-extra/sweetalert2.all.min.js",
        "resources/modules/global/js/global.js",
    ],
    "public/build/layouts/auth/auth.min.js"
);

mix.styles(
    [
        // Stylesheets
        "resources/modules/global/css/normalize.css",
        "resources/modules/global/adminremark/global/css/bootstrap.min.css",
        "resources/modules/global/adminremark/global/css/bootstrap-extend.min.css",
        "resources/modules/global/adminremark/assets/css/site.css",
        "resources/modules/global/css/loading.css",
        "resources/modules/global/css/checkAnimation.css",
        "resources/modules/global/css/ribbon.css",
        // Plugins
        "resources/modules/global/adminremark/global/vendor/animsition/animsition.css",
        "resources/modules/global/css/placeholder-loading.min.css",
        "resources/modules/global/jquery-imgareaselect/css/imgareaselect-default.css",
        "resources/modules/global/css/sweetalert2.min.css",
        "resources/modules/global/css/daterangepicker.css",
        "resources/modules/global/adminremark/global/vendor/sortable/sortable.css",
        // Fonts
        "resources/modules/global/adminremark/global/fonts/web-icons/web-icons.css",
        "resources/modules/global/adminremark/global/fonts/font-awesome/font-awesome.css",
        "resources/modules/global/css/newFonts.css",
        // Icons
        "resources/modules/fonts-googleapis-material-icons.css",
        "resources/modules/global/css/materialdesignicons.min.css",
        "resources/modules/global/adminremark/global/fonts/orion-icons/iconfont.css",
        // New CSS
        "resources/modules/global/css/new-site.css",
        // Bonus Balance Donut Progress
        "resources/modules/global/css/mk_charts.css",
        "resources/modules/global/css/global.css",
        "resources/modules/global/adminremark/global/vendor/asscrollable/asScrollable.css",
    ],
    "public/build/layouts/master/master.min.css"
);

mix.babel(
    [
        "resources/modules/global/adminremark/global/vendor/babel-external-helpers/babel-external-helpers.js",
        "resources/modules/global/adminremark/global/vendor/popper-js/umd/popper.min.js",
        "resources/modules/global/adminremark/global/vendor/bootstrap/bootstrap.js",
        "resources/modules/global/adminremark/global/vendor/animsition/animsition.js",
        "resources/modules/global/adminremark/global/vendor/matchheight/jquery.matchHeight-min.js",
        "resources/modules/global/js-extra/jquery.mask.min.js",
        "resources/modules/global/js-extra/jquery.maskMoney.js",
        "resources/modules/global/js-extra/sweetalert2.all.min.js",
        "resources/modules/global/js-extra/crypto-js.min.js",
        "resources/modules/global/adminremark/global/js/Component.js",
        "resources/modules/global/adminremark/global/js/Plugin.js",
        "resources/modules/global/adminremark/global/js/Base.js",
        "resources/modules/global/adminremark/global/js/Config.js",
        "resources/modules/global/adminremark/assets/js/Section/Menubar.js",
        "resources/modules/global/adminremark/assets/js/Section/Sidebar.js",
        "resources/modules/global/adminremark/assets/js/Section/PageAside.js",
        "resources/modules/global/adminremark/assets/js/Section/GridMenu.js",
        "resources/modules/global/adminremark/assets/js/Site.js",
        "resources/modules/global/adminremark/assets/examples/js/dashboard/v1.js",
        "resources/modules/global/adminremark/global/vendor/sortable/Sortable.js",
        "resources/modules/global/jquery-imgareaselect/scripts/jquery.imgareaselect.pack.js",
        "resources/modules/global/js/global.js",
        "resources/modules/global/adminremark/global/vendor/asscrollbar/jquery-asScrollbar.js",
        "resources/modules/global/adminremark/global/vendor/asscrollable/jquery-asScrollable.js",
        "resources/modules/global/js/mk_charts.js",
    ],
    "public/build/layouts/master/plugins.min.js"
);
mix.babel(
    ["resources/modules/global/js-extra/pusher.min.js", "resources/modules/global/js/notifications.js"],
    "public/build/layouts/master/production.min.js"
);
mix.babel(
    [
        "resources/modules/global/adminremark/global/vendor/jquery/jquery.min.js",
        "resources/modules/global/adminremark/global/vendor/breakpoints/breakpoints.js",
    ],
    "public/build/layouts/master/master.min.js"
);
mix.copy("resources/modules/global/js-extra/sentry-bundle.min.js", "public/build/layouts/master/sentry-bundle.min.js");

mix.copy(
    "resources/modules/global/jquery-imgareaselect/css/border-anim-h.gif",
    "public/build/layouts/master/border-anim-h.gif"
);
mix.copy(
    "resources/modules/global/jquery-imgareaselect/css/border-anim-v.gif",
    "public/build/layouts/master/border-anim-v.gif"
);
mix.copy("resources/modules/global/jquery-imgareaselect/css/border-h.gif", "public/build/layouts/master/border-h.gif");
mix.copy("resources/modules/global/jquery-imgareaselect/css/border-v.gif", "public/build/layouts/master/border-v.gif");

/**
 * assets genericos
 */

mix.copy("resources/modules/global/adminremark/assets/images", "public/build/global/adminremark/assets/images");

// global / css / font
mix.copy("resources/modules/global/css/font", "public/build/global/css/font");

// global / img
mix.copy("resources/modules/global/img", "public/build/global/img");
mix.copy("resources/modules/global/img/gateways", "public/build/global/img/gateways");
mix.copy("resources/modules/global/img/cartoes", "public/build/global/img/cartoes");
mix.copy("resources/modules/global/img/custom-product", "public/build/global/img/custom-product");
mix.copy("resources/modules/global/img/icon-red", "public/build/global/img/icon-red");
mix.copy("resources/modules/global/img/logos", "public/build/global/img/logos");
mix.copy("resources/modules/global/img/logos/2021", "public/build/global/img/logos/2021");
mix.copy("resources/modules/global/img/logos/2021/favicon", "public/build/global/img/logos/2021/favicon");
mix.copy("resources/modules/global/img/logos/2021/svg", "public/build/global/img/logos/2021/svg");
mix.copy("resources/modules/global/img/onboarding", "public/build/global/img/onboarding");
mix.copy("resources/modules/global/img/pix", "public/build/global/img/pix");
mix.copy("resources/modules/global/img/projects", "public/build/global/img/projects");
mix.copy("resources/modules/global/img/reports", "public/build/global/img/reports");
mix.copy("resources/modules/global/img/svg", "public/build/global/img/svg");

if (mix.inProduction()) {
    mix.version();
}

const proxy = process.env.APP_URL || "localhost:8080";
mix.browserSync(proxy);
