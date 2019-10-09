<?php

namespace Modules\Core\Listeners;

use Modules\Core\Entities\Domain;
use Illuminate\Queue\InteractsWithQueue;
use Modules\Core\Services\SaleService;
use Modules\Core\Services\SendgridService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Core\Events\TrackingCodeUpdatedEvent;

class TrackingCodeUpdatedSendEmailClientListener
{

    /**
     * @param TrackingCodeUpdatedEvent $event
     * @throws \Laracasts\Presenter\Exceptions\PresenterException
     */
    public function handle(TrackingCodeUpdatedEvent $event)
    {
        $sendGridService = new SendgridService();
        $saleService     = new SaleService();
        $domainModel     = new Domain();
        $clientName      = $event->sale->client->name;
        $clientEmail     = $event->sale->client->email;

        $projectName     = $event->sale->project->name;
        $projectContact  = $event->sale->project->contact;
        $clientNameExploded = explode(' ', $clientName);
        $domain             = $domainModel->where('project_id', $event->sale->project->id)->first();
        $products           = $saleService->getProducts($event->sale->id);
        $data = [
            'name'            => $clientNameExploded[0],
            'project_logo'    => $event->sale->project->logo,
            'tracking_code'   => $event->sale->delivery()->first()->tracking_code,
            'project_contact' => $projectContact,
            "products"        => $products,
        ];
        if (getenv('APP_ENV') != 'local') {
            $sendGridService->sendEmail('noreply@' . $domain['name'], $projectName, $clientEmail, $clientName, 'd-0df5ee26812d461f83c536fe88def4b6', $data);
        }
    }
}
