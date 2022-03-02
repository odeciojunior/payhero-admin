<?php

use  \Modules\Core\Entities\CheckoutConfig;

return [
    'enum' => [
        'type' => [
            CheckoutConfig::CHECKOUT_TYPE_THREE_STEPS => 'Checkout de 3 passos',
            CheckoutConfig::CHECKOUT_TYPE_ONE_STEP => 'Checkout único',
        ],
        'banner_type' => [
            CheckoutConfig::CHECKOUT_BANNER_TYPE_FULL => 'Banner em tela cheia',
            CheckoutConfig::CHECKOUT_BANNER_TYPE_CENTER => 'Banner centralizado',
        ],
        'theme' => [
            CheckoutConfig::CHECKOUT_THEME_SPACESHIP => 'Spaceship',
            CheckoutConfig::CHECKOUT_THEME_PURPLE_SPACE => 'Purple Space',
            CheckoutConfig::CHECKOUT_THEME_CLOUD_STD => 'Cloud Std',
            CheckoutConfig::CHECKOUT_THEME_SUNNY_DAY => 'Sunny Day',
            CheckoutConfig::CHECKOUT_THEME_BLUE_SKY => 'Blue Sky',
            CheckoutConfig::CHECKOUT_THEME_ALL_BLACK => 'All Black',
            CheckoutConfig::CHECKOUT_THEME_RED_MARS => 'Red Mars',
            CheckoutConfig::CHECKOUT_THEME_PINK_GALAXY => 'Pink Galaxy',
            CheckoutConfig::CHECKOUT_THEME_TURQUOISE => 'Turquoise',
            CheckoutConfig::CHECKOUT_THEME_GREENER => 'Greener',
        ],
    ]
];
