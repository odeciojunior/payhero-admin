<?php

namespace Modules\Core\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Modules\Core\Entities\Checkout;
use Modules\Core\Entities\Domain;
use Modules\Core\Entities\ProjectNotification;
use Modules\Core\Events\SendEmailEvent;
use Modules\Core\Events\SendSmsEvent;

class CartRecoveryService
{
    public function verifyAbandonedCarts($lastDay = false)
    {
        try {
            $projectNotificationService = new ProjectNotificationService();
            $domainApproved = Domain::STATUS_APPROVED;
            $notificationActive = ProjectNotification::STATUS_ACTIVE;

            if ($lastDay) {
                $startDate = now()
                    ->subDay()
                    ->startOfDay();
                $endDate = now()
                    ->subDay()
                    ->endOfDay();
                $smsNotificationEnum = ProjectNotification::NOTIFICATION_SMS_ABANDONED_CART_NEXT_DAY;
                $emailNotificationEnum = ProjectNotification::NOTIFICATION_EMAIL_ABANDONED_CART_NEXT_DAY;
            } else {
                $startDate = now()->subMinutes(75);
                $endDate = now()->subHour();
                $smsNotificationEnum = ProjectNotification::NOTIFICATION_SMS_ABANDONED_CART_AN_HOUR_LATER;
                $emailNotificationEnum = ProjectNotification::NOTIFICATION_EMAIL_ABANDONED_CART_AN_HOUR_LATER;
            }

            DB::select("SET SESSION group_concat_max_len = @@max_allowed_packet");
            DB::select("SET SESSION sort_buffer_size =  @@sort_buffer_size * 2");
            $query = Checkout::select([
                "checkouts.id as id",
                "checkouts.project_id",
                "p2.name as project_name",
                "cc.checkout_logo as logo",
                DB::raw(
                    "(select message from project_notifications where notification_enum = {$smsNotificationEnum} and status = {$notificationActive} and project_id = p2.id limit 1) as sms_message"
                ),
                DB::raw(
                    "(select message from project_notifications where notification_enum = {$emailNotificationEnum} and status = {$notificationActive} and project_id = p2.id limit 1) as email_message"
                ),
                DB::raw(
                    "(select d.name from domains as d where d.project_id = p2.id and d.status = {$domainApproved} and d.deleted_at is null limit 1) as domain"
                ),
                DB::raw(
                    "cast(concat('[', group_concat(json_object('id', p.id , 'name', p.name, 'photo', p.photo, 'amount', pp.amount)), ']') as json) as products"
                ),
                DB::raw(
                    "(select json_object('name', l.name, 'email', l.email, 'phone', l.telephone) from logs l where l.checkout_id = checkouts.id order by l.id desc limit 1) as customer"
                ),
            ])
                ->join("checkout_plans as cp", "checkouts.id", "=", "cp.checkout_id")
                ->join("products_plans as pp", "cp.plan_id", "=", "pp.plan_id")
                ->join("products as p", "pp.product_id", "=", "p.id")
                ->join("projects as p2", "checkouts.project_id", "=", "p2.id")
                ->join("checkout_configs as cc", "cc.project_id", "=", "p2.id")
                ->where("checkouts.status_enum", Checkout::STATUS_ABANDONED_CART)
                ->whereBetween("checkouts.created_at", [$startDate, $endDate])
                ->groupBy(
                    "checkouts.id",
                    "checkouts.project_id",
                    "p2.name",
                    "cc.checkout_logo",
                    "sms_message",
                    "email_message",
                    "domain"
                );

            $query->chunk(100, function ($abandonedCarts) use (
                $emailNotificationEnum,
                $smsNotificationEnum,
                $projectNotificationService
            ) {
                foreach ($abandonedCarts as $abandonedCart) {
                    try {
                        $customer = json_decode($abandonedCart->customer);
                        $products = json_decode($abandonedCart->products);
                        $project = (object) [
                            "id" => $abandonedCart->project_id,
                            "name" => $abandonedCart->project_name,
                        ];

                        $domain = $abandonedCart->domain ?? "nexuspay.com.br";
                        $linkCheckout =
                            "https://checkout." . $domain . "/recovery/" . hashids_encode($abandonedCart->id);

                        $customerFirstName = current(explode(" ", $customer->name));
                        $customerPhone = "+55" . preg_replace("/[^0-9]/", "", $customer->phone);

                        if (!empty($abandonedCart->sms_message)) {
                            $message = $abandonedCart->sms_message;
                            $smsMessage = $projectNotificationService->formatNotificationData(
                                $message,
                                null,
                                $project,
                                "sms",
                                $linkCheckout,
                                $customer
                            );
                            if (!empty($smsMessage) && !empty($customerPhone)) {
                                $dataSms = [
                                    "message" => $smsMessage,
                                    "telephone" => $customerPhone,
                                    "checkout_id" => $abandonedCart->id,
                                ];
                                event(new SendSmsEvent($dataSms));
                            }
                        }

                        if (!empty($abandonedCart->email_message)) {
                            $message = json_decode($abandonedCart->email_message);
                            $subjectMessage = $projectNotificationService->formatNotificationData(
                                $message->subject,
                                null,
                                $project,
                                null,
                                $linkCheckout,
                                $customer
                            );
                            $titleMessage = $projectNotificationService->formatNotificationData(
                                $message->title,
                                null,
                                $project,
                                null,
                                $linkCheckout,
                                $customer
                            );
                            $contentMessage = $projectNotificationService->formatNotificationData(
                                $message->content,
                                null,
                                $project,
                                null,
                                $linkCheckout,
                                $customer
                            );
                            $contentMessage = preg_replace("/\r\n/", "<br/>", $contentMessage);

                            if (!empty($domain)) {
                                $bodyEmail = [
                                    "name" => $customerFirstName,
                                    "project_logo" => $abandonedCart->logo,
                                    "checkout_link" => $linkCheckout,
                                    "subject" => $subjectMessage,
                                    "title" => $titleMessage,
                                    "content" => $contentMessage,
                                    "products" => $products,
                                ];

                                $dataEmail = [
                                    "domainName" => $domain,
                                    "projectName" => $project->name ?? "",
                                    "clientEmail" => $customer->email,
                                    "clientName" => $customer->name ?? "",
                                    "templateId" => "d-03ad5b50f5654118b888abceebf24a02", // done
                                    "bodyEmail" => $bodyEmail,
                                    "checkout_id" => $abandonedCart->id,
                                ];

                                event(new SendEmailEvent($dataEmail));
                            }
                        }
                    } catch (\Exception $e) {
                        report($e);
                    }
                }
            });
        } catch (\Exception $e) {
            report($e);
        }
    }
}
