<?php

namespace Modules\Core\Services;

use Exception;
use Modules\Core\Entities\Product;
use Modules\Core\Entities\ProductPlanSale;
use Illuminate\Support\Carbon;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\User;
use Modules\Core\Entities\Domain;
use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\Project;
use Modules\Core\Entities\Checkout;
use Modules\Core\Events\SendSmsEvent;
use Modules\Core\Entities\Transaction;
use Modules\Core\Events\SendEmailEvent;
use Modules\Core\Events\BilletExpiredEvent;
use Modules\Core\Entities\ProjectNotification;

/**
 * Class BoletoService
 * @package Modules\Core\Services
 */
class BoletoService
{
    public function verifyBoletosExpiring()
    {
        try {
            $projectNotificationService = new ProjectNotificationService();
            $domainApproved = Domain::STATUS_APPROVED;
            $notificationActive = ProjectNotification::STATUS_ACTIVE;

            $smsNotificationEnum = ProjectNotification::NOTIFICATION_SMS_BOLETO_DUE_TODAY;
            $emailNotificationEnum = ProjectNotification::NOTIFICATION_EMAIL_BOLETO_DUE_TODAY;

            DB::select("SET SESSION group_concat_max_len = @@max_allowed_packet");
            DB::select("SET SESSION sort_buffer_size =  @@sort_buffer_size * 2");
            Sale::select([
                "sales.id",
                "sales.checkout_id",
                "sales.sub_total",
                "sales.shipment_value",
                "sales.shopify_discount",
                "sales.total_paid_value",
                "sales.boleto_digitable_line",
                "sales.boleto_link",
                "sales.boleto_due_date",
                "c.name as customer_name",
                "c.email as customer_email",
                "c.telephone as customer_phone",
                "sales.project_id",
                "p2.name as project_name",
                "cc.checkout_logo as logo",
                DB::raw(
                    "(select message from project_notifications where notification_enum = {$smsNotificationEnum} and status = {$notificationActive} and project_id = p2.id limit 1) as sms_message"
                ),
                DB::raw(
                    "(select message from project_notifications where notification_enum = {$emailNotificationEnum} and status = {$notificationActive} and project_id = p2.id limit 1) as email_message"
                ),
                DB::raw(
                    "(select d.name from domains as d where d.project_id = p2.id and d.status = {$domainApproved} limit 1) as domain"
                ),
                DB::raw(
                    "cast(concat('[', group_concat(json_object('pps_id', pps.id, 'name', p.name, 'photo', p.photo, 'amount', pps.amount, 'type_enum', p.type_enum, 'url', p.digital_product_url, 'expiration', p.url_expiration_time)), ']') as json) as products"
                ),
            ])
                ->join("products_plans_sales as pps", "pps.sale_id", "=", "sales.id")
                ->join("products as p", "p.id", "=", "pps.product_id")
                ->join("customers as c", "c.id", "=", "sales.customer_id")
                ->join("projects as p2", "p2.id", "=", "sales.project_id")
                ->join("checkout_configs as cc", "cc.project_id", "=", "p2.id")
                ->where("sales.payment_method", Sale::PAYMENT_TYPE_BANK_SLIP)
                ->where("sales.status", Sale::STATUS_PENDING)
                ->where("sales.api_flag", 0)
                ->whereDate("sales.boleto_due_date", now()->startOfDay())
                ->groupBy([
                    "sales.id",
                    "sales.checkout_id",
                    "sales.sub_total",
                    "sales.shipment_value",
                    "sales.shopify_discount",
                    "sales.total_paid_value",
                    "sales.boleto_digitable_line",
                    "sales.boleto_link",
                    "sales.boleto_due_date",
                    "c.name",
                    "c.email",
                    "c.telephone",
                    "sales.project_id",
                    "p2.name",
                    "cc.checkout_logo",
                    "sms_message",
                    "email_message",
                    "domain",
                ])
                ->chunk(500, function ($sales) use ($projectNotificationService) {
                    foreach ($sales as $sale) {
                        try {
                            $products = json_decode($sale->products);
                            foreach ($products as $product) {
                                if ($product->type_enum === Product::TYPE_DIGITAL && !empty($product->url)) {
                                    $product->url = foxutils()->getAwsSignedUrl($product->url, $product->expiration);
                                    ProductPlanSale::where("id", $product->pps_id)->update([
                                        "temporary_url" => $product->url,
                                    ]);
                                } else {
                                    $product->url = "";
                                }
                                $product->photo = foxutils()->checkFileExistUrl($product->photo)
                                    ? $product->photo
                                    : "https://nexuspay-digital-products.s3.amazonaws.com/admin/produto.png";
                            }

                            $subTotal = preg_replace("/[^0-9]/", "", $sale->sub_total);
                            $subTotal = substr_replace($subTotal, ",", strlen($subTotal) - 2, 0);

                            $sale->shipment_value = preg_replace("/[^0-9]/", "", $sale->shipment_value);
                            $sale->shipment_value = substr_replace(
                                $sale->shipment_value,
                                ",",
                                strlen($sale->shipment_value) - 2,
                                0
                            );

                            $discount = preg_replace("/[^0-9]/", "", $sale->shopify_discount);
                            if ($discount == 0 || $discount == null) {
                                $discount = "";
                            }
                            $sale->total_paid_value = preg_replace("/[^0-9]/", "", $sale->total_paid_value);
                            if ($discount != "") {
                                $discount = substr_replace($discount, ",", strlen($discount) - 2, 0);
                            }
                            $sale->total_paid_value = substr_replace(
                                $sale->total_paid_value,
                                ",",
                                strlen($sale->total_paid_value) - 2,
                                0
                            );

                            $boletoDigitableLine = [];
                            $boletoDigitableLine[0] = substr($sale->boleto_digitable_line, 0, 24);
                            $boletoDigitableLine[1] = substr(
                                $sale->boleto_digitable_line,
                                24,
                                strlen($sale->boleto_digitable_line) - 1
                            );
                            $sale->boleto_due_date = Carbon::parse($sale->boleto_due_date)->format("d/m/y");

                            $domain = Domain::select("name")
                                ->where("project_id", $sale->project_id)
                                ->where("status", 3)
                                ->first();
                            $domainName = $domain->name ?? "azcend.com.br";
                            $boletoLink =
                                "https://checkout.{$domainName}/order/" .
                                hashids_encode($sale->id, "sale_id") .
                                "/download-boleto";

                            $saleData = (object) [
                                "id" => $sale->id,
                                "project_id" => $sale->project_id,
                                "boleto_link" => $boletoLink,
                                "customer" => (object) [
                                    "name" => $sale->customer_name,
                                ],
                            ];

                            $projectData = (object) [
                                "id" => $sale->project_id,
                                "name" => $sale->project_name,
                            ];

                            if (!empty($sale->sms_message)) {
                                $message = $sale->sms_message;
                                $smsMessage = $projectNotificationService->formatNotificationData(
                                    $message,
                                    $saleData,
                                    $projectData,
                                    "sms"
                                );
                                if (!empty($smsMessage) && !empty($sale->customer_phone)) {
                                    $data = [
                                        "message" => $smsMessage,
                                        "telephone" => $sale->customer_phone,
                                        "checkout_id" => $sale->checkout_id,
                                    ];
                                    event(new SendSmsEvent($data));
                                }
                            }

                            if (
                                !empty($sale->email_message) &&
                                !empty($sale->domain) &&
                                !empty($sale->customer_email)
                            ) {
                                if (stristr($sale->customer_email, "invalido") === false) {
                                    $message = json_decode($sale->email_message);
                                    if (!empty($message->title)) {
                                        $subjectMessage = $projectNotificationService->formatNotificationData(
                                            $message->subject,
                                            $saleData,
                                            $projectData
                                        );
                                        $titleMessage = $projectNotificationService->formatNotificationData(
                                            $message->title,
                                            $saleData,
                                            $projectData
                                        );
                                        $contentMessage = $projectNotificationService->formatNotificationData(
                                            $message->content,
                                            $saleData,
                                            $projectData
                                        );
                                        $contentMessage = preg_replace("/\r\n/", "<br/>", $contentMessage);
                                        $customerFirstName = current(explode(" ", $sale->customer_name));

                                        $data = [
                                            "name" => $customerFirstName,
                                            "boleto_link" => $boletoLink,
                                            "boleto_digitable_line" => $boletoDigitableLine,
                                            "boleto_due_date" => $sale->boleto_due_date,
                                            "total_paid_value" => $sale->total_paid_value,
                                            "shipment_value" => $sale->shipment_value,
                                            "subtotal" => strval($subTotal),
                                            "discount" => $discount,
                                            "project_logo" => $sale->logo,
                                            "subject" => $subjectMessage,
                                            "title" => $titleMessage,
                                            "content" => $contentMessage,
                                            "products" => $products,
                                        ];
                                        $dataEmail = [
                                            "domainName" => $sale->domain,
                                            "projectName" => $projectData->name ?? "",
                                            "clientEmail" => $sale->customer_email,
                                            "clientName" => $customerFirstName ?? "",
                                            "templateId" => "d-72259c31ebab40d69d903992ac1a963d", /// done
                                            "bodyEmail" => $data,
                                            "checkout_id" => $sale->checkout_id,
                                        ];
                                        event(new SendEmailEvent($dataEmail));
                                    }
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

    /**
     * Hoje vence o seu pedido
     */
    public function verifyBoletosExpiring2()
    {
        try {
            $saleModel = new Sale();
            $saleService = new SaleService();
            $projectModel = new Project();
            $domainModel = new Domain();
            $checkoutModel = new Checkout();
            $projectNotificationModel = new ProjectNotification();
            $projectNotificationService = new ProjectNotificationService();
            $domainPresent = $domainModel->present();

            $saleModel
                ->where([
                    ["payment_method", "=", "2"],
                    ["status", "=", "2"],
                    ["api_flag", "=", "0"],
                    [DB::raw("(DATE_FORMAT(boleto_due_date,'%Y-%m-%d'))"), now()->toDateString()],
                ])
                ->with("customer", "plansSales.plan.products")
                ->chunk(500, function ($boletoDueToday) use (
                    $projectModel,
                    $domainModel,
                    $checkoutModel,
                    $saleService,
                    $projectNotificationService,
                    $projectNotificationModel,
                    $domainPresent
                ) {
                    foreach ($boletoDueToday as $boleto) {
                        if ($boleto->api_flag) {
                            continue;
                        }

                        $checkout = $checkoutModel->where("id", $boleto->checkout_id)->first();
                        $clientName = $boleto->customer->name;
                        $clientEmail = $boleto->customer->email;

                        $subTotal = preg_replace("/[^0-9]/", "", $boleto->sub_total);
                        $discount = preg_replace("/[^0-9]/", "", $boleto->shopify_discount);

                        if ($discount == 0 || $discount == null) {
                            $discount = "";
                        }

                        $clientNameExploded = explode(" ", $clientName);
                        $boleto->total_paid_value = preg_replace("/[^0-9]/", "", $boleto->total_paid_value);

                        if ($discount != "") {
                            $discount = substr_replace($discount, ",", strlen($discount) - 2, 0);
                        }

                        $boleto->total_paid_value = substr_replace(
                            $boleto->total_paid_value,
                            ",",
                            strlen($boleto->total_paid_value) - 2,
                            0
                        );

                        $products = $saleService->getEmailProducts($boleto->id);
                        $project = $projectModel->with("checkoutConfig")->find($boleto->project_id);
                        $checkoutConfig = $project->checkoutConfig;
                        $domain = $domainModel
                            ->where("project_id", $project->id)
                            ->where("status", $domainPresent->getStatus("approved"))
                            ->first();
                        $domainName = $domain->name ?? "azcend.com.br";
                        $boletoLink =
                            "https://checkout.{$domainName}/order/" .
                            hashids_encode($boleto->id, "sale_id") .
                            "/download-boleto";

                        $subTotal = substr_replace($subTotal, ",", strlen($subTotal) - 2, 0);
                        $boleto->shipment_value = preg_replace("/[^0-9]/", "", $boleto->shipment_value);
                        $boleto->shipment_value = substr_replace(
                            $boleto->shipment_value,
                            ",",
                            strlen($boleto->shipment_value) - 2,
                            0
                        );
                        $boletoDigitableLine = [];
                        $boletoDigitableLine[0] = substr($boleto->boleto_digitable_line, 0, 24);
                        $boletoDigitableLine[1] = substr(
                            $boleto->boleto_digitable_line,
                            24,
                            strlen($boleto->boleto_digitable_line) - 1
                        );
                        $boleto->boleto_due_date = Carbon::parse($boleto->boleto_due_date)->format("d/m/y");

                        $clientTelephone = $boleto->customer->telephone;

                        //Traz a mensagem do sms formatado
                        $projectNotificationPresenter = $projectNotificationModel->present();
                        $projectNotificationSms = $projectNotificationModel
                            ->where("project_id", $project->id)
                            ->where(
                                "notification_enum",
                                $projectNotificationPresenter->getNotificationEnum("sms_billet_due_today")
                            )
                            ->where("status", $projectNotificationPresenter->getStatus("active"))
                            ->first();
                        if (!empty($projectNotificationSms)) {
                            $message = $projectNotificationSms->message;
                            $smsMessage = $projectNotificationService->formatNotificationData(
                                $message,
                                $boleto,
                                $project,
                                "sms"
                            );
                            if (!empty($smsMessage) && !empty($clientTelephone)) {
                                $data = [
                                    "message" => $smsMessage,
                                    "telephone" => $clientTelephone,
                                    "checkout_id" => $checkout->id,
                                ];
                                event(new SendSmsEvent($data));
                            }
                        }
                        //Traz o assunto, titulo e texto do email formatados
                        $projectNotificationPresenter = $projectNotificationModel->present();
                        $projectNotificationEmail = $projectNotificationModel
                            ->where("project_id", $project->id)
                            ->where(
                                "notification_enum",
                                $projectNotificationPresenter->getNotificationEnum("email_billet_due_today")
                            )
                            ->where("status", $projectNotificationPresenter->getStatus("active"))
                            ->first();

                        if (!empty($projectNotificationEmail) && !empty($domain) && !empty($clientEmail)) {
                            if (stristr($clientEmail, "invalido") === false) {
                                $message = json_decode($projectNotificationEmail->message);
                                if (!empty($message->title)) {
                                    $subjectMessage = $projectNotificationService->formatNotificationData(
                                        $message->subject,
                                        $boleto,
                                        $project
                                    );
                                    $titleMessage = $projectNotificationService->formatNotificationData(
                                        $message->title,
                                        $boleto,
                                        $project
                                    );
                                    $contentMessage = $projectNotificationService->formatNotificationData(
                                        $message->content,
                                        $boleto,
                                        $project
                                    );
                                    $contentMessage = preg_replace("/\r\n/", "<br/>", $contentMessage);
                                    $data = [
                                        "name" => $clientNameExploded[0],
                                        "boleto_link" => $boletoLink,
                                        "boleto_digitable_line" => $boletoDigitableLine,
                                        "boleto_due_date" => $boleto->boleto_due_date,
                                        "total_paid_value" => $boleto->total_paid_value,
                                        "shipment_value" => $boleto->shipment_value,
                                        "subtotal" => strval($subTotal),
                                        "discount" => $discount,
                                        "project_logo" => $checkoutConfig->checkout_logo,
                                        "subject" => $subjectMessage,
                                        "title" => $titleMessage,
                                        "content" => $contentMessage,
                                        "products" => $products,
                                    ];
                                    $dataEmail = [
                                        "domainName" => $domain["name"],
                                        "projectName" => $project["name"] ?? "",
                                        "clientEmail" => $clientEmail,
                                        "clientName" => $clientNameExploded[0] ?? "",
                                        "templateId" => "d-72259c31ebab40d69d903992ac1a963d", /// done
                                        "bodyEmail" => $data,
                                        "checkout_id" => $checkout->id,
                                    ];
                                    event(new SendEmailEvent($dataEmail));
                                }
                            }
                        }
                    }
                });
        } catch (Exception $e) {
            report($e);
        }
    }

    /**
     * Já separamos seu pedido
     */
    public function verifyBoletoWaitingPayment()
    {
        try {
            $saleModel = new Sale();
            $saleService = new SaleService();
            $checkoutModel = new Checkout();
            $projectModel = new Project();
            $domainModel = new Domain();
            $projectNotificationModel = new ProjectNotification();
            $projectNotificationService = new ProjectNotificationService();

            $startDate = now()
                ->startOfDay()
                ->subDay();
            $endDate = now()
                ->endOfDay()
                ->subDay();

            $saleModel
                ->with("customer", "plansSales.plan.products")
                ->whereBetween("start_date", [$startDate, $endDate])
                ->where([
                    ["payment_method", "=", "2"],
                    ["status", "=", "2"],
                    ["api_flag", "=", "0"],
                    [DB::raw("(DATE_FORMAT(boleto_due_date,'%Y-%m-%d'))"), "!=", now()->toDateString()],
                ])
                ->chunk(500, function ($boletos) use (
                    $checkoutModel,
                    $saleService,
                    $projectModel,
                    $domainModel,
                    $projectNotificationService,
                    $projectNotificationModel
                ) {
                    foreach ($boletos as $boleto) {
                        if ($boleto->api_flag) {
                            continue;
                        }

                        try {
                            $checkout = $checkoutModel->where("id", $boleto->checkout_id)->first();
                            $clientName = $boleto->customer->name;
                            $clientEmail = $boleto->customer->email;
                            $clientNameExploded = explode(" ", $clientName);

                            $subTotal = preg_replace("/[^0-9]/", "", $boleto->sub_total);
                            $discount = preg_replace("/[^0-9]/", "", $boleto->shopify_discount);

                            if ($discount == 0 || $discount == null) {
                                $discount = "";
                            }
                            if ($discount != "") {
                                $discount = substr_replace($discount, ",", strlen($discount) - 2, 0);
                            }

                            $boleto->total_paid_value = preg_replace("/[^0-9]/", "", $boleto->total_paid_value);
                            if ($discount != "") {
                                $boleto->total_paid_value =
                                    $boleto->total_paid_value - preg_replace("/[^0-9]/", "", $discount);
                            }

                            $boleto->total_paid_value = substr_replace(
                                $boleto->total_paid_value,
                                ",",
                                strlen($boleto->total_paid_value) - 2,
                                0
                            );
                            $products = $saleService->getEmailProducts($boleto->id);
                            $project = $projectModel->with("checkoutConfig")->find($boleto->project_id);
                            $checkoutConfig = $project->checkoutConfig;
                            $domain = $domainModel
                                ->where("project_id", $project->id)
                                ->where("status", 3)
                                ->first();
                            $domainName = $domain->name ?? "azcend.com.br";
                            $boletoLink =
                                "https://checkout.{$domainName}/order/" .
                                hashids_encode($boleto->id, "sale_id") .
                                "/download-boleto";

                            if (!empty($domain) && !empty($clientEmail)) {
                                if (stristr($clientEmail, "invalido") === false) {
                                    $subTotal = substr_replace($subTotal, ",", strlen($subTotal) - 2, 0);
                                    $boleto->shipment_value = preg_replace("/[^0-9]/", "", $boleto->shipment_value);
                                    $boleto->shipment_value = substr_replace(
                                        $boleto->shipment_value,
                                        ",",
                                        strlen($boleto->shipment_value) - 2,
                                        0
                                    );
                                    $boletoDigitableLine = [];
                                    $boletoDigitableLine[0] = substr($boleto->boleto_digitable_line, 0, 24);
                                    $boletoDigitableLine[1] = substr(
                                        $boleto->boleto_digitable_line,
                                        24,
                                        strlen($boleto->boleto_digitable_line) - 1
                                    );
                                    $boleto->boleto_due_date = Carbon::parse($boleto->boleto_due_date)->format("d/m/y");

                                    //Traz o assunto, titulo e texto do email formatados
                                    $projectNotificationPresenter = $projectNotificationModel->present();
                                    $projectNotification = $projectNotificationModel
                                        ->where("project_id", $project->id)
                                        ->where(
                                            "notification_enum",
                                            $projectNotificationPresenter->getNotificationEnum(
                                                "email_billet_generated_next_day"
                                            )
                                        )
                                        ->where("status", $projectNotificationPresenter->getStatus("active"))
                                        ->first();
                                    if (!empty($projectNotification)) {
                                        $message = json_decode($projectNotification->message);
                                        if (!empty($message->title)) {
                                            $subjectMessage = $projectNotificationService->formatNotificationData(
                                                $message->subject,
                                                $boleto,
                                                $project
                                            );
                                            $titleMessage = $projectNotificationService->formatNotificationData(
                                                $message->title,
                                                $boleto,
                                                $project
                                            );
                                            $contentMessage = $projectNotificationService->formatNotificationData(
                                                $message->content,
                                                $boleto,
                                                $project
                                            );
                                            $contentMessage = preg_replace("/\r\n/", "<br/>", $contentMessage);
                                            $data = [
                                                "name" => $clientNameExploded[0],
                                                "boleto_link" => $boletoLink,
                                                "boleto_digitable_line" => $boletoDigitableLine,
                                                "boleto_due_date" => $boleto->boleto_due_date,
                                                "total_paid_value" => $boleto->total_paid_value,
                                                "shipment_value" => $boleto->shipment_value,
                                                "subtotal" => strval($subTotal),
                                                "discount" => $discount,
                                                "project_logo" => $checkoutConfig->checkout_logo,
                                                "subject" => $subjectMessage,
                                                "title" => $titleMessage,
                                                "content" => $contentMessage,
                                                "products" => $products,
                                            ];
                                            $dataEmail = [
                                                "domainName" => $domain["name"],
                                                "projectName" => $project["name"] ?? "",
                                                "clientEmail" => $clientEmail,
                                                "clientName" => $clientNameExploded[0] ?? "",
                                                "templateId" => "d-72259c31ebab40d69d903992ac1a963d", /// done
                                                "bodyEmail" => $data,
                                                "checkout_id" => $checkout->id,
                                            ];
                                            event(new SendEmailEvent($dataEmail));
                                        }
                                    }
                                }
                            }
                        } catch (Exception $e) {
                            report($e);
                        }
                    }
                });
        } catch (Exception $e) {
            report($e);
        }
    }

    /**
     * Vamos ter que liberar sua mercadoria
     */
    public function verifyBoleto2()
    {
        try {
            $saleModel = new Sale();
            $saleService = new SaleService();
            $projectModel = new Project();
            $domainModel = new Domain();
            $checkoutModel = new Checkout();
            $projectNotificationModel = new ProjectNotification();
            $projectNotificationService = new ProjectNotificationService();
            $domainPresenter = $domainModel->present();

            $startDate = now()
                ->startOfDay()
                ->subDays(2);
            $endDate = now()
                ->endOfDay()
                ->subDays(2);

            $saleModel
                ->with("customer", "plansSales.plan.products")
                ->whereBetween("start_date", [$startDate, $endDate])
                ->where([
                    ["payment_method", "=", "2"],
                    ["status", "=", "2"],
                    ["api_flag", "=", "0"],
                    [DB::raw("(DATE_FORMAT(boleto_due_date,'%Y-%m-%d'))"), "!=", now()->toDateString()],
                ])
                ->chunk(500, function ($boletos) use (
                    $checkoutModel,
                    $saleService,
                    $projectModel,
                    $domainModel,
                    $projectNotificationService,
                    $projectNotificationModel,
                    $domainPresenter
                ) {
                    foreach ($boletos as $boleto) {
                        if ($boleto->api_flag) {
                            continue;
                        }

                        try {
                            $checkout = $checkoutModel->where("id", $boleto->checkout_id)->first();
                            $clientName = $boleto->customer->name;
                            $clientEmail = $boleto->customer->email;

                            $discount = preg_replace("/[^0-9]/", "", $boleto->shopify_discount);
                            if ($discount == 0 || $discount == null) {
                                $discount = "";
                            }
                            if ($discount != "") {
                                $discount = substr_replace($discount, ",", strlen($discount) - 2, 0);
                            }

                            $clientNameExploded = explode(" ", $clientName);
                            $boleto->total_paid_value = preg_replace("/[^0-9]/", "", $boleto->total_paid_value);

                            $boleto->total_paid_value = substr_replace(
                                $boleto->total_paid_value,
                                ",",
                                strlen($boleto->total_paid_value) - 2,
                                0
                            );
                            $products = $saleService->getEmailProducts($boleto->id);
                            $project = $projectModel->with("checkoutConfig")->find($boleto->project_id);
                            $checkoutConfig = $project->checkoutConfig;
                            $domain = $domainModel
                                ->where("project_id", $project->id)
                                ->where("status", $domainPresenter->getStatus("approved"))
                                ->first();
                            $domainName = $domain->name ?? "azcend.com.br";
                            $boletoLink =
                                "https://checkout.{$domainName}/order/" .
                                hashids_encode($boleto->id, "sale_id") .
                                "/download-boleto";

                            if (
                                !empty($domain) &&
                                !empty($clientEmail) &&
                                stristr($clientEmail, "invalido") === false
                            ) {
                                $subTotal = preg_replace("/[^0-9]/", "", $boleto->sub_total);
                                $subTotal = substr_replace($subTotal, ",", strlen($subTotal) - 2, 0);
                                $boleto->shipment_value = preg_replace("/[^0-9]/", "", $boleto->shipment_value);
                                $boleto->shipment_value = substr_replace(
                                    $boleto->shipment_value,
                                    ",",
                                    strlen($boleto->shipment_value) - 2,
                                    0
                                );
                                $boletoDigitableLine = [];
                                $boletoDigitableLine[0] = substr($boleto->boleto_digitable_line, 0, 24);
                                $boletoDigitableLine[1] = substr(
                                    $boleto->boleto_digitable_line,
                                    24,
                                    strlen($boleto->boleto_digitable_line) - 1
                                );
                                $boleto->boleto_due_date = Carbon::parse($boleto->boleto_due_date)->format("d/m/y");

                                //Traz o assunto, titulo e texto do email formatados
                                $projectNotificationPresenter = $projectNotificationModel->present();
                                $projectNotification = $projectNotificationModel
                                    ->where("project_id", $project->id)
                                    ->where(
                                        "notification_enum",
                                        $projectNotificationPresenter->getNotificationEnum(
                                            "email_billet_generated_two_days_later"
                                        )
                                    )
                                    ->where("status", $projectNotificationPresenter->getStatus("active"))
                                    ->first();

                                if (!empty($projectNotification)) {
                                    $message = json_decode($projectNotification->message);
                                    if (!empty($message->title)) {
                                        $subjectMessage = $projectNotificationService->formatNotificationData(
                                            $message->subject,
                                            $boleto,
                                            $project
                                        );
                                        $titleMessage = $projectNotificationService->formatNotificationData(
                                            $message->title,
                                            $boleto,
                                            $project
                                        );
                                        $contentMessage = $projectNotificationService->formatNotificationData(
                                            $message->content,
                                            $boleto,
                                            $project
                                        );
                                        $contentMessage = preg_replace("/\r\n/", "<br/>", $contentMessage);
                                        $data = [
                                            "name" => $clientNameExploded[0],
                                            "boleto_link" => $boletoLink,
                                            "boleto_digitable_line" => $boletoDigitableLine,
                                            "boleto_due_date" => $boleto->boleto_due_date,
                                            "total_paid_value" => $boleto->total_paid_value,
                                            "shipment_value" => $boleto->shipment_value,
                                            "subtotal" => strval($subTotal),
                                            "discount" => $discount,
                                            "project_logo" => $checkoutConfig->checkout_logo,
                                            "subject" => $subjectMessage,
                                            "title" => $titleMessage,
                                            "content" => $contentMessage,
                                            "products" => $products,
                                        ];
                                        $dataEmail = [
                                            "domainName" => $domain["name"],
                                            "projectName" => $project["name"] ?? "",
                                            "clientEmail" => $clientEmail,
                                            "clientName" => $clientNameExploded[0] ?? "",
                                            "templateId" => "d-de050e0dd8824c3cb754ee8773bad443", // done
                                            "bodyEmail" => $data,
                                            "checkout_id" => $checkout->id,
                                        ];
                                        event(new SendEmailEvent($dataEmail));
                                    }
                                }
                            }
                        } catch (Exception $e) {
                            report($e);
                        }
                    }
                });
        } catch (Exception $e) {
            report($e);
        }
    }

    public function changeBoletoPendingToCanceled()
    {
        $compensationDays = 2;
        $compensationDate = Carbon::now()
            ->subDay($compensationDays)
            ->toDateString();
        $todayDate = Carbon::now()->toDateString();

        try {
            $saleService = new SaleService();

            $boletos = Sale::where([
                ["payment_method", Sale::BILLET_PAYMENT],
                ["status", Sale::STATUS_PENDING],
                [DB::raw("(DATE_FORMAT(boleto_due_date,'%Y-%m-%d'))"), "<", $compensationDate],
            ]);

            foreach ($boletos->cursor() as $boleto) {
                //verificando se prazo de compensação foi final de semana
                $bankSlipCompensationDate = Carbon::parse($boleto->boleto_due_date)->addDay($compensationDays);
                if ($bankSlipCompensationDate->isWeekend()) {
                    $bankSlipCompensationDate = $bankSlipCompensationDate->nextWeekday();
                }
                $dueDate = $bankSlipCompensationDate->toDateString();

                if ($dueDate >= $todayDate) {
                    continue;
                }

                $boleto->update([
                    "status" => Sale::STATUS_CANCELED,
                    "gateway_status" => "canceled",
                ]);

                SaleService::createSaleLog($boleto->id, "canceled");

                foreach ($boleto->transactions as $transaction) {
                    $transaction->update([
                        "status" => "canceled",
                        "status_enum" => Transaction::STATUS_CANCELED,
                    ]);
                }

                if (!$boleto->api_flag && $boleto->owner_id > User::DEMO_ID) {
                    event(new BilletExpiredEvent($boleto));
                }
            }
        } catch (Exception $e) {
            report($e);
        }
    }
}
