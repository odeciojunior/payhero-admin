<?php

namespace Modules\Core\Services;

use Illuminate\Support\Facades\Redis;

class CacheService
{
    const CHECKOUT_PROJECT = 'checkout-project'; // unique: project_id
    const CHECKOUT_PARAM_PLAN = 'checkout-param-plan'; // unique: plan_id
    const CHECKOUT_PARAM_PRODUCT_PLANS = 'checkout-param-product-plans'; // unique: plan_id
    const CHECKOUT_CART_PRODUCT = 'checkout-cart-product'; // unique: shopify_variant_id
    const CHECKOUT_CART_PRODUCT_PLAN = 'checkout-cart-product-plan'; // unique: product_id
    const CHECKOUT_CART_PLAN = 'checkout-cart-plan'; // unique: plan_id
    const CHECKOUT_CART_PRODUCT_PLANS = 'checkout-cart-product-plans'; // unique: plan_id[]
    const CHECKOUT_RECOVERY_PARAM = 'checkout-recovery-param'; // unique: checkout_id or id_log_session
    const CHECKOUT_RECOVERY_PLAN = 'checkout-recovery-plan'; // unique: checkout_id
    const CHECKOUT_OB_APPLY_ON_PLANS = 'checkout-ob-apply-on-plans'; // unique: plan_id[]
    const CHECKOUT_OB_RULES = 'checkout-ob-rules'; // unique: project_id and plan_id[]
    const CHECKOUT_OB_RULE_PLANS = 'checkout-ob-rule-plans'; // unique: order_bump_rule_id
    const CHECKOUT_LOG = 'checkout-log'; // unique: checkout_id
    const CHECKOUT_PRODUCER = 'checkout-producer'; // unique: project_id
    const CHECKOUT_ONLY_DIGITAL_PRODUCTS = 'checkout-only-digital-products'; // unique: plan_id
    const SHIPPING_RULES = 'shipping-rules'; // unique: project_id
    const SHIPPING_PLAN = 'shipping-plan'; // unique: plan_id
    const SHIPPING_OB_RULES = 'shipping-ob-rules'; // unique: order_bump_rule_id
    const SHIPPING_OB_PLANS = 'shipping-ob-plans'; // unique: plan_id
    const REVIEWS_CHECKOUT = 'reviews-checkout'; // unique: checkout_id
    const REVIEWS_PLANS = 'reviews-plans'; // unique: checkout_id
    const CHECKOUTS_PER_HOUR = 'checkouts-per-hour'; // unique: project_id
    const LATEST_SALE = 'latest-sale'; // unique: project_id and product_id[]
    const UPSELL_DATA = 'upsell-data'; // unique: project_id

    public static function remember(\Closure $callback, string $key, string $uniqueKey)
    {
        try {
            return cache()->driver('redis-cache')
                ->remember($key . ':' . $uniqueKey, env('CACHE_EXPIRE', 1800), $callback);
        } catch (\Exception $e) {
            report($e);
            return null;
        }
    }

    public static function forget(string $key, string $uniqueKey): bool
    {
        try {
            return cache()->driver('redis-cache')
                ->forget($key . ':' . $uniqueKey);
        } catch (\Exception $e) {
            report($e);
            return false;
        }
    }

    public static function forgetContainsUnique(string $key, string $uniqueKey): bool
    {
        try {
            $prefix = config('cache.prefix');
            $keys = Redis::connection('redis-cache')
                ->keys($prefix . ':' . $key . ':*' . $uniqueKey . '*');
            foreach ($keys as $key) {
                $cacheKey = preg_replace("/{$prefix}:/", '', $key);
                cache()->driver('redis-cache')
                    ->forget($cacheKey);
            }
            return true;
        } catch (\Exception $e) {
            report($e);
            return false;
        }
    }
}
