<?php

namespace Modules\Core\Services;

use Exception;
use function foo\func;
use Illuminate\Support\Carbon;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\User;
use Modules\Core\Entities\Domain;
use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\Project;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\Checkout;
use Modules\Core\Entities\Customer;
use Modules\Core\Events\SendSmsEvent;
use Modules\Core\Entities\Transaction;
use Modules\Core\Events\SendEmailEvent;
use Modules\Core\Events\BoletoPaidEvent;
use Modules\Core\Events\BilletExpiredEvent;
use Modules\Core\Entities\ProjectNotification;

/**
 * Class BoletoService
 * @package Modules\Core\Services
 */
class BoletoService
{
    /**
     * Hoje vence o seu pedido
     */
    public function verifyBoletosExpiring()
    {
        try {
            $saleModel                  = new Sale();
            $saleService                = new SaleService();
            $projectModel               = new Project();
            $domainModel                = new Domain();
            $checkoutModel              = new Checkout();
            $projectNotificationModel   = new ProjectNotification();
            $projectNotificationService = new ProjectNotificationService();

            $saleModel->where([
                                  ['payment_method', '=', '2'], ['status', '=', '2'],
                                  [
                                      DB::raw("(DATE_FORMAT(boleto_due_date,'%Y-%m-%d'))"), now()->toDateString(),
                                  ],
                              ])
                      ->with('customer', 'plansSales.plan.products')
                      ->chunk(100, function($boletoDueToday) use ($projectModel, $domainModel, $checkoutModel, $saleService, $projectNotificationService, $projectNotificationModel) {

                          foreach ($boletoDueToday as $boleto) {
                              $checkout    = $checkoutModel->where('id', $boleto->checkout_id)
                                                           ->first();
                              $clientName  = $boleto->customer->name;
                              $clientEmail = $boleto->customer->email;

                              $subTotal = preg_replace("/[^0-9]/", "", $boleto->sub_total);
                              $iof      = preg_replace("/[^0-9]/", "", $boleto->iof);
                              $discount = preg_replace("/[^0-9]/", "", $boleto->shopify_discount);
                              if ($iof == 0) {
                                  $iof = '';
                              } else {
                                  $iof = substr_replace($iof, ',', strlen($iof) - 2, 0);
                              }

                              if ($discount == 0 || $discount == null) {
                                  $discount = '';
                              }
                              if ($discount != '') {
                                  $iof = substr_replace($iof, ',', strlen($iof) - 2, 0);
                              }

                              $clientNameExploded       = explode(' ', $clientName);
                              $boleto->total_paid_value = preg_replace("/[^0-9]/", "", $boleto->iof) + preg_replace("/[^0-9]/", "", $boleto->total_paid_value);

                              if ($discount != '') {
                                  $boleto->total_paid_value = $boleto->total_paid_value - preg_replace("/[^0-9]/", "", $discount);
                                  $discount                 = substr_replace($discount, ',', strlen($discount) - 2, 0);
                              }

                              $boleto->total_paid_value = substr_replace($boleto->total_paid_value, ',', strlen($boleto->total_paid_value) - 2, 0);

                              $products = $saleService->getEmailProducts($boleto->id);
                              $project  = $projectModel->find($boleto->project_id);
                              $domain   = $domainModel->where('project_id', $project->id)
                                                      ->where('status', 3)
                                                      ->first(); // dominio tem que estar aprovado

                              $subTotal                = substr_replace($subTotal, ',', strlen($subTotal) - 2, 0);
                              $boleto->shipment_value  = preg_replace("/[^0-9]/", "", $boleto->shipment_value);
                              $boleto->shipment_value  = substr_replace($boleto->shipment_value, ',', strlen($boleto->shipment_value) - 2, 0);
                              $boletoDigitableLine     = [];
                              $boletoDigitableLine[0]  = substr($boleto->boleto_digitable_line, 0, 24);
                              $boletoDigitableLine[1]  = substr($boleto->boleto_digitable_line, 24, strlen($boleto->boleto_digitable_line) - 1);
                              $boleto->boleto_due_date = Carbon::parse($boleto->boleto_due_date)
                                                               ->format('d/m/y');

                              $clientTelephone = $boleto->customer->telephone;

                              //Traz a mensagem do sms formatado
                              $projectNotificationPresenter = $projectNotificationModel->present();
                              $projectNotificationSms       = $projectNotificationModel->where('project_id', $project->id)
                                                                                       ->where('notification_enum', $projectNotificationPresenter->getNotificationEnum('sms_billet_due_today'))
                                                                                       ->where('status', $projectNotificationPresenter->getStatus('active'))
                                                                                       ->first();
                              if (!empty($projectNotificationSms)) {
                                  $message    = $projectNotificationSms->message;
                                  $smsMessage = $projectNotificationService->formatNotificationData($message, $boleto, $project, 'sms');
                                  if (!empty($smsMessage) && !empty($clientTelephone)) {
                                      $data = [
                                          'message'   => $smsMessage,
                                          'telephone' => $clientTelephone,
                                          'checkout'  => $checkout,

                                      ];
                                      event(new SendSmsEvent($data));
                                  }
                              }
                              //Traz o assunto, titulo e texto do email formatados
                              $projectNotificationPresenter = $projectNotificationModel->present();
                              $projectNotificationEmail     = $projectNotificationModel->where('project_id', $project->id)
                                                                                       ->where('notification_enum', $projectNotificationPresenter->getNotificationEnum('email_billet_due_today'))
                                                                                       ->where('status', $projectNotificationPresenter->getStatus('active'))
                                                                                       ->first();

                              if (!empty($projectNotificationEmail) && !empty($domain) && !empty($clientEmail)) {
                                  $message = json_decode($projectNotificationEmail->message);
                                  if (!empty($message->title)) {

                                      $subjectMessage = $projectNotificationService->formatNotificationData($message->subject, $boleto, $project);
                                      $titleMessage   = $projectNotificationService->formatNotificationData($message->title, $boleto, $project);
                                      $contentMessage = $projectNotificationService->formatNotificationData($message->content, $boleto, $project);
                                      $contentMessage = preg_replace("/\r\n/", "<br/>", $contentMessage);
                                      $data           = [
                                          "name"                  => $clientNameExploded[0],
                                          "boleto_link"           => $boleto->boleto_link,
                                          "boleto_digitable_line" => $boletoDigitableLine,
                                          "boleto_due_date"       => $boleto->boleto_due_date,
                                          "total_paid_value"      => $boleto->total_paid_value,
                                          "shipment_value"        => $boleto->shipment_value,
                                          "subtotal"              => strval($subTotal),
                                          "iof"                   => $iof,
                                          'discount'              => $discount,
                                          "project_logo"          => $project->logo,
                                          "project_contact"       => $project->contact,
                                          "subject"               => $subjectMessage,
                                          "title"                 => $titleMessage,
                                          "content"               => $contentMessage,
                                          "products"              => $products,
                                          'sac_link'              => "https://sac." . $domain->name,
                                      ];
                                      $dataEmail      = [
                                          'domainName'  => $domain['name'],
                                          'projectName' => $project['name'] ?? '',
                                          'clientEmail' => $clientEmail,
                                          'clientName'  => $clientNameExploded[0] ?? '',
                                          //'templateId'  => 'd-957fe3c5ecc6402dbd74e707b3d37a9b',
                                          'templateId'  => 'd-32a6a7b666ed49f6be2392ba8a5f6973',
                                          'bodyEmail'   => $data,
                                          'checkout'    => $checkout,
                                      ];
                                      event(new SendEmailEvent($dataEmail));
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
            $saleModel                  = new Sale();
            $saleService                = new SaleService();
            $checkoutModel              = new Checkout();
            $projectModel               = new Project();
            $domainModel                = new Domain();
            $projectNotificationModel   = new ProjectNotification();
            $projectNotificationService = new ProjectNotificationService();

            $startDate = now()->startOfDay()->subDay();
            $endDate   = now()->endOfDay()->subDay();

            $saleModel->with('customer', 'plansSales.plan.products')
                      ->whereBetween('start_date', [$startDate, $endDate])
                      ->where(
                          [
                              ['payment_method', '=', '2'],
                              ['status', '=', '2'],
                              [DB::raw("(DATE_FORMAT(boleto_due_date,'%Y-%m-%d'))"), '!=', now()->toDateString()],
                          ]
                      )
                      ->chunk(100, function($boletos) use ($checkoutModel, $saleService, $projectModel, $domainModel, $projectNotificationService, $projectNotificationModel) {
                          foreach ($boletos as $boleto) {
                              try {
                                  $checkout    = $checkoutModel->where("id", $boleto->checkout_id)->first();
                                  $clientName  = $boleto->customer->name;
                                  $clientEmail = $boleto->customer->email;
                                  $subTotal    = preg_replace("/[^0-9]/", "", $boleto->sub_total);
                                  $iof         = preg_replace("/[^0-9]/", "", $boleto->iof);
                                  $discount    = preg_replace("/[^0-9]/", "", $boleto->shopify_discount);

                                  if ($iof == 0) {
                                      $iof = '';
                                  } else {
                                      $iof = substr_replace($iof, ',', strlen($iof) - 2, 0);
                                  }
                                  if ($discount == 0 || $discount == null) {
                                      $discount = '';
                                  }
                                  if ($discount != '') {
                                      $discount = substr_replace($discount, ',', strlen($discount) - 2, 0);
                                  }

                                  $clientNameExploded       = explode(' ', $clientName);
                                  $boleto->total_paid_value = preg_replace("/[^0-9]/", "", $boleto->iof) + preg_replace("/[^0-9]/", "", $boleto->total_paid_value);
                                  if ($discount != '') {
                                      $boleto->total_paid_value = $boleto->total_paid_value - preg_replace("/[^0-9]/", "", $discount);
                                  }

                                  $boleto->total_paid_value = substr_replace($boleto->total_paid_value, ',', strlen($boleto->total_paid_value) - 2, 0);
                                  $products                 = $saleService->getEmailProducts($boleto->id);
                                  $project                  = $projectModel->find($boleto->project_id);
                                  $domain                   = $domainModel->where('project_id', $project->id)
                                                                          ->where('status', 3)
                                                                          ->first();
                                  if (!empty($domain) && !empty($clientEmail)) {
                                      $subTotal                = substr_replace($subTotal, ',', strlen($subTotal) - 2, 0);
                                      $boleto->shipment_value  = preg_replace("/[^0-9]/", "", $boleto->shipment_value);
                                      $boleto->shipment_value  = substr_replace($boleto->shipment_value, ',', strlen($boleto->shipment_value) - 2, 0);
                                      $boletoDigitableLine     = [];
                                      $boletoDigitableLine[0]  = substr($boleto->boleto_digitable_line, 0, 24);
                                      $boletoDigitableLine[1]  = substr($boleto->boleto_digitable_line, 24, strlen($boleto->boleto_digitable_line) - 1);
                                      $boleto->boleto_due_date = Carbon::parse($boleto->boleto_due_date)
                                                                       ->format('d/m/y');

                                      //Traz o assunto, titulo e texto do email formatados
                                      $projectNotificationPresenter = $projectNotificationModel->present();
                                      $projectNotification          = $projectNotificationModel->where('project_id', $project->id)
                                                                                               ->where('notification_enum', $projectNotificationPresenter->getNotificationEnum('email_billet_generated_next_day'))
                                                                                               ->where('status', $projectNotificationPresenter->getStatus('active'))
                                                                                               ->first();
                                      if (!empty($projectNotification)) {
                                          $message = json_decode($projectNotification->message);
                                          if (!empty($message->title)) {
                                              $subjectMessage = $projectNotificationService->formatNotificationData($message->subject, $boleto, $project);
                                              $titleMessage   = $projectNotificationService->formatNotificationData($message->title, $boleto, $project);
                                              $contentMessage = $projectNotificationService->formatNotificationData($message->content, $boleto, $project);
                                              $contentMessage = preg_replace("/\r\n/", "<br/>", $contentMessage);
                                              $data           = [
                                                  "name"                  => $clientNameExploded[0],
                                                  "boleto_link"           => $boleto->boleto_link,
                                                  "boleto_digitable_line" => $boletoDigitableLine,
                                                  "boleto_due_date"       => $boleto->boleto_due_date,
                                                  "total_paid_value"      => $boleto->total_paid_value,
                                                  "shipment_value"        => $boleto->shipment_value,
                                                  "subtotal"              => strval($subTotal),
                                                  "iof"                   => $iof,
                                                  'discount'              => $discount,
                                                  "project_logo"          => $project->logo,
                                                  "project_contact"       => $project->contact,
                                                  "subject"               => $subjectMessage,
                                                  "title"                 => $titleMessage,
                                                  "content"               => $contentMessage,
                                                  "products"              => $products,
                                                  'sac_link'              => "https://sac." . $domain->name,
                                              ];
                                              $dataEmail      = [
                                                  'domainName'  => $domain['name'],
                                                  'projectName' => $project['name'] ?? '',
                                                  'clientEmail' => $clientEmail,
                                                  'clientName'  => $clientNameExploded[0] ?? '',
                                                  //'templateId'  => 'd-59dab7e71d4045e294cb6a14577da236',
                                                  'templateId'  => 'd-32a6a7b666ed49f6be2392ba8a5f6973',
                                                  'bodyEmail'   => $data,
                                                  'checkout'    => $checkout,
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

    /**
     * Vamos ter que liberar sua mercadoria
     */
    public function verifyBoleto2()
    {
        try {
            $saleModel                  = new Sale();
            $saleService                = new SaleService();
            $projectModel               = new Project();
            $domainModel                = new Domain();
            $checkoutModel              = new Checkout();
            $projectNotificationModel   = new ProjectNotification();
            $projectNotificationService = new ProjectNotificationService();

            $startDate = now()->startOfDay()->subDays(2);
            $endDate   = now()->endOfDay()->subDays(2);

            $saleModel->with('customer', 'plansSales.plan.products')
                      ->whereBetween('start_date', [$startDate, $endDate])
                      ->where(
                          [
                              ['payment_method', '=', '2'],
                              ['status', '=', '2'],
                              [DB::raw("(DATE_FORMAT(boleto_due_date,'%Y-%m-%d'))"), '!=', now()->toDateString()],
                          ]
                      )
                      ->chunk(100, function($boletos) use ($checkoutModel, $saleService, $projectModel, $domainModel, $projectNotificationService, $projectNotificationModel) {
                          foreach ($boletos as $boleto) {
                              try {
                                  $checkout    = $checkoutModel->where("id", $boleto->checkout_id)->first();
                                  $clientName  = $boleto->customer->name;
                                  $clientEmail = $boleto->customer->email;
                                  $subTotal    = preg_replace("/[^0-9]/", "", $boleto->sub_total);
                                  $iof         = preg_replace("/[^0-9]/", "", $boleto->iof);
                                  $discount    = preg_replace("/[^0-9]/", "", $boleto->shopify_discount);
                                  if ($iof == 0) {
                                      $iof = '';
                                  } else {
                                      $iof = substr_replace($iof, ',', strlen($iof) - 2, 0);
                                  }
                                  if ($discount == 0 || $discount == null) {
                                      $discount = '';
                                  }
                                  if ($discount != '') {
                                      $discount = substr_replace($discount, ',', strlen($discount) - 2, 0);
                                  }
                                  $clientNameExploded       = explode(' ', $clientName);
                                  $boleto->total_paid_value = preg_replace("/[^0-9]/", "", $boleto->iof) + preg_replace("/[^0-9]/", "", $boleto->total_paid_value);

                                  if ($discount != '') {
                                      $boleto->total_paid_value = $boleto->total_paid_value - preg_replace("/[^0-9]/", "", $discount);
                                  }

                                  $boleto->total_paid_value = substr_replace($boleto->total_paid_value, ',', strlen($boleto->total_paid_value) - 2, 0);
                                  $products                 = $saleService->getEmailProducts($boleto->id);
                                  $project                  = $projectModel->find($boleto->project_id);
                                  $domain                   = $domainModel->where('project_id', $project->id)
                                                                          ->where('status', 3)
                                                                          ->first();
                                  if (!empty($domain) && !empty($clientEmail)) {
                                      $subTotal                = substr_replace($subTotal, ',', strlen($subTotal) - 2, 0);
                                      $boleto->shipment_value  = preg_replace("/[^0-9]/", "", $boleto->shipment_value);
                                      $boleto->shipment_value  = substr_replace($boleto->shipment_value, ',', strlen($boleto->shipment_value) - 2, 0);
                                      $boletoDigitableLine     = [];
                                      $boletoDigitableLine[0]  = substr($boleto->boleto_digitable_line, 0, 24);
                                      $boletoDigitableLine[1]  = substr($boleto->boleto_digitable_line, 24, strlen($boleto->boleto_digitable_line) - 1);
                                      $boleto->boleto_due_date = Carbon::parse($boleto->boleto_due_date)
                                                                       ->format('d/m/y');

                                      //Traz o assunto, titulo e texto do email formatados
                                      $projectNotificationPresenter = $projectNotificationModel->present();
                                      $projectNotification          = $projectNotificationModel->where('project_id', $project->id)
                                                                                               ->where('notification_enum', $projectNotificationPresenter->getNotificationEnum('email_billet_generated_two_days_later'))
                                                                                               ->where('status', $projectNotificationPresenter->getStatus('active'))
                                                                                               ->first();

                                      if (!empty($projectNotification)) {
                                          $message = json_decode($projectNotification->message);
                                          if (!empty($message->title)) {
                                              $subjectMessage = $projectNotificationService->formatNotificationData($message->subject, $boleto, $project);
                                              $titleMessage   = $projectNotificationService->formatNotificationData($message->title, $boleto, $project);
                                              $contentMessage = $projectNotificationService->formatNotificationData($message->content, $boleto, $project);
                                              $contentMessage = preg_replace("/\r\n/", "<br/>", $contentMessage);
                                              $data           = [
                                                  "name"                  => $clientNameExploded[0],
                                                  "boleto_link"           => $boleto->boleto_link,
                                                  "boleto_digitable_line" => $boletoDigitableLine,
                                                  "boleto_due_date"       => $boleto->boleto_due_date,
                                                  "total_paid_value"      => $boleto->total_paid_value,
                                                  "shipment_value"        => $boleto->shipment_value,
                                                  "subtotal"              => strval($subTotal),
                                                  "iof"                   => $iof,
                                                  'discount'              => $discount,
                                                  "project_logo"          => $project->logo,
                                                  "project_contact"       => $project->contact,
                                                  "subject"               => $subjectMessage,
                                                  "title"                 => $titleMessage,
                                                  "content"               => $contentMessage,
                                                  "products"              => $products,
                                                  'sac_link'              => "https://sac." . $domain->name,
                                              ];
                                              $dataEmail      = [
                                                  'domainName'  => $domain['name'],
                                                  'projectName' => $project['name'] ?? '',
                                                  'clientEmail' => $clientEmail,
                                                  'clientName'  => $clientNameExploded[0] ?? '',
                                                  //'templateId'  => 'd-690a6140f72643c1af280b079d5e84c5',
                                                  'templateId'  => 'd-792f7ecb932e40e09403149653e013e1',
                                                  'bodyEmail'   => $data,
                                                  'checkout'    => $checkout,
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

    /**
     *  Boletos Compensados
     * @return void
     */
    public function verifyBoletoPaid()
    {
        try {
            $userModel   = new User();
            $sql         = 'SELECT u.id owner_id
                            , u.email
                            , COUNT(DISTINCT s.id) as boleto_count
                            , COUNT(DISTINCT t.id) as transactions_boleto_count
                            , SUM(t.value) as transactions_amount
                            FROM users u
                            INNER JOIN companies c
                            ON u.id = c.user_id
                            INNER JOIN sales s
                            ON u.id = s.owner_id
                            INNER JOIN transactions t
                            ON t.sale_id = s.id
                            AND t.company_id = c.id
                            WHERE 1 = 1
                            AND s.payment_method = 2
                            AND s.status = 1
                            AND date(s.end_date) = CURRENT_DATE
                            AND t.deleted_at IS NULL
                            GROUP BY u.id
                            , u.email';
            $boletosPaid = DB::select($sql);
            foreach ($boletosPaid as $boleto) {
                try {
                    $user = $userModel->find($boleto->owner_id);
                    if ($boleto->boleto_count == 1) {
                        $message       = 'boleto foi compensado';
                        $messageHeader = 'Boleto Compensado!';
                    } else {
                        $message       = 'boletos foram compensados';
                        $messageHeader = 'Boletos Compensados!';
                    }
                    $data = [
                        'user'              => $user,
                        "name"              => $user->name,
                        'boleto_count'      => strval($boleto->boleto_count),
                        'message'           => $message,
                        'messageHeader'     => $messageHeader,
                        'transaction_value' => "R$ " . number_format(intval($boleto->transactions_amount) / 100, 2, ',', '.'),
                    ];
                    event(new BoletoPaidEvent($data));
                } catch (Exception $e) {
                    Log::warning('Erro ao enviar boleto para e-mail no foreach - Boletos compensados');
                    report($e);
                }
            }
        } catch (Exception $e) {
            Log::warning('Erro ao enviar boletos para e-mails - Boletos compensados');
            report($e);
        }
    }

    public function changeBoletoPendingToCanceled()
    {
        try {

            $saleModel        = new Sale();
            $transactionModel = new Transaction();

            $boletos = $saleModel->with(['customer'])
                                 ->where([
                                             ['payment_method', '=', '2'],
                                             ['status', '=', '2'],
                                             [
                                                 DB::raw("(DATE_FORMAT(boleto_due_date,'%Y-%m-%d'))"), '<=', Carbon::now()
                                                                                                                   ->subDay('2')
                                                                                                                   ->toDateString(),
                                             ],
                                         ]);
            foreach ($boletos->cursor() as $boleto) {
                $boleto->update([
                                    'status'         => 5,
                                    'gateway_status' => 'canceled',
                                ]);

                foreach ($boleto->transactions as $transaction) {
                    $transaction->update([
                                             'status'      => 'canceled',
                                             'status_enum' => $transactionModel->present()->getStatusEnum('canceled'),
                                         ]);
                }

                event(new BilletExpiredEvent($boleto));
            }
        } catch (Exception $e) {
            Log::warning('Erro ao atualizar boleto para status 5 - changeBoletoPendingToCanceled');
            report($e);
        }
    }
}

