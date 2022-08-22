<?php

namespace Modules\DemoAccount\Http\Controllers;

use Vinkla\Hashids\Facades\Hashids;
use Spatie\Activitylog\Models\Activity;
use Modules\Core\Entities\ActivecampaignIntegration;
use Modules\ActiveCampaign\Transformers\ActivecampaignResource;
use Modules\ActiveCampaign\Http\Controllers\ActiveCampaignApiController;

class ActiveCampaignApiDemoController extends ActiveCampaignApiController
{
    public function show($id)
    {
        $activecampaignIntegrationModel = new ActivecampaignIntegration();

        // activity()->on($activecampaignIntegrationModel)->tap(function(Activity $activity) use ($id) {
        //     $activity->log_name   = 'visualization';
        //     $activity->subject_id = current(Hashids::decode($id));
        // })->log('Visualizou tela configuração ActiveCampaign');

        $activecampaignIntegration = $activecampaignIntegrationModel->find(current(Hashids::decode($id)));

        return new ActivecampaignResource($activecampaignIntegration);
    }
}
