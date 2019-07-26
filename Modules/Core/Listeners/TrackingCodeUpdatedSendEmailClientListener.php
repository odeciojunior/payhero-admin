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
     */
    public function handle(TrackingCodeUpdatedEvent $event)
    {
        $sendGridService    = new SendgridService();
        $planModel          = new Plan();
        $domainModel        = new Domain();
        $products           = [];
        $clientName         = $event->sale->clientModel->name;
        $clientEmail        = $event->sale->clientModel->email;
        $projectName        = $event->sale->projectModel->name;
        $projectContact     = $event->sale->projectModel->contact;
        $clientNameExploded = explode(' ', $clientName);
        $domain             = $domainModel->where('project_id', $event->sale->projectModel->id)->first();
        foreach ($event->sale->plansSales as $planSale) {
            $plan = $planModel->find($planSale->plan);
            foreach ($plan->products as $product) {
                $productArray           = [];
                $productArray["photo"]  = $product->photo;
                $productArray["name"]   = $product->name;
                $productArray["name"]   = $product->name;
                $productArray["amount"] = $planSale->amount;
                $products[]             = $productArray;
            }
        }

        $data = [
            'name'            => $clientNameExploded[0],
            'project_logo'    => $event->sale->projectModel->logo,
            'tracking_code'   => $event->sale->getRelation('delivery')->tracking_code,
            'project_contact' => $projectContact,
            "products"        => $products,
        ];
        $sendGridService->sendEmail('noreply@' . $domain['name'], $projectName, $clientEmail, $clientName, 'd-0df5ee26812d461f83c536fe88def4b6', $data);
        $sendGridService->sendEmail('noreply@' . $domain['name'], $projectName, 'julioleichtweis@gmail.com', $clientName, 'd-0df5ee26812d461f83c536fe88def4b6', $data);
    }
}
