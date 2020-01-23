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
            $linkShortenerService       = new LinkShortenerService();

            $dateStart = new \DateTime();
            $dateEnd   = new \DateTime();

            $dateEnd->modify('-1 hours');
            $dateStart->modify('-75 minutes');
            $formatted_dateStart = $dateStart->format('y-m-d H:i:s');
            $formatted_dateEnd   = $dateEnd->format('y-m-d H:i:s');
            $data                = [];

            $checkoutModel->where([['status', '=', 'abandoned cart'], ['created_at', '>', $formatted_dateStart], ['created_at', '<', $formatted_dateEnd]])
                          ->with('project', 'checkoutPlans.plan.productsPlans.product')
                          ->chunk(100, function($abandonedCarts) use ($checkoutLogModel, $projectModel, $domainModel, $linkShortenerService, $projectNotificationService, $projectNotificationModel) {
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

                                          $telephoneValidated = FoxUtils::prepareCellPhoneNumber($log['telephone']);
                                          $project            = $projectModel->find($abandonedCart['project_id']);
                                          $domain             = $domainModel->where('project_id', $project->id)
                                                                            ->first();

                                          $linkCheckout       = "https://checkout." . $domain['name'] . "/recovery/" . $log->id_log_session;
                                          $clientNameExploded = explode(' ', $log['name']);

                                          $link = $linkShortenerService->shorten($linkCheckout);
                                          if (!empty($link) && !empty($telephoneValidated)) {
                                              $dataSms = [
                                                  'message'   => 'Olá ' . $clientNameExploded[0] . ', somos da loja ' . $project['name'] . ', vimos que você não finalizou seu pedido, aproveite o último dia da promoção: ' . $link,
                                                  'telephone' => $telephoneValidated,
                                                  'checkout'  => $abandonedCart,
                                              ];
                                              event(new SendSmsEvent($dataSms));
                                          }

                                          if (!empty($domain) && !empty($clientNameExploded[0])) {
                                              $bodyEmail = [
                                                  'name'            => $clientNameExploded[0],
                                                  'project_logo'    => $project['logo'],
                                                  'checkout_link'   => $link,
                                                  "project_contact" => $project['contact'],
                                                  "products"        => $products,
                                              ];

                                              $dataEmail = [
                                                  'domainName'  => $domain['name'],
                                                  'projectName' => $project['name'] ?? '',
                                                  'clientEmail' => $log['email'],
                                                  'clientName'  => $log['name'] ?? '',
                                                  'templateId'  => 'd-538d3405815c43debcf48aa44ceab965',
                                                  'bodyEmail'   => $bodyEmail,
                                                  'checkout'    => $abandonedCart,

                                              ];

                                              event(new SendEmailEvent($dataEmail));
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
            $linkShortenerService       = new LinkShortenerService();

            $date = Carbon::now()->subDay('1')->toDateString();
            $data = [];
            $checkoutModel->where([['status', '=', 'abandoned cart'], [DB::raw("(DATE_FORMAT(created_at,'%Y-%m-%d'))"), $date]])
                          ->with('project', 'checkoutPlans.plan.productsPlans.product')
                          ->chunk(100, function($abandonedCarts) use ($checkoutLogModel, $projectModel, $domainModel, $linkShortenerService, $projectNotificationService, $projectNotificationModel) {
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

                                      $telephoneValidated = FoxUtils::prepareCellPhoneNumber($log['telephone']);
                                      $project            = $projectModel->find($abandonedCart['project_id']);
                                      $domain             = $domainModel->where('project_id', $project->id)
                                                                        ->where('status', 3)
                                                                        ->first();

                                      $linkCheckout       = "https://checkout." . $domain['name'] . "/recovery/" . $log->id_log_session;
                                      $clientNameExploded = explode(' ', $log['name']);

                                      $link = $linkShortenerService->shorten($linkCheckout);
                                      if (!empty($link) && !empty($telephoneValidated)) {
                                          $dataSms = [
                                              'message'   => 'Olá ' . $clientNameExploded[0] . ', somos da loja ' . $project['name'] . ', vimos que você não finalizou seu pedido, aproveite o último dia da promoção: ' . $link,
                                              'telephone' => $telephoneValidated,
                                              'checkout'  => $abandonedCart,
                                          ];
                                          event(new SendSmsEvent($dataSms));
                                      }

                                      if (!empty($domain) && !empty($clientNameExploded[0])) {
                                          $bodyEmail = [
                                              'name'            => $clientNameExploded[0],
                                              'project_logo'    => $project['logo'],
                                              'checkout_link'   => $link,
                                              "project_contact" => $project['contact'],
                                              "products"        => $products,
                                          ];

                                          $dataEmail = [
                                              'domainName'  => $domain['name'],
                                              'projectName' => $project['name'] ?? '',
                                              'clientEmail' => $log['email'],
                                              'clientName'  => $clientNameExploded[0] ?? '',
                                              'templateId'  => 'd-84ef2d36b629496da42c1a8bcbf6ed53',
                                              'bodyEmail'   => $bodyEmail,
                                              'checkout'    => $abandonedCart,
                                          ];

                                          event(new SendEmailEvent($dataEmail));
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
