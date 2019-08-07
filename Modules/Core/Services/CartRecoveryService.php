<?php

namespace Modules\Core\Services;

use Exception;
use Carbon\Carbon;
use App\Entities\Plan;
use App\Entities\Domain;
use App\Entities\Project;
use App\Entities\Checkout;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Entities\Log as CheckoutLog;

class CartRecoveryService
{
    public function verifyAbandonedCarts()
    {
        try {
            $dateStart = new \DateTime();
            $dateEnd   = new \DateTime();

            $dateEnd->modify('-4 hours');
            $dateStart->modify('-255 minutes');
            $formatted_dateStart = $dateStart->format('y-m-d H:i:s');
            $formatted_dateEnd   = $dateEnd->format('y-m-d H:i:s');
            $data                = [];

            $abandonedCarts = Checkout::where([['status', '=', 'abandoned cart'], ['created_at', '>', $formatted_dateStart], ['created_at', '<', $formatted_dateEnd]])
                                      ->with('projectModel', 'checkoutPlans.plan.productsPlans.getProduct')
                                      ->get();

            foreach ($abandonedCarts as $abandonedCart) {
                $products            = [];

                try {
                    foreach ($abandonedCart->checkoutPlans as $checkoutPlan) {
                        foreach ($checkoutPlan->getRelation('plan')->productsPlans as $productPlan) {
                            $productArray           = [];
                            $productArray["name"]   = $productPlan->getProduct->name;
                            $productArray["photo"]  = $productPlan->getProduct->photo;
                            $productArray["amount"] = $productPlan->amount;
                            $products[]             = $productArray;
                        }
                    }

                    $log                = CheckoutLog::where('id_log_session', $abandonedCart->id_log_session)
                                                    ->orderBy('created_at', 'desc')
                                                    ->first();

                    $telephoneValidated = FoxUtils::prepareCellPhoneNumber($log['telephone']);
                    $project            = Project::find($abandonedCart['project']);
                    $domain             = Domain::where('project_id', $project->id)->first();

                    $link               = "https://checkout." . $domain['name'] . "/recovery/" . $log->id_log_session;
                    $clientNameExploded = explode(' ', $log['name']);

                    if ($telephoneValidated != '') {
                        $zenviaSms            = new ZenviaSmsService();
                        $linkShortenerService = new LinkShortenerService();
                        $link                 = $linkShortenerService->shorten($link);
                        if (!empty($link)) {

                            $zenviaSms->sendSms('Olá ' . $clientNameExploded[0] . ', somos da loja ' . $project['name'] . ', vimos que você não finalizou seu pedido, aproveite o último dia da promoção: ' . $link, $telephoneValidated);
                            $abandonedCart->increment('sms_sent_amount');
                        }
                    }
                    $data           = [
                        'name'            => $clientNameExploded[0],
                        'project_logo'    => $project['logo'],
                        'checkout_link'   => $link,
                        "project_contact" => $project['contact'],
                        "products"        => $products,
                    ];
                    $emailValidated = FoxUtils::validateEmail($log['email']);

                    if ($emailValidated) {
                        $sendEmail = new SendgridService();

                        $sendEmail->sendEmail('noreply@' . $domain['name'], $project['name'], $log['email'], $log['name'], 'd-538d3405815c43debcf48aa44ceab965', $data);
                        $abandonedCart->increment('email_sent_amount');
                    }
                } catch (Exception $e) {
                    Log::warning('Erro ao enviar e-mail no foreach - Carrinho abandonado');

                    report($e);
                }
            }
        } catch (Exception $e) {
            Log::warning('Erro ao enviar e-mail - Carrinho abandonado ');

            report($e);
        }
    }

    //Carrinho abandonado dia seguinte
    public function verifyAbandonedCarts2()
    {
        try {
            $date     = Carbon::now()->subDay('1')->toDateString();
            $data     = [];

            $abandonedCarts = Checkout::where([['status', '=', 'abandoned cart'], [DB::raw("(DATE_FORMAT(created_at,'%Y-%m-%d'))"), $date]])
                                      ->with('projectModel', 'checkoutPlans.plan.productsPlans.getProduct')
                                      ->get();

            foreach($abandonedCarts as $abandonedCart){
                $log = CheckoutLog::where('id_log_session', $abandonedCart->id_log_session)
                    ->orderBy('created_at', 'desc')
                    ->first();
            }

            foreach ($abandonedCarts as $abandonedCart) {
                try {

                    $products = [];

                    foreach ($abandonedCart->checkoutPlans as $checkoutPlan) {
                        foreach ($checkoutPlan->getRelation('plan')->productsPlans as $productPlan) {
                            $productArray           = []; 
                            $productArray["name"]   = $productPlan->getProduct->name;
                            $productArray["photo"]  = $productPlan->getProduct->photo;
                            $productArray["amount"] = $productPlan->amount;
                            $products[]             = $productArray;
                        }
                    }

                    $log = CheckoutLog::where('id_log_session', $abandonedCart->id_log_session)
                                     ->orderBy('created_at', 'desc')
                                     ->first();

                    $telephoneValidated = FoxUtils::prepareCellPhoneNumber($log['telephone']);
                    $project            = Project::find($abandonedCart['project']);
                    $domain             = Domain::where('project_id', $project->id)->first();

                    $link               = "https://checkout." . $domain['name'] . "/recovery/" . $log->id_log_session;
                    $clientNameExploded = explode(' ', $log['name']);

                    if ($telephoneValidated != '') {
                        $zenviaSms            = new ZenviaSmsService();
                        $linkShortenerService = new LinkShortenerService();
                        $link                 = $linkShortenerService->shorten($link);
                        if (!empty($link)) {
                            $zenviaSms->sendSms('Olá ' . $clientNameExploded[0] . ', somos da loja ' . $project['name'] . ', vimos que você não finalizou seu pedido, aproveite o último dia da promoção: ' . $link, $telephoneValidated);
                            $abandonedCart->increment('sms_sent_amount');
                        }
                    }

                    $data           = [
                        'name'            => $clientNameExploded[0],
                        'project_logo'    => $project['logo'],
                        'checkout_link'   => $link,
                        "project_contact" => $project['contact'],
                        "products"        => $products,

                    ];
                    $emailValidated = FoxUtils::validateEmail($log['email']);
                    if ($emailValidated) {
                        $sendEmail = new SendgridService();

                        $sendEmail->sendEmail('noreply@' . $domain['name'], $project['name'], $log['email'], $log['name'], 'd-84ef2d36b629496da42c1a8bcbf6ed53', $data);
                        $abandonedCart->increment('email_sent_amount');
                    }

                } catch (Exception $e) {
                    Log::warning('Erro ao enviar e-mail no foreach - Carrinho abandonado, Dia seguinte');
                    report($e);
                }
            }
        } catch (Exception $e) {
            Log::warning('Erro ao enviar e-mail - Carrinho abandonado, Dia seguinte');

            report($e);
        }
    }
}
