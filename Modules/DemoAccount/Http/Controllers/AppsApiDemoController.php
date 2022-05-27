<?php

namespace Modules\DemoAccount\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Modules\Core\Entities\ActivecampaignIntegration;
use Modules\Core\Entities\AstronMembersIntegration;
use Modules\Core\Entities\ConvertaxIntegration;
use Modules\Core\Entities\DigitalmanagerIntegration;
use Modules\Core\Entities\HotzappIntegration;
use Modules\Core\Entities\NotazzIntegration;
use Modules\Core\Entities\ReportanaIntegration;
use Modules\Core\Entities\ShopifyIntegration;
use Modules\Core\Entities\SmartfunnelIntegration;
use Modules\Core\Entities\UnicodropIntegration;
use Modules\Core\Entities\Whatsapp2Integration;
use Modules\Core\Entities\HotbilletIntegration;
use Modules\Core\Entities\WooCommerceIntegration;
use Modules\Core\Entities\MelhorenvioIntegration;
use Modules\Apps\Http\Controllers\AppsApiController;
use Modules\Core\Entities\User;

class AppsApiDemoController extends AppsApiController
{
    public function index(): JsonResponse
    {
        $accountOwnerId = User::DEMO_ID;
        
        return response()->json([
            'hotzappIntegrations' => HotzappIntegration::where('user_id', $accountOwnerId)->count(),
            'shopifyIntegrations' => ShopifyIntegration::where('user_id', $accountOwnerId)->count(),
            'notazzIntegrations' => NotazzIntegration::where('user_id', $accountOwnerId)->count(),
            'convertaxIntegrations' => ConvertaxIntegration::where('user_id', $accountOwnerId)->count(),
            'activecampaignIntegrations' => ActivecampaignIntegration::where('user_id', $accountOwnerId)->count(),
            'digitalmanagerIntegrations' => DigitalmanagerIntegration::where('user_id', $accountOwnerId)->count(),
            'whatsapp2Integrations' => Whatsapp2Integration::where('user_id', $accountOwnerId)->count(),
            'reportanaIntegrations' => ReportanaIntegration::where('user_id', $accountOwnerId)->count(),
            'unicodropIntegrations' => UnicodropIntegration::where('user_id', $accountOwnerId)->count(),
            'smartfunnelIntegrations' => SmartfunnelIntegration::where('user_id', $accountOwnerId)->count(),
            'woocommerceIntegrations' => WooCommerceIntegration::where('user_id', $accountOwnerId)->count(),
            'astronmembersIntegrations' => AstronMembersIntegration::where('user_id', $accountOwnerId)->count(),
            'hotbilletIntegrations' => HotbilletIntegration::where('user_id', $accountOwnerId)->count(),
            'melhorenvioIntegrations' => MelhorenvioIntegration::where('user_id', $accountOwnerId)->count(),
        ]);
    }
}
