<?php

namespace Modules\Core\Services;

use Carbon\Carbon;
use Exception;
use Modules\Core\Entities\Customer;
use Modules\Core\Entities\Domain;
use Modules\Core\Entities\Project;
use Modules\Core\Entities\Sale;
use SendGrid;
use SendGrid\Mail\Mail;
use Throwable;
use Vinkla\Hashids\Facades\Hashids;

/**
 * Class EmailService
 * @package Modules\Core\Services
 */
class EmailService
{
    public const EMAIL_HELP_CLOUDFOX = "noreply@azcend.com.br";

    public const TEMPLATE_ID_EMAIL_CHARGEBACK = "d-b202c8d57c2445658fc5e53a74f32ba0"; /// done

    private SendgridService $sendgridService;

    public function __construct()
    {
        $this->sendgridService = new SendgridService();
    }

    /**
     * @param $to
     * @param $parameter
     * @return SendGrid\Response|string
     */
    public function sendInvite($to, $parameter)
    {
        try {
            $emailLayout = view("invites::email.invite", [
                "logo" =>
                    (env("APP_ENV") != "production" ? env("APP_URL") : "https://admin.azcend.com.br") .
                    "/build/global/img/logos/2021/logo-primary.png",
                "link" => env("ACCOUNT_FRONT_URL") . "/signup?i=" . $parameter,
            ]);

            if (env("APP_ENV") == "local") {
                $to = env("APP_EMAIL_TEST");
            }

            $email = new Mail();
            $email->setFrom("noreply@azcend.com.br", "Azcend");
            $email->setSubject("Convite para o Azcend");
            $email->addTo($to, "Azcend");
            $email->addContent("text/html", $emailLayout->render());
            $sendgrid = new SendGrid(getenv("SENDGRID_API_KEY"));

            return $sendgrid->send($email);
        } catch (Exception | Throwable $e) {
            report($e);

            return "error";
        }
    }

    /**
     * @param Sale $sale
     * @return bool|void
     */
    public static function userSaleChargeback(Sale $sale)
    {
        try {
            $domainModel = new Domain();
            $domainPresent = $domainModel->present();

            $domain = $domainModel
                ->where("project_id", $sale->project_id)
                ->where("status", $domainPresent->getStatus("approved"))
                ->first();
            if (empty($domain)) {
                return false;
            }

            $sale->loadMissing(["customer", "project"]);
            if (stristr($sale->customer->email, "invalido") !== false) {
                return false;
            }

            $sendEmail = new SendgridService();
            $productsSale = $sale->present()->getProducts();

            $subTotal = $sale->present()->getSubTotal();
            $subTotal = substr_replace($subTotal, ",", strlen($subTotal) - 2, 0);

            $discount = preg_replace("/[^0-9]/", "", $sale->shopify_discount);
            if ($discount == 0 || $discount == null) {
                $discount = "";
            }
            if ($discount != "") {
                $discount = substr_replace($discount, ",", strlen($discount) - 2, 0);
            }

            $data = [
                "transaction" => Hashids::connection("sale_id")->encode($sale->id),
                "products" => $productsSale,
                "subtotal" => $subTotal,
                "shipment_value" => $sale->shipment_value,
                "discount" => $discount,
                "installments_amount" => $sale->installments_amount,
                "installments_value" => number_format($sale->installments_value, 2, ",", "."),
            ];

            $sendEmail->sendEmail(
                "noreply@" . $domain->name,
                $sale->project->name,
                $sale->customer->email,
                $sale->customer->present()->getFirstName(),
                "d-b202c8d57c2445658fc5e53a74f32ba0", /// done
                $data
            );

            return;
        } catch (Exception $ex) {
            report($ex);

            return;
        }
    }

