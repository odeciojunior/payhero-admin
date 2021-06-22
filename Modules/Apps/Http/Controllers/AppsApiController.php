<?php

namespace Modules\Apps\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Core\Entities\MelhorenvioIntegration;
use Modules\Core\Entities\NotazzIntegration;
use Modules\Core\Entities\HotzappIntegration;
use Modules\Core\Entities\ShopifyIntegration;
use Modules\Core\Entities\ConvertaxIntegration;
use Modules\Core\Entities\ActivecampaignIntegration;
use Modules\Core\Entities\DigitalmanagerIntegration;
use Modules\Core\Entities\UnicodropIntegration;
use Modules\Core\Entities\Whatsapp2Integration;
use Modules\Core\Entities\HotsacIntegration;
use Modules\Core\Entities\ReportanaIntegration;
use Modules\Core\Entities\SmartfunnelIntegration;

class AppsApiController extends Controller
{
    /**
     * Count of integrations.
     * @return Response
     */
    public function index()
    {
        $hotzappIntegrationModel        = new HotzappIntegration();
        $shopifyIntegration             = new ShopifyIntegration();
        $notazzIntegration              = new NotazzIntegration();
        $convertaxIntegrationModel      = new ConvertaxIntegration();
        $activecampaignIntegrationModel = new ActivecampaignIntegration();
        $digitalmanagerIntegrationModel = new DigitalmanagerIntegration();
        $whatsapp2IntegrationModel      = new Whatsapp2Integration();
        $hotsacIntegrationModel         = new HotsacIntegration();
        $reportanaIntegrationModel      = new ReportanaIntegration();
        $unicodropIntegrationModel      = new UnicodropIntegration();
        $smartfunnelIntegrationModel    = new SmartfunnelIntegration();
        $melhorenvioIntegrationModel    = new MelhorenvioIntegration();


        return response()->json([
            'hotzappIntegrations'        => $hotzappIntegrationModel->where('user_id', auth()->user()->account_owner_id)->count(),
            'shopifyIntegrations'        => $shopifyIntegration->where('user_id', auth()->user()->account_owner_id)->count(),
            'notazzIntegrations'         => $notazzIntegration->where('user_id', auth()->user()->account_owner_id)->count(),
            'convertaxIntegrations'      => $convertaxIntegrationModel->where('user_id', auth()->user()->account_owner_id)->count(),
            'activecampaignIntegrations' => $activecampaignIntegrationModel->where('user_id', auth()->user()->account_owner_id)->count(),
            'digitalmanagerIntegrations' => $digitalmanagerIntegrationModel->where('user_id', auth()->user()->account_owner_id)->count(),
            'whatsapp2Integrations'      => $whatsapp2IntegrationModel->where('user_id', auth()->user()->account_owner_id)->count(),
            'hotsacIntegrations'         => $hotsacIntegrationModel->where('user_id', auth()->user()->account_owner_id)->count(),
            'reportanaIntegrations'      => $reportanaIntegrationModel->where('user_id', auth()->user()->account_owner_id)->count(),
            'unicodropIntegrations'      => $unicodropIntegrationModel->where('user_id', auth()->user()->account_owner_id)->count(),
            'smartfunnelIntegrations'    => $smartfunnelIntegrationModel->where('user_id', auth()->user()->account_owner_id)->count(),
            'melhorenvioIntegrations'    => $melhorenvioIntegrationModel->where('user_id', auth()->user()->account_owner_id)->count(),
          ]);
    }

}
