<?php

namespace Modules\Core\Services;

use Illuminate\Support\Facades\Gate;
use Modules\Core\Entities\Pixel;
use Modules\Core\Entities\Project;

class PixelService
{
    public function store($projectId, $dataValidated): array
    {
        if (!empty($dataValidated['affiliate_id'])) {
            $dataValidated['affiliate_id'] = hashids_decode($dataValidated['affiliate_id']);
            $affiliateId = $dataValidated['affiliate_id'];
        } else {
            $affiliateId = 0;
            $dataValidated['affiliate_id'] = null;
        }

        $project = Project::find(hashids_decode($projectId));

        if (!Gate::allows('edit', [$project, $affiliateId])) {
            return [
                'status' => 403,
                'message' => __('controller.pixel.permission.create')
            ];
        }

        $applyPlanEncoded = json_encode(foxutils()->getApplyPlans($dataValidated['add_pixel_plans']));

        if ($dataValidated['platform'] == 'google_adwords') {
            $dataValidated['code'] = str_replace(['AW-'], '', $dataValidated['code']);
        }

        if (in_array(
                $dataValidated['platform'],
                ['taboola', 'outbrain']
            ) && empty($dataValidated['purchase-event-name'])) {
            $dataValidated['purchase-event-name'] = $dataValidated['platform'] == 'taboola' ? 'make_purchase' : 'Purchase';
        }

        if (!in_array($dataValidated['platform'], ['taboola', 'outbrain'])) {
            $dataValidated['purchase-event-name'] = null;
        }

        $facebookToken = null;
        $isApi = false;
        if ($dataValidated['platform'] == 'facebook' && !empty($dataValidated['api-facebook']) && $dataValidated['api-facebook'] == 'api') {
            $facebookToken = $dataValidated['facebook-token-api'];
            $isApi = true;
        }

        if (empty($dataValidated['value_percentage_purchase_boleto'])) {
            $dataValidated['value_percentage_purchase_boleto'] = 100;
        }

        Pixel::create(
            [
                'project_id' => $project->id,
                'name' => $dataValidated['name'],
                'code' => $dataValidated['code'],
                'platform' => $dataValidated['platform'],
                'status' => (bool)$dataValidated['status'],
                'checkout' => $dataValidated['checkout'] == 'true',
                'purchase_boleto' => $dataValidated['purchase_boleto'] == 'true',
                'purchase_card' => $dataValidated['purchase_card'] == 'true',
                'purchase_pix' => $dataValidated['purchase_pix'] == 'true',
                'affiliate_id' => $dataValidated['affiliate_id'],
                'campaign_id' => $dataValidated['campaign'] ?? null,
                'apply_on_plans' => $applyPlanEncoded,
                'purchase_event_name' => $dataValidated['purchase-event-name'],
                'facebook_token' => $facebookToken,
                'is_api' => $isApi,
                'value_percentage_purchase_boleto' => $dataValidated['value_percentage_purchase_boleto'],
            ]
        );

        return [
            'status' => 200,
            'message' => 'Pixel ' . __('controller.success.create')
        ];
    }
}