    /**
     * @param Customer $customer
     * @param Sale $sale
     * @return bool
     */
    public static function clientSale(Customer $customer, Sale $sale, $project)
    {
        try {
            $domainModel = new Domain();
            $domainPresent = $domainModel->present();

            $domain = $domainModel
                ->where("project_id", $sale->project_id)
                ->where("status", $domainPresent->getStatus("approved"))
                ->first();

            if (empty($domain)) {
                return false;
            }

            if (stristr($customer->email, "invalido") !== false) {
                return false;
            }

            $sendEmail = new SendgridService();
            $productsSale = $sale->present()->getProducts();
            $saleCode = Hashids::connection("sale_id")->encode($sale->id);

            $discount = preg_replace("/[^0-9]/", "", $sale->shopify_discount);
            if ($discount == 0 || $discount == null) {
                $discount = "";
            }
            if ($discount != "") {
                $discount = substr_replace($discount, ",", strlen($discount) - 2, 0);
            }

            $totalPaidValue = preg_replace("/[^0-9]/", "", $sale->total_paid_value);
            $totalPaidValue = substr_replace($totalPaidValue, ",", strlen($totalPaidValue) - 2, 0);

            $subTotal = $sale->present()->getSubTotal();
            $subTotal = substr_replace($subTotal, ",", strlen($subTotal) - 2, 0);

            $shipmentValue = preg_replace("/[^0-9]/", "", $sale->shipment_value);
            $shipmentValue = substr_replace($shipmentValue, ",", strlen($shipmentValue) - 2, 0);

            $domainName = $domain->name ?? "cloudfox.net";
            $boletoLink =
                "https://checkout.{$domainName}/order/" .
                Hashids::connection("sale_id")->encode($sale->id) .
                "/download-boleto";

            if ($sale->payment_method == $sale->present()->getPaymentType("boleto")) {
                $boletoDigitableLine = [];
                $boletoDigitableLine[0] = substr($sale->boleto_digitable_line, 0, 24);
                $boletoDigitableLine[1] = substr(
                    $sale->boleto_digitable_line,
                    24,
                    strlen($sale->boleto_digitable_line) - 1
                );

                $data = [
                    "first_name" => $customer->present()->getFirstName(),
                    "boleto_link" => $boletoLink,
                    "digitable_line" => $boletoDigitableLine,
                    "expiration_date" => Carbon::parse($sale->boleto_due_date)->format("d/m/Y"),
                    "total_value" => $totalPaidValue,
                    "products" => $productsSale,
                    "shipment_value" => $shipmentValue,
                    "subtotal" => $subTotal,
                    "store_logo" => $project->checkoutConfig->checkout_logo,
                    "discount" => $discount,
                    "sale_code" => $saleCode,
                ];
                $sendEmail->sendEmail(
                    "noreply@" . $domain->name,
                    $project->name ?? null,
                    $customer->email,
                    $customer->present()->getFirstName(),
                    "d-60753ac1274b448490aae28e81474aad", /// done
                    $data
                );
            } else {
                $data = [
                    "first_name" => $customer->present()->getFirstName(),
                    "installments_amount" => $sale->installments_amount,
                    "installments_value" => number_format($sale->installments_value, 2, ",", "."),
                    "products" => $productsSale,
                    "shipment_value" => $sale->shipment_value,
                    "subtotal" => $subTotal,
                    "store_logo" => $project->checkoutConfig->checkout_logo,
                    "discount" => $discount,
                    "sale_code" => $saleCode,
                ];

                $sendEmail->sendEmail(
                    "noreply@" . $domain->name,
                    $project->name ?? null,
                    $customer->email,
                    $customer->present()->getFirstName(),
                    "d-9bd7cd4c3651497aa8c268e2a3314657", /// done
                    $data
                );
            }

            return true;
        } catch (Exception $e) {
            report($e);

            return false;
        }
    }

    /**
     * @param $fromEmail
     * @param $fromName
     * @param $toEmail
     * @param $toName
     * @param $templateId
     * @param $data
     * @return bool
     */
    public function sendEmail($fromEmail, $fromName, $toEmail, $toName, $templateId, $data)
    {
        try {
            $sendGridService = new SendgridService();

            if (getenv("APP_ENV") != "production") {
                $fromEmail = getenv("APP_EMAIL_TEST");
            }

            if (
                stristr($fromEmail, "invalido") === false &&
                !empty($fromName) &&
                !empty($toEmail) &&
                !empty($toName) &&
                !empty($templateId)
            ) {
                return $sendGridService->sendEmail($fromEmail, $fromName, $toEmail, $toName, $templateId, $data);
            } else {
                return false;
            }
        } catch (Exception $e) {
            report($e);

            return false;
        }
    }

    public function sendEmailUnderAttack($systemsStatus)
    {
        $emailList = [
            "julioleichtweis@gmail.com",
            "felixlorram@gmail.com",
            "jeanvcastro1@gmail.com",
            "murillogomes@azcend.com.br",
            "henriquebrites@live.com",
        ];

        foreach ($emailList as $email) {
            (new SendgridService())->sendEmail(
                "noreply@azcend.com.br",
                "Azcend",
                $email,
                "Admin/Dev",
                "not", // done
                $systemsStatus
            );
        }
    }

    public function sendEmailChargeback(Sale $sale)
    {
        $salePresenter = $sale->present();

        if (empty($sale->user)) {
            $sale->load(["user"]);
        }

        $user = $sale->user;

        $data = [
            "transaction" => hashids_encode($sale->id, "sale_id"),
            "products" => $salePresenter->getProducts(),
            "subtotal" => $salePresenter->getFormattedSubTotal(),
            "shipment_value" => $salePresenter->getFormattedShipmentValue(),
            "discount" => $salePresenter->getFormattedDiscount(),
            "project_contact" => self::EMAIL_HELP_CLOUDFOX,
            "total_value" => $salePresenter->getTotalPaidValueWithoutInstallmentTax(),
        ];

        $this->sendgridService->sendEmail(
            self::EMAIL_HELP_CLOUDFOX,
            "CloudFox",
            $user->email,
            $user->name,
            self::TEMPLATE_ID_EMAIL_CHARGEBACK,
            $data
        );
    }
}
