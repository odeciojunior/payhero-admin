<?php
/**
 * Created by PhpStorm.
 * User: gustavo
 * Date: 08/07/19
 * Time: 15:03
 */

namespace Modules\Core\Services;

use App\Entities\Checkout;
use App\Entities\Domain;
use App\Entities\Log;
use App\Entities\Plan;
use App\Entities\Project;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CartRecoveryService
{
    public function verifyAbandonedCarts()
    {
        $dateStart = new \DateTime();
        $dateEnd   = new \DateTime();

        $dateEnd->modify('-4 hours');
        $dateStart->modify('-255 minutes');
        $formatted_dateStart = $dateStart->format('y-m-d H:i:s');
        $formatted_dateEnd   = $dateEnd->format('y-m-d H:i:s');
        $data                = [];
        $products            = [];

        $abandonedCarts = Checkout::where([['status', '=', 'abandoned cart'], ['created_at', '>', $formatted_dateStart], ['created_at', '<', $formatted_dateEnd]])
                                  ->with('projectModel', 'checkoutPlans.plan.products')
                                  ->get();

        foreach ($abandonedCarts as $abandonedCart) {
            foreach ($abandonedCart->checkoutPlans as $checkoutPlan) {
                foreach ($checkoutPlan->getRelation('plan')->products as $product) {
                    $productArray = [
                        $productArray["name"] = $product->name,
                        $productArray["photo"] = $product->photo,
                        $productArray["amount"] = $checkoutPlan->amount,
                        $products[] = $productArray,
                    ];
                }
            }

            $log                = Log::where('id_log_session', $abandonedCart->id_log_session)
                                     ->orderBy('created_at', 'desc')
                                     ->first();
            $telephoneValidated = FoxUtils::prepareCellPhoneNumber($log['telephone']);
            $project            = Project::find($abandonedCart['project']);
            $domain             = Domain::where('project_id', $project->id)->first();

            $link               = "https://checkout." . $domain['name'] . "/recovery/" . $log->id_log_session;
            $clientNameExploded = explode(' ', $log['name']);

            if ($telephoneValidated != '') {
                $zenviaSms = new ZenviaSmsService();
                \Illuminate\Support\Facades\Log::warning('verifyAbandonedCarts');

                $zenviaSms->sendSms('Olá ' . $clientNameExploded[0] . ', somos da loja ' . $project['name'] . ', vimos que você não finalizou seu pedido, aproveite o último dia da promoção: ' . $link, $telephoneValidated);
            }
            $data           = [
                'name'          => $clientNameExploded[0],
                'project_photo' => $project['logo'],
                'checkout_link' => $link,
                'contact_email' => 'sac@' . $domain['name'],
                "products"      => $products,

            ];
            $emailValidated = FoxUtils::validateEmail($log['email']);

            if ($emailValidated) {
                $sendEmail = new SendgridService();
                \Illuminate\Support\Facades\Log::warning('verifyAbandonedCarts');

                $sendEmail->sendEmail('noreply@' . $domain['name'], $project['name'], $log['email'], $log['name'], 'd-538d3405815c43debcf48aa44ceab965', $data);
            }
        }
    }

    //Carrinho abandonado dia seguinte
    public function verifyAbandonedCarts2()
    {
        $date     = Carbon::now()->subDay('1')->toDateString();
        $data     = [];
        $products = [];

        $abandonedCarts = Checkout::where([['status', '=', 'abandoned cart'], [DB::raw("(DATE_FORMAT(created_at,'%Y-%m-%d'))"), $date]])
                                  ->with('projectModel', 'checkoutPlans.plan.products')
                                  ->get();
        foreach ($abandonedCarts as $abandonedCart) {
            foreach ($abandonedCart->checkoutPlans as $checkoutPlan) {
                foreach ($checkoutPlan->getRelation('plan')->products as $product) {
                    $productArray = [
                        $productArray["name"] = $product->name,
                        $productArray["photo"] = $product->photo,
                        $productArray["amount"] = $checkoutPlan->amount,
                        $products[] = $productArray,
                    ];
                }
            }

            $log = Log::where('id_log_session', $abandonedCart->id_log_session)
                      ->orderBy('created_at', 'desc')
                      ->first();

            $telephoneValidated = FoxUtils::prepareCellPhoneNumber($log['telephone']);
            $project            = Project::find($abandonedCart['project']);
            $domain             = Domain::where('project_id', $project->id)->first();

            $link               = "https://checkout." . $domain['name'] . "/recovery/" . $log->id_log_session;
            $clientNameExploded = explode(' ', $log['name']);

            if ($telephoneValidated != '') {
                $zenviaSms = new ZenviaSmsService();
                \Illuminate\Support\Facades\Log::warning('verifyAbandonedCarts2');

                $zenviaSms->sendSms('Olá ' . $clientNameExploded[0] . ', somos da loja ' . $project['name'] . ', vimos que você não finalizou seu pedido, aproveite o último dia da promoção: ' . $link, $telephoneValidated);
            }

            $data           = [
                'name'          => $clientNameExploded[0],
                'project_photo' => $project['logo'],
                'checkout_link' => $link,
                'contact_email' => 'sac@' . $domain['name'],
                "products"      => $products,

            ];
            $emailValidated = FoxUtils::validateEmail($log['email']);
            if ($emailValidated) {
                $sendEmail = new SendgridService();
                \Illuminate\Support\Facades\Log::warning('verifyAbandonedCarts2');

                $sendEmail->sendEmail('noreply@' . $domain['name'], $project['name'], $log['email'], $log['name'], 'd-84ef2d36b629496da42c1a8bcbf6ed53', $data);
            }
        }
    }
}
