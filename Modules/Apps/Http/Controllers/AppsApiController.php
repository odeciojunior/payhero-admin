<?php

namespace Modules\Apps\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Core\Entities\ActivecampaignIntegration;
use Modules\Core\Entities\ApiToken;
use Modules\Core\Entities\AstronMembersIntegration;
use Modules\Core\Entities\ConvertaxIntegration;
use Modules\Core\Entities\DigitalmanagerIntegration;
use Modules\Core\Entities\HotbilletIntegration;
use Modules\Core\Entities\HotzappIntegration;
use Modules\Core\Entities\MelhorenvioIntegration;
use Modules\Core\Entities\NotazzIntegration;
use Modules\Core\Entities\NotificacoesInteligentesIntegration;
use Modules\Core\Entities\NuvemshopIntegration;
use Modules\Core\Entities\ReportanaIntegration;
use Modules\Core\Entities\ShopifyIntegration;
use Modules\Core\Entities\SmartfunnelIntegration;
use Modules\Core\Entities\UnicodropIntegration;
use Modules\Core\Entities\UtmifyIntegration;
use Modules\Core\Entities\WebhookTracking;
use Modules\Core\Entities\Whatsapp2Integration;
use Modules\Core\Entities\WooCommerceIntegration;

class AppsApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $accountOwnerId = auth()
            ->user()
            ->getAccountOwnerId();
        $company_default = auth()->user()->company_default;
        return response()->json([
            "hotzappIntegrations" => HotzappIntegration::join(
                "checkout_configs as cc",
                "cc.project_id",
                "=",
                "hotzapp_integrations.project_id",
            )
                ->where([["user_id", $accountOwnerId], ["cc.company_id", $company_default]])
                ->count(),

            "shopifyIntegrations" => ShopifyIntegration::join(
                "checkout_configs as cc",
                "cc.project_id",
                "=",
                "shopify_integrations.project_id",
            )
                ->where([["user_id", $accountOwnerId], ["cc.company_id", $company_default]])
                ->count(),

            "notazzIntegrations" => NotazzIntegration::join(
                "checkout_configs as cc",
                "cc.project_id",
                "=",
                "notazz_integrations.project_id",
            )
                ->where([["user_id", $accountOwnerId], ["cc.company_id", $company_default]])
                ->count(),

            "convertaxIntegrations" => ConvertaxIntegration::join(
                "checkout_configs as cc",
                "cc.project_id",
                "=",
                "convertax_integrations.project_id",
            )
                ->where([["user_id", $accountOwnerId], ["cc.company_id", $company_default]])
                ->count(),

            "activecampaignIntegrations" => ActivecampaignIntegration::join(
                "checkout_configs as cc",
                "cc.project_id",
                "=",
                "activecampaign_integrations.project_id",
            )
                ->where([["user_id", $accountOwnerId], ["cc.company_id", $company_default]])
                ->count(),

            "digitalmanagerIntegrations" => DigitalmanagerIntegration::join(
                "checkout_configs as cc",
                "cc.project_id",
                "=",
                "digitalmanager_integrations.project_id",
            )
                ->where([["user_id", $accountOwnerId], ["cc.company_id", $company_default]])
                ->count(),

            "whatsapp2Integrations" => Whatsapp2Integration::join(
                "checkout_configs as cc",
                "cc.project_id",
                "=",
                "whatsapp2_integrations.project_id",
            )
                ->where([["user_id", $accountOwnerId], ["cc.company_id", $company_default]])
                ->count(),

            "reportanaIntegrations" => ReportanaIntegration::join(
                "checkout_configs as cc",
                "cc.project_id",
                "=",
                "reportana_integrations.project_id",
            )
                ->where([["user_id", $accountOwnerId], ["cc.company_id", $company_default]])
                ->count(),

            "unicodropIntegrations" => UnicodropIntegration::join(
                "checkout_configs as cc",
                "cc.project_id",
                "=",
                "unicodrop_integrations.project_id",
            )
                ->where([["user_id", $accountOwnerId], ["cc.company_id", $company_default]])
                ->count(),

            "smartfunnelIntegrations" => SmartfunnelIntegration::join(
                "checkout_configs as cc",
                "cc.project_id",
                "=",
                "smartfunnel_integrations.project_id",
            )
                ->where([["user_id", $accountOwnerId], ["cc.company_id", $company_default]])
                ->count(),

            "woocommerceIntegrations" => WooCommerceIntegration::join(
                "checkout_configs as cc",
                "cc.project_id",
                "=",
                "woo_commerce_integrations.project_id",
            )
                ->where([["user_id", $accountOwnerId], ["cc.company_id", $company_default]])
                ->count(),

            "astronmembersIntegrations" => AstronMembersIntegration::join(
                "checkout_configs as cc",
                "cc.project_id",
                "=",
                "astron_members_integrations.project_id",
            )
                ->where([["user_id", $accountOwnerId], ["cc.company_id", $company_default]])
                ->count(),

            "geradorrastreioIntegrations" => WebhookTracking::join(
                "checkout_configs as cc",
                "cc.project_id",
                "=",
                "webhook_trackings.project_id",
            )
                ->where([["user_id", $accountOwnerId], ["cc.company_id", $company_default]])
                ->count(),

            "notificacoesinteligentesIntegrations" => NotificacoesInteligentesIntegration::join(
                "checkout_configs as cc",
                "cc.project_id",
                "=",
                "notificacoes_inteligentes_integrations.project_id",
            )
                ->where([["user_id", $accountOwnerId], ["cc.company_id", $company_default]])
                ->count(),

            "hotbilletIntegrations" => HotbilletIntegration::join(
                "checkout_configs as cc",
                "cc.project_id",
                "=",
                "hotbillet_integrations.project_id",
            )
                ->where([["user_id", $accountOwnerId], ["cc.company_id", $company_default]])
                ->count(),

            "melhorenvioIntegrations" => MelhorenvioIntegration::where("user_id", $accountOwnerId)->count(),

            "utmifyIntegrations" => UtmifyIntegration::where("user_id", $accountOwnerId)->count(),

            "vegacheckoutIntegrations" => ApiToken::where([
                ["description", "Vega_Checkout"],
                ["company_id", $company_default],
            ])->count(),

            "adooreicheckoutIntegrations" => ApiToken::where([
                ["description", "Adoorei_Checkout"],
                ["company_id", $company_default],
            ])->count(),

            "nuvemshopIntegrations" => NuvemshopIntegration::join(
                "checkout_configs as cc",
                "cc.project_id",
                "=",
                "nuvemshop_integrations.project_id",
            )
                ->where("nuvemshop_integrations.user_id", $accountOwnerId)
                ->where("cc.company_id", $company_default)
                ->count(),
        ]);
    }
}
