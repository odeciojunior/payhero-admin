<?php

namespace Modules\Core\Listeners;

use Exception;
use Illuminate\Bus\Queueable;
use Modules\Core\Entities\Domain;
use Modules\Core\Entities\ProjectNotification;
use Modules\Core\Entities\Tracking;
use Modules\Core\Services\ProductService;
use Modules\Core\Services\ProjectNotificationService;
use Modules\Core\Services\SmsService;
use Modules\Core\Services\SendgridService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Core\Services\LinkShortenerService;
use Modules\Core\Events\TrackingCodeUpdatedEvent;

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
            $productService = new ProductService();
            $trackingModel = new Tracking();

            $tracking = $trackingModel->with([
                'sale.project',
                'sale.customer',
                'productsPlansSale.tracking',
                'productsPlansSale.product',
            ])->find($event->trackingId);

            $sale = $tracking->sale;
            $project = $sale->project;
            $customer = $sale->customer;
            $products = $productService->getProductsBySale($sale);

            $clientName = $customer->present()->getFirstName();
            $clientEmail = $customer->email;
            $clientTelephone = $customer->telephone;

            $projectName = $project->name;
            $projectContact = $project->contact;
            $domain = $domainModel->where('project_id', $project->id)->first();

            //Traz a mensagem do sms formatado
            $projectNotificationPresenter = $projectNotificationModel->present();
            $projectNotificationSms = $projectNotificationModel->where('project_id', $project->id)
                ->where('notification_enum', $projectNotificationPresenter->getNotificationEnum('sms_tracking_immediate'))
                ->where('status', $projectNotificationPresenter->getStatus('active'))
                ->first();
            //Traz o assunto, titulo e texto do email formatados
            $projectNotificationPresenter = $projectNotificationModel->present();
            $projectNotificationEmail = $projectNotificationModel->where('project_id', $project->id)
                ->where('notification_enum', $projectNotificationPresenter->getNotificationEnum('email_tracking_immediate'))
                ->where('status', $projectNotificationPresenter->getStatus('active'))
                ->first();

            if (!empty($domain)) {
                $linkBase = 'https://tracking.' . $domain->name . '/';
                if (!empty($projectNotificationSms)) {
                    //$link = $linkShortenerService->shorten($linkBase . $data['tracking_code']);
                    $message = $projectNotificationSms->message;
                    $smsMessage = $projectNotificationService->formatNotificationData($message, $sale, $project, 'sms', null, null, $event->tracking->tracking_code);
                    if (!empty($smsMessage) && !empty($clientTelephone)) {
                        $smsService->sendSms($clientTelephone, $smsMessage);
                    }
                }
                if (!empty($projectNotificationEmail)) {
                    $message = json_decode($projectNotificationEmail->message);
                    $subjectMessage = $projectNotificationService->formatNotificationData($message->subject, $sale, $project, null, null, null, $event->tracking->tracking_code);
                    $titleMessage = $projectNotificationService->formatNotificationData($message->title, $sale, $project, null, null, null, $event->tracking->tracking_code);
                    $contentMessage = $projectNotificationService->formatNotificationData($message->content, $sale, $project, null, null, null, $event->tracking->tracking_code);
                    $contentMessage = preg_replace("/\r\n/", "<br/>", $contentMessage);
                    $data = [
                        'name' => $clientName,
                        'project_logo' => $project->logo,
                        'tracking_code' => $tracking->tracking_code,
                        'project_contact' => $projectContact,
                        "subject" => $subjectMessage,
                        "title" => $titleMessage,
                        "content" => $contentMessage,
                        "products" => $products,
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
