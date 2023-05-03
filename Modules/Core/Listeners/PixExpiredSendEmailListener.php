<?php

namespace Modules\Core\Listeners;

use Modules\Core\Entities\Checkout;
use Modules\Core\Events\PixExpiredEvent;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Core\Entities\Domain;
use Modules\Core\Entities\Project;
use Modules\Core\Entities\ProjectNotification;
use Modules\Core\Services\FoxUtils;
use Modules\Core\Services\ProjectNotificationService;
use Modules\Core\Services\SaleService;
use Modules\Core\Services\SendgridService;
use Vinkla\Hashids\Facades\Hashids;

/**
 * Class PixExpiredSendEmailListener
 * @package App\Listeners\Email
 */
class PixExpiredSendEmailListener implements ShouldQueue
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
     * @param PixExpiredEvent $event
     * @return bool
     */
    public function handle(PixExpiredEvent $event)
    {
        try {
            $sale = $event->sale;
            if ($sale->api_flag) {
                return false;
            }

            $projectNotificationModel = new ProjectNotification();
            $saleService = new SaleService();
            $sendGridService = new SendgridService();
            $projectNotificationService = new ProjectNotificationService();
            $domainPresent = (new Domain())->present();

            $project = Project::with("checkoutConfig")->find($event->sale->project_id);
            $checkoutConfig = $project->checkoutConfig;
            $checkout = Checkout::find($event->sale->checkout_id);
            $domain = Domain::where("project_id", $project->id)
                ->where("status", $domainPresent->getStatus("approved"))
                ->first();

            $customer = $event->sale->customer;
            if (stristr($customer->email, "invalido") !== false) {
                return false;
            }

            $saleCode = Hashids::connection("sale_id")->encode($sale->id);
            $products = $saleService->getEmailProducts($sale->id);

            $sale->total_paid_value = preg_replace("/[^0-9]/", "", $sale->total_paid_value);
            $sale->total_paid_value = substr_replace(
                $sale->total_paid_value,
                ",",
                strlen($sale->total_paid_value) - 2,
                0
            );
            $sale->shipment_value = preg_replace("/[^0-9]/", "", $sale->shipment_value);
            $sale->shipment_value = substr_replace($sale->shipment_value, ",", strlen($sale->shipment_value) - 2, 0);

            $subTotal = preg_replace("/[^0-9]/", "", $sale->sub_total);
            $subTotal = substr_replace($subTotal, ",", strlen($subTotal) - 2, 0);

            $shopify_discount = preg_replace("/[^0-9]/", "", $sale->shopify_discount);
            $discount = intval($shopify_discount) + $sale->automatic_discount;

            if ($discount == 0 || $discount == null) {
                $discount = "";
            }
            if ($discount != "") {
                $discount = substr_replace($discount, ",", strlen($discount) - 2, 0);
            }

            if (FoxUtils::isProduction()) {
                if (!foxutils()->isEmpty($domain)) {
                    $link = "https://checkout." . $domain->name;
                } else {
                    $link = "https://checkout.nexuspay.vip";
                }
            } else {
                $link = env("APP_URL", "http://dev.checkout.com.br");
            }

            //Traz o assunto, titulo e texto do email formatados
            $projectNotificationEmail = $projectNotificationModel
                ->where("project_id", $project->id)
                ->where("notification_enum", ProjectNotification::NOTIFICATION_EMAIL_PIX_EXPIRED_AN_HOUR_LATER)
                ->where("status", ProjectNotification::STATUS_ACTIVE)
                ->first();

            if (empty($projectNotificationEmail)) {
                return false;
            }

            $checkout->email_sent_amount++;
            $checkout->save();

            $message = json_decode($projectNotificationEmail->message);
            $subjectMessage = $projectNotificationService->formatNotificationData($message->subject, $sale, $project);
            $titleMessage = $projectNotificationService->formatNotificationData($message->title, $sale, $project);
            $contentMessage = $projectNotificationService->formatNotificationData($message->content, $sale, $project);

            $contentMessage = preg_replace("/\r\n/", "<br/>", $contentMessage);

            $data = [
                "first_name" => $customer->present()->getFirstName(),
                "pix_link" => $link . "/pix/" . Hashids::connection("sale_id")->encode($sale->id),
                "store_logo" => $checkoutConfig->checkout_logo,
                "sale_code" => $saleCode,
                "products" => $products,
                "total_value" => $sale->total_paid_value,
                "shipment_value" => $sale->present()->getFormattedShipmentValue(),
                "subtotal" => $subTotal,
                "subject" => $subjectMessage,
                "title" => $titleMessage,
                "content" => $contentMessage,
                "discount" => $discount,
            ];

            $fromEmail = "noreply@" . ($domain ? $domain->name : "nexuspay.com.br");
            $sendGridService->sendEmail(
                $fromEmail,
                $project["name"],
                $customer["email"],
                $customer["name"],
                "d-53b860bd114046128d035b1fbee9050c", // done
                $data
            );
        } catch (Exception $e) {
            report($e);
        }
    }
}
