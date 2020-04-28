<?php

namespace Modules\Core\Listeners;

use Exception;
use Illuminate\Bus\Queueable;
use Modules\Core\Entities\Domain;
use Modules\Core\Entities\ProjectNotification;
use Modules\Core\Services\FoxUtils;
use Modules\Core\Services\ProjectNotificationService;
use Modules\Core\Services\SmsService;
use Modules\Core\Services\SendgridService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Core\Services\LinkShortenerService;
use Modules\Core\Events\TrackingCodeUpdatedEvent;
use Laracasts\Presenter\Exceptions\PresenterException;

/**
 * Class TrackingCodeUpdatedSendEmailClientListener
 * @package Modules\Core\Listeners
 */
class TrackingCodeUpdatedSendEmailClientListener implements ShouldQueue
{
    use Queueable;

    /**
     * @param TrackingCodeUpdatedEvent $event
     */
    public function handle(TrackingCodeUpdatedEvent $event)
    {
        try {
            $sendGridService = new SendgridService();
            $smsService = new SmsService();
            $linkShortenerService = new LinkShortenerService();
            $domainModel = new Domain();
            $projectNotificationModel = new ProjectNotification();
            $projectNotificationService = new ProjectNotificationService();

            $clientName = $event->sale->customer->present()->getFirstName();
            $clientEmail = $event->sale->customer->email;
            $clientTelephone = $event->sale->customer->telephone;

            $projectName = $event->sale->project->name;
            $projectContact = $event->sale->project->contact;
            $domain = $domainModel->where('project_id', $event->sale->project->id)->first();

            //Traz a mensagem do sms formatado
            $projectNotificationPresenter = $projectNotificationModel->present();
            $projectNotificationSms = $projectNotificationModel->where('project_id', $event->sale->project->id)
                ->where('notification_enum', $projectNotificationPresenter->getNotificationEnum('sms_tracking_immediate'))
                ->where('status', $projectNotificationPresenter->getStatus('active'))
                ->first();
            //Traz o assunto, titulo e texto do email formatados
            $projectNotificationPresenter = $projectNotificationModel->present();
            $projectNotificationEmail = $projectNotificationModel->where('project_id', $event->sale->project->id)
                ->where('notification_enum', $projectNotificationPresenter->getNotificationEnum('email_tracking_immediate'))
                ->where('status', $projectNotificationPresenter->getStatus('active'))
                ->first();

            if (!empty($domain)) {
                $linkBase = 'https://tracking.' . $domain->name . '/';
                if (!empty($projectNotificationSms)) {
                    //                $link            = $linkShortenerService->shorten($linkBase . $data['tracking_code']);
                    $message = $projectNotificationSms->message;
                    $smsMessage = $projectNotificationService->formatNotificationData($message, $event->sale, $event->sale->project, 'sms', null, null, $event->tracking->tracking_code);
                    if (!empty($smsMessage) && !empty($clientTelephone)) {
                        $smsService->sendSms($clientTelephone, $smsMessage);
                    }
                }
                if (!empty($projectNotificationEmail)) {
                    $message = json_decode($projectNotificationEmail->message);
                    $subjectMessage = $projectNotificationService->formatNotificationData($message->subject, $event->sale, $event->sale->project, null, null, null, $event->tracking->tracking_code);
                    $titleMessage = $projectNotificationService->formatNotificationData($message->title, $event->sale, $event->sale->project, null, null, null, $event->tracking->tracking_code);
                    $contentMessage = $projectNotificationService->formatNotificationData($message->content, $event->sale, $event->sale->project, null, null, null, $event->tracking->tracking_code);
                    $contentMessage = preg_replace("/\r\n/", "<br/>", $contentMessage);
                    $data = [
                        'name' => $clientName,
                        'project_logo' => $event->sale->project->logo,
                        'tracking_code' => $event->tracking->tracking_code,
                        'project_contact' => $projectContact,
                        "subject" => $subjectMessage,
                        "title" => $titleMessage,
                        "content" => $contentMessage,
                        "products" => $event->products,
                        "link" => $linkBase,
                        'sac_link' => "https://sac." . $domain->name,
                    ];

                    $sendGridService->sendEmail('noreply@' . $domain['name'], $projectName, $clientEmail, $clientName, 'd-347cde26384449548df47e290ad50906', $data);
                }
            }
        } catch (Exception $e) {
            report($e);
        }
    }

    public function tags()
    {
        return ['listener:' . static::class, 'tracking'];
    }
}
