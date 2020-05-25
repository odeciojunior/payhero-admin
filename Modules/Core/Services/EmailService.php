<?php

namespace Modules\Core\Services;

use Exception;
use SendGrid;
use SendGrid\Mail\Mail;
use Throwable;
use Vinkla\Hashids\Facades\Hashids;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\Customer;
use Modules\Core\Entities\Project;
use Modules\Core\Entities\Domain;
use Carbon\Carbon;

/**
 * Class EmailService
 * @package Modules\Core\Services
 */
class EmailService
{
    /**
     * @param $to
     * @param $parameter
     * @return SendGrid\Response|string
     */
    public function sendInvite($to, $parameter)
    {
        try {
            $emailLayout = view(
                'invites::email.invite',
                [
                    'link' => 'https://app.cloudfox.net/register/' . $parameter,
                ]
            );
            $email = new Mail();
            $email->setFrom("noreply@cloudfox.net", "Cloudfox");
            $email->setSubject("Convite para o CloudFox");
            $email->addTo($to, "CloudFox");
            $email->addContent(
                "text/html",
                $emailLayout->render()
            );
            $sendgrid = new SendGrid(getenv('SENDGRID_API_KEY'));

            return $sendgrid->send($email);
        } catch (Exception | Throwable $e) {
            report($e);

            return 'error';
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

            $domain = $domainModel->where('project_id', $sale->project_id)
                ->where('status', $domainPresent->getStatus('approved'))->first();
            if (empty($domain)) {
                return false;
            }

            $sale->loadMissing(["customer", "project"]);
            if (stristr($sale->customer->email, 'invalido') !== false) {
                return false;
            }

            $sendEmail = new SendgridService();
            $productsSale = $sale->present()->getProducts();

            $subTotal = $sale->present()->getSubTotal();
            $subTotal = substr_replace($subTotal, ',', strlen($subTotal) - 2, 0);

            $discount = preg_replace("/[^0-9]/", "", $sale->shopify_discount);
            if ($discount == 0 || $discount == null) {
                $discount = '';
            }
            if ($discount != '') {
                $discount = substr_replace($discount, ',', strlen($discount) - 2, 0);
            }

            $data = [
                'transaction' => Hashids::connection('sale_id')->encode($sale->id),
                'products' => $productsSale,
                'subtotal' => $subTotal,
                'shipment_value' => $sale->shipment_value,
                'discount' => $discount,
                'project_contact' => $sale->project->contact,
                'installments_amount' => $sale->installments_amount,
                'installments_value' => number_format($sale->installments_value, 2, ',', '.'),
            ];

            $sendEmail->sendEmail(
                'noreply@' . $domain->name,
                $sale->project->name,
                $sale->customer->email,
                $sale->customer->present()
                    ->getFirstName(),
                'd-ed70ee0df3a04153aa835e8e4f652434',
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
     * @param Project $project
     * @return bool
     */
    public static function clientSale(Customer $customer, Sale $sale, Project $project)
    {
        try {
            $domainModel = new Domain();
            $domainPresent = $domainModel->present();

            $domain = $domainModel->where('project_id', $sale->project_id)
                ->where('status', $domainPresent->getStatus('approved'))->first();

            if (empty($domain)) {
                return false;
            }

            if (stristr($customer->email, 'invalido') !== false) {
                return false;
            }

            $sendEmail = new SendgridService();
            $productsSale = $sale->present()->getProducts();
            $saleCode = Hashids::connection('sale_id')->encode($sale->id);

            $discount = preg_replace("/[^0-9]/", "", $sale->shopify_discount);
            if ($discount == 0 || $discount == null) {
                $discount = '';
            }
            if ($discount != '') {
                $discount = substr_replace($discount, ',', strlen($discount) - 2, 0);
            }

            $totalPaidValue = preg_replace("/[^0-9]/", "", $sale->total_paid_value);
            $totalPaidValue = substr_replace($totalPaidValue, ',', strlen($totalPaidValue) - 2, 0);

            $subTotal = $sale->present()->getSubTotal();
            $subTotal = substr_replace($subTotal, ',', strlen($subTotal) - 2, 0);

            $shipmentValue = preg_replace("/[^0-9]/", "", $sale->shipment_value);
            $shipmentValue = substr_replace($shipmentValue, ',', strlen($shipmentValue) - 2, 0);


            if ($sale->payment_method == $sale->present()->getPaymentType('boleto')) {
                $boletoDigitableLine = [];
                $boletoDigitableLine[0] = substr($sale->boleto_digitable_line, 0, 24);
                $boletoDigitableLine[1] = substr(
                    $sale->boleto_digitable_line,
                    24,
                    strlen($sale->boleto_digitable_line) - 1
                );

                $data = [
                    'first_name' => $customer->present()->getFirstName(),
                    'boleto_link' => $sale->boleto_link,
                    'digitable_line' => $boletoDigitableLine,
                    'expiration_date' => Carbon::parse($sale->boleto_due_date)->format('d/m/Y'),
                    'total_value' => $totalPaidValue,
                    'products' => $productsSale,
                    'shipment_value' => $shipmentValue,
                    'subtotal' => $subTotal,
                    'store_logo' => $project->logo,
                    'discount' => $discount,
                    'project_contact' => $project->contact,
                    'sale_code' => $saleCode,
                ];
                $sendEmail->sendEmail(
                    'noreply@' . $domain->name,
                    $project->name,
                    $customer->email,
                    $customer->present()
                        ->getFirstName(),
                    'd-c521a65b247645a9b5f7be6b9b0db262',
                    $data
                );
            } else {
                $data = [
                    'first_name' => $customer->present()->getFirstName(),
                    'installments_amount' => $sale->installments_amount,
                    'installments_value' => number_format($sale->installments_value, 2, ',', '.'),
                    'products' => $productsSale,
                    'shipment_value' => $sale->shipment_value,
                    'subtotal' => $subTotal,
                    'store_logo' => $project->logo,
                    'discount' => $discount,
                    'project_contact' => $project->contact,
                    'sale_code' => $saleCode,
                ];

                $sendEmail->sendEmail(
                    'noreply@' . $domain->name,
                    $project->name,
                    $customer->email,
                    $customer->present()->getFirstName(),
                    'd-b80c0854a9d342428532d8d4b0e2f654',
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

            if (getenv('APP_ENV') != 'production') {
                $fromEmail = getenv('EMAIL_TEST');
            }

            if (stristr($fromEmail, 'invalido') === false &&
                !empty($fromName) && !empty($toEmail) &&
                !empty($toName) && !empty($templateId)
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
}

