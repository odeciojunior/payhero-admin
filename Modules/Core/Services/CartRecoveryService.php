<?php

namespace Modules\Core\Services;

use Exception;
use Carbon\Carbon;
use Modules\Core\Entities\Domain;
use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\Project;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\Checkout;
use Modules\Core\Entities\ProjectNotification;
use Modules\Core\Events\SendEmailEvent;
use Modules\Core\Events\SendSmsEvent;
use Modules\Core\Entities\Log as CheckoutLog;

/**
 * Class CartRecoveryService
 * @package Modules\Core\Services
 */
class CartRecoveryService
{
    public function verifyAbandonedCarts()
    {
        try {

            $checkoutModel              = new Checkout();
            $checkoutLogModel           = new CheckoutLog();
            $projectModel               = new Project();
            $domainModel                = new Domain();
            $projectNotificationModel   = new ProjectNotification();
            $projectNotificationService = new ProjectNotificationService();

            $dateStart = new \DateTime();
            $dateEnd   = new \DateTime();

            $dateEnd->modify('-1 hours');
            $dateStart->modify('-75 minutes');
            $formatted_dateStart = $dateStart->format('y-m-d H:i:s');
            $formatted_dateEnd   = $dateEnd->format('y-m-d H:i:s');
            $data                = [];
            $checkoutModel->where([['status', '=', 'abandoned cart'], ['created_at', '>', $formatted_dateStart], ['created_at', '<', $formatted_dateEnd]])
                          ->with('project', 'checkoutPlans.plan.productsPlans.product')
                          ->chunk(100, function($abandonedCarts) use ($checkoutLogModel, $projectModel, $domainModel, $projectNotificationService, $projectNotificationModel) {
                              try {
                                  foreach ($abandonedCarts as $abandonedCart) {
                                      $products = [];
                                      try {
                                          foreach ($abandonedCart->checkoutPlans as $checkoutPlan) {
                                              foreach ($checkoutPlan->getRelation('plan')->productsPlans as $productPlan) {
                                                  $productArray           = [];
                                                  $productArray["name"]   = $productPlan->product->name;
                                                  $productArray["photo"]  = $productPlan->product->photo;
                                                  $productArray["amount"] = $productPlan->amount;
                                                  $products[]             = $productArray;
                                              }
                                          }

                                          $log = $checkoutLogModel->where('id_log_session', $abandonedCart->id_log_session)
                                                                  ->orderBy('created_at', 'desc')
                                                                  ->first();

                                          $project         = $projectModel->find($abandonedCart['project_id']);
                                          $domain          = $domainModel->where('project_id', $project->id)
                                                                         ->first();
                                          $clientTelephone = $log['telephone'];

                                          $linkCheckout       = "https://checkout." . $domain['name'] . "/recovery/" . $log->id_log_session;
                                          $clientNameExploded = explode(' ', $log['name']);

                                          //Traz a mensagem do sms formatado
                                          $projectNotificationPresenter = $projectNotificationModel->present();
                                          $projectNotificationSms       = $projectNotificationModel->where('project_id', $project->id)
                                                                                                   ->where('notification_enum', $projectNotificationPresenter->getNotificationEnum('sms_abandoned_cart_an_hour_later'))
                                                                                                   ->where('status', $projectNotificationPresenter->getStatus('active'))
                                                                                                   ->first();
                                          if (!empty($projectNotificationSms)) {
                                              $message    = $projectNotificationSms->message;
                                              $smsMessage = $projectNotificationService->formatNotificationData($message, null, $project, 'sms', $linkCheckout, $log);
                                              if (!empty($smsMessage) && !empty($clientTelephone)) {
                                                  $dataSms = [
                                                      //'message'   => 'Olá ' . $clientNameExploded[0] . ', somos da loja ' . $project['name'] . ', vimos que você não finalizou seu pedido, aproveite o último dia da promoção: ' . $link,
                                                      'message'   => $smsMessage,
                                                      'telephone' => $clientTelephone,
                                                      'checkout'  => $abandonedCart,
                                                  ];
                                                  event(new SendSmsEvent($dataSms));
                                              }
                                          }

                                          //Traz o assunto, titulo e texto do email formatados
                                          $projectNotificationPresenter = $projectNotificationModel->present();
                                          $projectNotificationEmail     = $projectNotificationModel->where('project_id', $project->id)
                                                                                                   ->where('notification_enum', $projectNotificationPresenter->getNotificationEnum('email_abandoned_cart_an_hour_later'))
                                                                                                   ->where('status', $projectNotificationPresenter->getStatus('active'))
                                                                                                   ->first();
                                          if (!empty($projectNotificationEmail)) {
                                              $message        = json_decode($projectNotificationEmail->message);
                                              $subjectMessage = $projectNotificationService->formatNotificationData($message->subject, null, $project, null, $linkCheckout, $log);
                                              $titleMessage   = $projectNotificationService->formatNotificationData($message->title, null, $project, null, $linkCheckout, $log);
                                              $contentMessage = $projectNotificationService->formatNotificationData($message->content, null, $project, null, $linkCheckout, $log);
                                              if (!empty($domain)) {
                                                  $bodyEmail = [
                                                      'name'            => $clientNameExploded[0],
                                                      'project_logo'    => $project['logo'],
                                                      'checkout_link'   => $linkCheckout,
                                                      "project_contact" => $project['contact'],
                                                      "subject"         => $subjectMessage,
                                                      "title"           => $titleMessage,
                                                      "content"         => $contentMessage,
                                                      "products"        => $products,
                                                  ];

                                                  $dataEmail = [
                                                      'domainName'  => $domain['name'],
                                                      'projectName' => $project['name'] ?? '',
                                                      'clientEmail' => $log['email'],
                                                      'clientName'  => $log['name'] ?? '',
                                                      //'templateId'  => 'd-538d3405815c43debcf48aa44ceab965',
                                                      'templateId'  => 'd-92937608e68b47b79dbd2641fd20fd0d',
                                                      'bodyEmail'   => $bodyEmail,
                                                      'checkout'    => $abandonedCart,

                                                  ];

                                                  event(new SendEmailEvent($dataEmail));
                                              }
                                          }
                                      } catch (Exception $e) {
                                          Log::warning('Erro ao enviar e-mail no foreach - Carrinho abandonado');
                                          report($e);
                                      }
                                  }
                              } catch (Exception $e) {
                                  Log::warning('Erro ao enviar e-mail no foreach - Carrinho abandonado');
                                  report($e);
                              }
                          });
        } catch (Exception $e) {
            Log::warning('Erro ao enviar e-mail - Carrinho abandonado ');
            report($e);
        }
    }

