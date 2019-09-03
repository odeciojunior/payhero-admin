<?php

namespace Modules\Core\Listeners;

use App\Entities\Domain;
use App\Entities\Plan;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Core\Events\TrackingCodeUpdatedEvent;
use Modules\Core\Services\SendgridService;

class TrackingCodeUpdatedSendEmailClientListener
{
    /**
     * Create the event listener.
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * @param TrackingCodeUpdatedEvent $event
     * @throws \Laracasts\Presenter\Exceptions\PresenterException
     */
    public function handle(TrackingCodeUpdatedEvent $event)
    {
        $sendGridService = new SendgridService();
        $domainModel     = new Domain();
        $clientName      = $event->sale->clientModel->name;
        $clientEmail     = $event->sale->clientModel->email;
        $projectName     = $event->sale->projectModel->name;
        $projectContact  = $event->sale->projectModel->contact;

        $clientNameExploded = explode(' ', $clientName);
        $domain             = $domainModel->where('project_id', $event->sale->projectModel->id)->first();
        $products           = $event->sale->present()->getProducts();

        $data = [
            'name'            => $clientNameExploded[0],
            'project_logo'    => $event->sale->projectModel->logo,
            'tracking_code'   => $event->sale->delivery()->first()->tracking_code,
            'project_contact' => $projectContact,
            "products"        => $products,
        ];
        if (getenv('APP_ENV') != 'local') {
            $sendGridService->sendEmail('noreply@' . $domain['name'], $projectName, $clientEmail, $clientName, 'd-0df5ee26812d461f83c536fe88def4b6', $data);
        }
    }
}
