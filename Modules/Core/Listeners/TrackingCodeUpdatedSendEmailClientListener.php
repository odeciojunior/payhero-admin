<?php

namespace Modules\Core\Listeners;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Laracasts\Presenter\Exceptions\PresenterException;
use Modules\Core\Entities\Domain;
use Modules\Core\Services\FoxUtils;
use Modules\Core\Services\LinkShortenerService;
use Modules\Core\Services\SendgridService;
use Modules\Core\Events\TrackingCodeUpdatedEvent;
use Modules\Core\Services\SmsService;

/**
 * Class TrackingCodeUpdatedSendEmailClientListener
 * @package Modules\Core\Listeners
 */
class TrackingCodeUpdatedSendEmailClientListener implements ShouldQueue
{
    use Queueable;

    /**
     * @param TrackingCodeUpdatedEvent $event
     * @throws PresenterException
     */
    public function handle(TrackingCodeUpdatedEvent $event)
    {
        $sendGridService = new SendgridService();
        $smsService      = new SmsService();
        $linkShortenerService = new LinkShortenerService();
        $domainModel     = new Domain();

        $clientName      = $event->sale->client->present()->getFirstName();
        $clientEmail     = $event->sale->client->email;

        $projectName     = $event->sale->project->name;
        $projectContact  = $event->sale->project->contact;
        $domain             = $domainModel->where('project_id', $event->sale->project->id)->first();

        if (isset($domain)) {
            $data = [
                'name'            => $clientName,
                'project_logo'    => $event->sale->project->logo,
                'tracking_code'   => $event->tracking->tracking_code,
                'project_contact' => $projectContact,
                "products"        => $event->products,
            ];

            $sendGridService->sendEmail('noreply@' . $domain['name'], $projectName, $clientEmail, $clientName, 'd-0df5ee26812d461f83c536fe88def4b6', $data);

            $clientTelephone = FoxUtils::prepareCellPhoneNumber($event->sale->client->telephone);
            $link = $linkShortenerService->shorten('https://www.linkcorreios.com.br/?id=' . $data['tracking_code']);

            if(!empty($clientTelephone) && !empty($link)){
                $smsService->sendSms($clientTelephone, 'Olá ' . $clientName . ', seu código de rastreio chegou: ' . $data['tracking_code'] . '. Acesse: ' . $link);
            }
        }
    }
}