    //Carrinho abandonado dia seguinte
    public function verifyAbandonedCarts2()
    {
        try {
            $checkoutModel              = new Checkout();
            $checkoutLogModel           = new CheckoutLog();
            $projectModel               = new Project();
            $domainModel                = new Domain();
            $projectNotificationModel   = new ProjectNotification();
            $projectNotificationService = new ProjectNotificationService();

            $date = Carbon::now()->subDay('1')->toDateString();
            $data = [];
            $checkoutModel->where([['status', '=', 'abandoned cart'], [DB::raw("(DATE_FORMAT(created_at,'%Y-%m-%d'))"), $date]])
                          ->with('project', 'checkoutPlans.plan.productsPlans.product')
                          ->chunk(100, function($abandonedCarts) use ($checkoutLogModel, $projectModel, $domainModel, $projectNotificationService, $projectNotificationModel) {
                              foreach ($abandonedCarts as $abandonedCart) {
                                  try {

                                      $products = [];

                                      foreach ($abandonedCart->checkoutPlans as $checkoutPlan) {
                                          foreach ($checkoutPlan->getRelation('plan')->productsPlans as $productPlan) {
                                              $productArray           = [];
                                              $productArray["name"]   = $productPlan->product->name;
                                              $productArray["photo"]  = $productPlan->product->photo;
                                              $productArray["amount"] = $productPlan->amount;
                                              $products[]             = $productArray;
                                          }
                                      }

                                      $log = $checkoutLogModel->where('id_log_session', $abandonedCart->id_log_session)
                                                              ->orderBy('created_at', 'desc')
                                                              ->first();

                                      //$telephoneValidated = FoxUtils::prepareCellPhoneNumber($log['telephone']);
                                      $project = $projectModel->find($abandonedCart['project_id']);
                                      $domain  = $domainModel->where('project_id', $project->id)
                                                             ->where('status', 3)
                                                             ->first();

                                      $linkCheckout       = "https://checkout." . $domain['name'] . "/recovery/" . $log->id_log_session;
                                      $clientTelephone    = $log['telephone'];
                                      $clientNameExploded = explode(' ', $log['name']);

                                      //Traz a mensagem do sms formatado
                                      $projectNotificationPresenter = $projectNotificationModel->present();
                                      $projectNotificationSms       = $projectNotificationModel->where('project_id', $project->id)
                                                                                               ->where('notification_enum', $projectNotificationPresenter->getNotificationEnum('sms_abandoned_cart_next_day'))
                                                                                               ->where('status', $projectNotificationPresenter->getStatus('active'))
                                                                                               ->first();
                                      if (!empty($projectNotificationSms)) {
                                          $message    = $projectNotificationSms->message;
                                          $smsMessage = $projectNotificationService->formatNotificationData($message, null, $project, 'sms', $linkCheckout, $log);
                                          if (!empty($smsMessage) && !empty($clientTelephone)) {
                                              $dataSms = [
                                                  'message'   => $smsMessage,
                                                  'telephone' => $clientTelephone,
                                                  'checkout'  => $abandonedCart,
                                              ];
                                              event(new SendSmsEvent($dataSms));
                                          }
                                      }
                                      //Traz o assunto, titulo e texto do email formatados
                                      $projectNotificationPresenter = $projectNotificationModel->present();
                                      $projectNotificationEmail     = $projectNotificationModel->where('project_id', $project->id)
                                                                                               ->where('notification_enum', $projectNotificationPresenter->getNotificationEnum('email_abandoned_cart_next_day'))
                                                                                               ->where('status', $projectNotificationPresenter->getStatus('active'))
                                                                                               ->first();
                                      if (!empty($projectNotificationEmail)) {
                                          $message        = json_decode($projectNotificationEmail->message);
                                          $subjectMessage = $projectNotificationService->formatNotificationData($message->subject, null, $project, null, $linkCheckout, $log);
                                          $titleMessage   = $projectNotificationService->formatNotificationData($message->title, null, $project, null, $linkCheckout, $log);
                                          $contentMessage = $projectNotificationService->formatNotificationData($message->content, null, $project, null, $linkCheckout, $log);
                                          if (!empty($domain)) {
                                              $bodyEmail = [
                                                  'name'            => $clientNameExploded[0],
                                                  'project_logo'    => $project['logo'],
                                                  'checkout_link'   => $linkCheckout,
                                                  "project_contact" => $project['contact'],
                                                  "subject"         => $subjectMessage,
                                                  "title"           => $titleMessage,
                                                  "content"         => $contentMessage,
                                                  "products"        => $products,
                                              ];

                                              $dataEmail = [
                                                  'domainName'  => $domain['name'],
                                                  'projectName' => $project['name'] ?? '',
                                                  'clientEmail' => $log['email'],
                                                  'clientName'  => $clientNameExploded[0] ?? '',
                                                  //'templateId'  => 'd-84ef2d36b629496da42c1a8bcbf6ed53',
                                                  'templateId'  => 'd-613da0ac5d7e478ba436e4d51e2ee42c',
                                                  'bodyEmail'   => $bodyEmail,
                                                  'checkout'    => $abandonedCart,
                                              ];

                                              event(new SendEmailEvent($dataEmail));
                                          }
                                      }
                                  } catch (Exception $e) {
                                      Log::warning('Erro ao enviar e-mail no foreach - Carrinho abandonado, Dia seguinte');
                                      report($e);
                                  }
                              }
                          });
        } catch (Exception $e) {
            Log::warning('Erro ao enviar e-mail - Carrinho abandonado, Dia seguinte');

            report($e);
        }
    }
}
