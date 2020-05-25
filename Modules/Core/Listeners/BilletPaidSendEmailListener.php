<?php

namespace Modules\Core\Listeners;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Core\Entities\Domain;
use Modules\Core\Entities\Project;
use Modules\Core\Entities\ProjectNotification;
use Modules\Core\Events\BilletPaidEvent;
use Modules\Core\Services\ProjectNotificationService;
use Modules\Core\Services\SaleService;
use Modules\Core\Services\SendgridService;
use Vinkla\Hashids\Facades\Hashids;

/**
 * Class BilletPaidSendEmailListener
 * @package Modules\Core\Listeners
 */
class BilletPaidSendEmailListener implements ShouldQueue
{
    use Queueable;

    /**
     * Create the event listener.
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * @param BilletPaidEvent $event
     * @return bool
     */
    public function handle(BilletPaidEvent $event)
    {
        try {
            $projectModel = new Project();
            $domainModel = new Domain();
            $projectNotificationModel = new ProjectNotification();
            $saleService = new SaleService();
            $sendGridService = new SendgridService();
            $projectNotificationService = new ProjectNotificationService();
            $domainPresent = $domainModel->present();

            $project = $projectModel->find($event->plan->project_id);
            $domain = $domainModel->where('project_id', $project->id)
                ->where('status', $domainPresent->getStatus('approved'))->first();

            if (empty($domain)) {
                return false;
            }

            $sale = $event->sale;
            $customer = $event->customer;
            if (stristr($customer->email, 'invalido') !== false) {
                return false;
            }
            
            $saleCode = Hashids::connection('sale_id')->encode($sale->id);
            $products = $saleService->getEmailProducts($sale->id);

            $sale->total_paid_value = preg_replace("/[^0-9]/", "", $sale->total_paid_value);
            $sale->total_paid_value = substr_replace(
                $sale->total_paid_value,
                ',',
                strlen($sale->total_paid_value) - 2,
                0
            );
            $sale->shipment_value = preg_replace("/[^0-9]/", "", $sale->shipment_value);
            $sale->shipment_value = substr_replace(
                $sale->shipment_value,
                ',',
                strlen($sale->shipment_value) - 2,
                0
            );

            $subTotal = preg_replace("/[^0-9]/", "", $sale->sub_total);
            $subTotal = substr_replace($subTotal, ',', strlen($subTotal) - 2, 0);

            $discount = preg_replace("/[^0-9]/", "", $sale->shopify_discount);
            if ($discount == 0 || $discount == null) {
                $discount = '';
            }
            if ($discount != '') {
                $discount = substr_replace($discount, ',', strlen($discount) - 2, 0);
            }

            //Traz o assunto, titulo e texto do email formatados
            $projectNotificationPresenter = $projectNotificationModel->present();
            $projectNotificationEmail = $projectNotificationModel->where('project_id', $project->id)
                ->where(
                    'notification_enum',
                    $projectNotificationPresenter->getNotificationEnum('email_billet_paid_immediate')
                )
                ->where('status', $projectNotificationPresenter->getStatus('active'))
                ->first();

            if (empty($projectNotificationEmail)) {
                return false;
            }

            $message = json_decode($projectNotificationEmail->message);
            $subjectMessage = $projectNotificationService->formatNotificationData(
                $message->subject,
                $sale,
                $project
            );
            $titleMessage = $projectNotificationService->formatNotificationData(
                $message->title,
                $sale,
                $project
            );
            $contentMessage = $projectNotificationService->formatNotificationData(
                $message->content,
                $sale,
                $project
            );
            $contentMessage = preg_replace("/\r\n/", "<br/>", $contentMessage);

            $data = [
                'first_name' => $customer->present()->getFirstName(),
                "store_logo" => $project->logo,
                "project_contact" => $project->contact,
                'sale_code' => $saleCode,
                "products" => $products,
                "total_paid_value" => $sale->total_paid_value,
                "shipment_value" => $sale->shipment_value,
                "subtotal" => strval($subTotal),
                "subject" => $subjectMessage,
                "title" => $titleMessage,
                "content" => $contentMessage,
                'discount' => $discount,
                'sac_link' => "https://sac." . $domain->name,
            ];
            $sendGridService->sendEmail(
                'noreply@' . $domain['name'],
                $project['name'],
                $customer['email'],
                $customer['name'],
                'd-89821e27e40e4b1aa715b49c68a6d2e7',
                $data
            );
        } catch (Exception $e) {
            report($e);
        }
    }
}
