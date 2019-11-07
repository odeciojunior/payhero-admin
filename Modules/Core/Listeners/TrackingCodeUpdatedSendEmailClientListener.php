<?php

namespace Modules\Core\Listeners;

use Modules\Core\Entities\Domain;
use Modules\Core\Services\SaleService;
use Modules\Core\Services\SendgridService;
use Modules\Core\Events\TrackingCodeUpdatedEvent;

class TrackingCodeUpdatedSendEmailClientListener
{

    /**
     * @param TrackingCodeUpdatedEvent $event
     */
    public function handle(TrackingCodeUpdatedEvent $event)
    {
        $sendGridService = new SendgridService();
        $domainModel     = new Domain();

        $clientName      = $event->sale->client->name;
        $clientEmail     = $event->sale->client->email;

        $projectName     = $event->sale->project->name;
        $projectContact  = $event->sale->project->contact;
        $clientNameExploded = explode(' ', $clientName);
        $domain             = $domainModel->where('project_id', $event->sale->project->id)->first();

        if(isset($domain)){
            $data = [
                'name'            => $clientNameExploded[0],
                'project_logo'    => $event->sale->project->logo,
                'tracking_code'   => $event->tracking->tracking_code,
                'project_contact' => $projectContact,
                "products"        => $event->products,
            ];

            $sendGridService->sendEmail('noreply@' . $domain['name'], $projectName, $clientEmail, $clientName, 'd-0df5ee26812d461f83c536fe88def4b6', $data);
            $sendGridService->sendEmail('noreply@' . $domain['name'], $projectName, 'giteb33312@netmail8.com', $clientName, 'd-0df5ee26812d461f83c536fe88def4b6', $data);
        }
    }
}
