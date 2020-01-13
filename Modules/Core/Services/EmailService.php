<?php

namespace Modules\Core\Services;

use Exception;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\Domain;
use SendGrid;
use SendGrid\Mail\Mail;
use Throwable;
use Vinkla\Hashids\Facades\Hashids;

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
            $emailLayout = view('invites::email.invite', [
                'link' => 'https://app.cloudfox.net/register/' . $parameter,
            ]);
            $email       = new Mail();
            $email->setFrom("noreply@cloudfox.net", "Cloudfox");
            $email->setSubject("Convite para o CloudFox");
            $email->addTo($to, "CloudFox");
            $email->addContent(
                "text/html", $emailLayout->render()
            );
            $sendgrid = new SendGrid(getenv('SENDGRID_API_KEY'));

            return $sendgrid->send($email);
        } catch (Exception | Throwable $e) {
            Log::warning('Erro ao enviar email de convite (EmailHelper - sendInvite)');
            report($e);

            return 'error';
        }
    }

    /**
     * @param Sale $sale
     * @return void
     */
    public static function userSaleChargeback(Sale $sale)
    {
        try {
            $sale->loadMissing(["client", "project"]);
            $sendEmail    = new SendgridService();
            $productsSale = $sale->present()->getProducts();
            $subTotal     = $sale->present()->getSubTotal();
            $iof          = preg_replace("/[^0-9]/", "", $sale->iof);
            $discount     = preg_replace("/[^0-9]/", "", $sale->shopify_discount);

            if ($iof == 0) {
                $iof = '';
            }

            if ($discount == 0 || $discount == null) {
                $discount = '';
            }

            $subTotal = substr_replace($subTotal, ',', strlen($subTotal) - 2, 0);

            if ($discount != '') {
                $discount = substr_replace($discount, ',', strlen($discount) - 2, 0);
            }

            $domain = Domain::where([
                                        ['project_id', $sale->project->id],
                                        ['status', 3],
                                    ])->first();

            if (getenv('APP_ENV') != 'production') {
                $sale->client->email = getenv('EMAIL_TEST');
            }

            $data = [
                'transaction'         => Hashids::connection('sale_id')->encode($sale->id),
                'products'            => $productsSale,
                'subtotal'            => $subTotal,
                'shipment_value'      => $sale->shipment_value,
                'iof'                 => $iof,
                'discount'            => $discount,
                'project_contact'     => $sale->project->contact,
                'installments_amount' => $sale->installments_amount,
                'installments_value'  => number_format($sale->installments_value, 2, ',', '.'),
            ];

            if (stristr($sale->client->email, 'invalido') === false) {
                $sendEmail->sendEmail('noreply@' . $domain->name, $sale->project->name, $sale->client->email, $sale->client->present()
                                                                                                                           ->getFirstName(), 'd-ed70ee0df3a04153aa835e8e4f652434', $data);
            }

            return;
        } catch (Exception $ex) {
            Log::warning('erro ao enviar email de chargeback para o cliente na venda ' . $sale->id);
            report($ex);

            return;
        }
    }
}

