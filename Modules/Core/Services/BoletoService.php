<?php

namespace Modules\Core\Services;

use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\Checkout;
use Modules\Core\Entities\Domain;
use Modules\Core\Entities\Project;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\User;
use Modules\Core\Events\BoletoPaidEvent;

class BoletoService
{
    public function verifyBoletosExpired()
    {
        //        $date = now()->subDay('1')->toDateString();
        //
        //        $boletos = Sale::where([['payment_method', '=', '2'], ['status', '=', '2'], [DB::raw("(DATE_FORMAT(boleto_due_date,'%Y-%m-%d'))"), $date]])
        //                       ->with('clientModel', 'plansSales.plan.products')
        //                       ->get();
        //
        //        foreach ($boletos as $boleto) {
        //            $sendEmail   = new SendgridService();
        //            $clientName  = $boleto->clientModel->name;
        //            $clientEmail = $boleto->clientModel->email;
        //
        //            $emailValidated = FoxUtils::validateEmail($clientEmail);
        //
        //            if ($emailValidated) {
        //                $sendEmail->sendEmail('Verifiquei aqui está pendente o pagamento', 'noreply@cloudfox.net', 'cloudfox', '', '', '');
        //            }
        //        }
    }

    /**
     * Hoje vence o seu pedido
     */
    public function verifyBoletosExpiring()
    {
        try {
            /** @var Sale $saleModel */
            $saleModel = new Sale();
            /** @var SaleService $saleService */
            $saleService = new SaleService();
            /** @var Project $projectModel */
            $projectModel = new Project();
            /** @var Domain $domainModel */
            $domainModel    = new Domain();
            $boletoDueToday = $saleModel->where([['payment_method', '=', '2'], ['status', '=', '2'], [DB::raw("(DATE_FORMAT(boleto_due_date,'%Y-%m-%d'))"), now()->toDateString()]])
                                        ->with('client', 'plansSales.plan.products')
                                        ->get();
            /** @var Sale $boleto */
            foreach ($boletoDueToday as $boleto) {
                try {
                    /** @var SendgridService $sendEmail */
                    $sendEmail = new SendgridService();
                    /** @var Checkout $checkoutModel */
                    $checkoutModel = new Checkout();
                    /** @var Checkout $checkout */
                    $checkout    = $checkoutModel->where("id", $boleto->checkout_id)->first();
                    $clientName  = $boleto->client->name;
                    $clientEmail = $boleto->client->email;

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
                        $discount = substr_replace($discount, ',', strlen($discount) - 2, 0);
                    }
                    $clientNameExploded = explode(' ', $clientName);

                    $boleto->total_paid_value = preg_replace("/[^0-9]/", "", $boleto->iof) + preg_replace("/[^0-9]/", "", $boleto->total_paid_value);
                    $boleto->total_paid_value = substr_replace($boleto->total_paid_value, ',', strlen($boleto->total_paid_value) - 2, 0);

                    $products = $saleService->getEmailProducts($boleto->id);
                    $project  = $projectModel->find($boleto->project_id);
                    $domain   = $domainModel->where('project_id', $project->id)->first();

                    $subTotal                = substr_replace($subTotal, ',', strlen($subTotal) - 2, 0);
                    $boleto->shipment_value  = preg_replace("/[^0-9]/", "", $boleto->shipment_value);
                    $boleto->shipment_value  = substr_replace($boleto->shipment_value, ',', strlen($boleto->shipment_value) - 2, 0);
                    $boletoDigitableLine     = [];
                    $boletoDigitableLine[0]  = substr($boleto->boleto_digitable_line, 0, 24);
                    $boletoDigitableLine[1]  = substr($boleto->boleto_digitable_line, 24, strlen($boleto->boleto_digitable_line) - 1);
                    $boleto->boleto_due_date = Carbon::parse($boleto->boleto_due_date)->format('d/m/y');

                    $telephoneValidated = FoxUtils::prepareCellPhoneNumber($boleto->client->telephone);

                    $linkShortenerService = new LinkShortenerService();
                    $link                 = $linkShortenerService->shorten($boleto->boleto_link);
                    if (!empty($link) && !empty($telephoneValidated)) {
                        DisparoProService::sendMessage($telephoneValidated, 'Olá ' . $clientNameExploded[0] . ',  seu boleto vence hoje, não deixe de efetuar o pagamento e garantir seu pedido! ' . $link);
                        /* $zenviaSms = new ZenviaSmsService();
                         $zenviaSms->sendSms('Olá ' . $clientNameExploded[0] . ',  seu boleto vence hoje, não deixe de efetuar o pagamento e garantir seu pedido! ' . $link, $telephoneValidated);*/
                        $checkout->increment('sms_sent_amount');
                    }

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
                        "products"              => $products,
                    ];
                    $emailValidated = FoxUtils::validateEmail($clientEmail);
                    if ($emailValidated) {
                        $sendEmail->sendEmail('noreply@' . $domain['name'], $project['name'], $clientEmail, $clientNameExploded[0], 'd-957fe3c5ecc6402dbd74e707b3d37a9b', $data);

                        $checkout->increment('email_sent_amount');
                    }
                } catch (Exception $e) {
                    Log::warning('Erro ao enviar boleto para e-mail no foreach - Boleto vencendo');
                    report($e);
                }
            }
        } catch (Exception $e) {
            Log::warning('Erro ao enviar boletos para e-mails - Boleto vencendo');
            report($e);
        }
    }

    /**
     * Já separamos seu pedido
     */
    public function verifyBoletoWaitingPayment()
    {
        try {
            /** @var Sale $saleModel */
            $saleModel = new Sale();
            /** @var SaleService $saleService */
            $saleService = new SaleService();
            /** @var SendgridService $sendEmail */
            $sendEmail = new SendgridService();
            /** @var Checkout $checkoutModel */
            $checkoutModel = new Checkout();
            /** @var Project $projectModel */
            $projectModel = new Project();
            /** @var Domain $domainModel */
            $domainModel = new Domain();
            /** @var Carbon $startDate */
            $startDate = now()->startOfDay()->subDay();
            /** @var Carbon $endDate */
            $endDate              = now()->endOfDay()->subDay();
            $boletoWaitingPayment = $saleModel->with('client', 'plansSales.plan.products')
                                              ->whereBetween('start_date', [$startDate, $endDate])
                                              ->where(
                                                  [
                                                      ['payment_method', '=', '2'],
                                                      ['status', '=', '2'],
                                                  ]
                                              )->get();
            /** @var Sale $boleto */
            foreach ($boletoWaitingPayment as $boleto) {
                try {
                    $checkout    = $checkoutModel->where("id", $boleto->checkout_id)->first();
                    $clientName  = $boleto->client->name;
                    $clientEmail = $boleto->client->email;
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
                    $boleto->total_paid_value = substr_replace($boleto->total_paid_value, ',', strlen($boleto->total_paid_value) - 2, 0);
                    $products                 = $saleService->getEmailProducts($boleto->id);
                    $project                  = $projectModel->find($boleto->project_id);
                    $domain                   = $domainModel->where('project_id', $project->id)->first();
                    $subTotal                 = substr_replace($subTotal, ',', strlen($subTotal) - 2, 0);
                    $boleto->shipment_value   = preg_replace("/[^0-9]/", "", $boleto->shipment_value);
                    $boleto->shipment_value   = substr_replace($boleto->shipment_value, ',', strlen($boleto->shipment_value) - 2, 0);
                    $boletoDigitableLine      = [];
                    $boletoDigitableLine[0]   = substr($boleto->boleto_digitable_line, 0, 24);
                    $boletoDigitableLine[1]   = substr($boleto->boleto_digitable_line, 24, strlen($boleto->boleto_digitable_line) - 1);
                    $boleto->boleto_due_date  = Carbon::parse($boleto->boleto_due_date)->format('d/m/y');
                    $data                     = [
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
                        "products"              => $products,
                    ];
                    $emailValidated           = FoxUtils::validateEmail($clientEmail);

                    if ($emailValidated) {
                        $sendEmail->sendEmail('noreply@' . $domain['name'], $project['name'], $clientEmail, $clientNameExploded[0], 'd-59dab7e71d4045e294cb6a14577da236', $data);

                        $checkout->increment('email_sent_amount');
                    }
                } catch (Exception $e) {
                    Log::warning('Erro ao enviar boletos par e-mail no foreach - Já separamos seu pedido');
                    report($e);
                }
            }
        } catch (Exception $e) {
            Log::warning('Erro ao enviar boletos para e-mails - Já separamos seu pedido');
            report($e);
        }
    }

    /**
     * Vamos ter que liberar sua mercadoria
     */
    public function verifyBoleto2()
    {
        try {
            /** @var Sale $saleModel */
            $saleModel = new Sale();
            /** @var SaleService $saleService */
            $saleService = new SaleService();
            /** @var Project $projectModel */
            $projectModel = new Project();
            /** @var Domain $domainModel */
            $domainModel = new Domain();
            /** @var Checkout $checkoutModel */
            $checkoutModel = new Checkout();
            /** @var Carbon $startDate */
            $startDate = now()->startOfDay()->subDays(2);
            /** @var Carbon $endDate */
            $endDate = now()->endOfDay()->subDays(2);

            $boletos = $saleModel->with('client', 'plansSales.plan.products')
                                 ->whereBetween('start_date', [$startDate, $endDate])
                                 ->where(
                                     [
                                         ['payment_method', '=', '2'],
                                         ['status', '=', '2'],
                                     ]
                                 )->get();

            /** @var Sale $boleto */
            foreach ($boletos as $boleto) {
                try {
                    $checkout    = $checkoutModel->where("id", $boleto->checkout_id)->first();
                    $clientName  = $boleto->client->name;
                    $clientEmail = $boleto->client->email;
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
                    $boleto->total_paid_value = substr_replace($boleto->total_paid_value, ',', strlen($boleto->total_paid_value) - 2, 0);
                    $products                 = $saleService->getEmailProducts($boleto->id);
                    $project                  = $projectModel->find($boleto->project_id);
                    $domain                   = $domainModel->where('project_id', $project->id)->first();

                    $subTotal                = substr_replace($subTotal, ',', strlen($subTotal) - 2, 0);
                    $boleto->shipment_value  = preg_replace("/[^0-9]/", "", $boleto->shipment_value);
                    $boleto->shipment_value  = substr_replace($boleto->shipment_value, ',', strlen($boleto->shipment_value) - 2, 0);
                    $boletoDigitableLine     = [];
                    $boletoDigitableLine[0]  = substr($boleto->boleto_digitable_line, 0, 24);
                    $boletoDigitableLine[1]  = substr($boleto->boleto_digitable_line, 24, strlen($boleto->boleto_digitable_line) - 1);
                    $boleto->boleto_due_date = Carbon::parse($boleto->boleto_due_date)->format('d/m/y');
                    $data                    = [
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
                        "products"              => $products,
                    ];
                    $emailValidated          = FoxUtils::validateEmail($clientEmail);
                    if ($emailValidated) {
                        $sendEmail = new SendgridService();

                        $sendEmail->sendEmail('noreply@' . $domain['name'], $project['name'], $clientEmail, $clientNameExploded[0], 'd-690a6140f72643c1af280b079d5e84c5', $data);
                        $checkout->increment('email_sent_amount');
                    }
                } catch (Exception $e) {
                    Log::warning('Erro ao enviar boleto para e-mail no foreach - Vamos ter que liberar sua mercadoria');
                    report($e);
                }
            }
        } catch (Exception $e) {
            Log::warning('Erro ao enviar boletos para e-mails - Vamos ter que liberar sua mercadoria');
            report($e);
        }
    }

    /**
     *
     */
    public function verifyBoletoExpired3()
    {
        //        $date          = now()->subDay('3')->toDateString();
        //        $boletoExpired = Sale::where([['payment_method', '=', '2'], ['status', '=', '2'], [DB::raw("(DATE_FORMAT(boleto_due_date,'%Y-%m-%d'))"), $date]])
        //                             ->with('clientModel')->get();
        //        foreach ($boletoExpired as $boleto) {
        //            $sendEmail       = new SendgridService();
        //            $clientName      = $boleto->clientModel->name;
        //            $clientEmail     = $boleto->clientModel->email;
        //            $clientTelephone = $boleto->clientModel->telephone;
        //
        //            $emailValidated     = FoxUtils::validateEmail($clientEmail);
        //            $telephoneValidated = FoxUtils::prepareCellPhoneNumber($clientTelephone);
        //            if ($telephoneValidated != '') {
        //                $zenviaSms = new ZenviaSmsService();
        //                $zenviaSms->sendSms('Promoção relâmpago por 24h', $telephoneValidated);
        //            }
        //            if ($emailValidated) {
        //                $totalValue = $boleto->total_paid_value;
        //                $view       = view('core::emails.boleto', compact('totalValue', 'clientName'));
        //                $sendEmail->sendEmail('Promoção relâmpago por 24h', 'noreply@cloudfox.net', 'cloudfox', '', '', '');
        //            }
        //        }
    }

    /**
     *
     */
    public function verifyBoletoExpired4()
    {
        //        $date          = now()->subDay('4')->toDateString();
        //        $boletoExpired = Sale::where([['payment_method', '=', '2'], ['status', '=', '2'], [DB::raw("(DATE_FORMAT(boleto_due_date,'%Y-%m-%d'))"), $date]])
        //                             ->with('clientModel')->get();
        //        foreach ($boletoExpired as $boleto) {
        //            $sendEmail          = new SendgridService();
        //            $clientName         = $boleto->clientModel->name;
        //            $clientEmail        = $boleto->clientModel->email;
        //            $products           = [];
        //            $data               = [];
        //            $subTotal           = 0;
        //            $iof                = preg_replace("/[^0-9]/", "", $boleto->iof);
        //            $clientNameExploded = explode(' ', $clientName);
        //            if ($boleto->iof > '0') {
        //                $boleto->total_paid_value = preg_replace("/[^0-9]/", "", $boleto->iof) + preg_replace("/[^0-9]/", "", $boleto->total_paid_value);
        //                $boleto->total_paid_value = substr_replace($boleto->total_paid_value, ',', strlen($boleto->total_paid_value) - 2, 0);
        //            }
        //            foreach ($boleto->plansSales as $plansSale) {
        //                $plan     = Plan::find($plansSale['plan']);
        //                $project  = Project::find($plan['project']);
        //                $subTotal += str_replace('.', '', $plan['price']) * $plansSale["amount"];
        //                foreach ($plansSale->getRelation('plan')->products as $product) {
        //                    $productArray               = [];
        //                    $productArray["photo"]      = $product->photo;
        //                    $productArray["name"]       = $product->name;
        //                    $productArray["name"]       = $product->name;
        //                    $productArray["amount"]     = $plansSale->amount;
        //                    $productArray["plan_value"] = $plansSale->plan_value;
        //                    $products[]                 = $productArray;
        //                }
        //                $subTotal                = substr_replace($subTotal, ',', strlen($subTotal) - 2, 0);
        //                $iof                     = substr_replace($iof, ',', strlen($iof) - 2, 0);
        //                $boleto->shipment_value  = preg_replace("/[^0-9]/", "", $boleto->iof) + preg_replace("/[^0-9]/", "", $boleto->total_paid_value);
        //                $boleto->shipment_value  = substr_replace($boleto->shipment_value, ',', strlen($boleto->shipment_value) - 2, 0);
        //                $boletoDigitableLine     = [];
        //                $boletoDigitableLine[0]  = substr($boleto->boleto_digitable_line, 0, 24);
        //                $boletoDigitableLine[1]  = substr($boleto->boleto_digitable_line, 24, strlen($boleto->boleto_digitable_line) - 1);
        //                $boleto->boleto_due_date = Carbon::parse($boleto->boleto_due_date)->format('d/m/y');
        //                $data                    = [
        //                    "name"                  => $clientNameExploded[0],
        //                    "boleto_link"           => $boleto->boleto_link,
        //                    "boleto_digitable_line" => $boletoDigitableLine,
        //                    "boleto_due_date"       => $boleto->boleto_due_date,
        //                    "total_paid_value"      => $boleto->total_paid_value,
        //                    "shipment_value"        => $boleto->shipment_value,
        //                    "subtotal"              => strval($subTotal),
        //                    "iof"                   => $iof,
        //                    "project_logo"          => $project->logo,
        //                    "products"              => $products,
        //                ];
        //            }
        //            $emailValidated = FoxUtils::validateEmail($clientEmail);
        //            if ($emailValidated) {
        //                $sendEmail->sendEmail('Últimas horas para acabar', 'noreply@cloudfox.net', 'cloudfox', '', '', 'd-0a12383664cc44538fdee997bd3456d1', $data);
        //            }
        //        }
    }

    /**
     *  Boletos Compensados
     * @return void
     */
    public function verifyBoletoPaid()
    {
        try {
            /** @var User $userModel */
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
                            GROUP BY u.id
                            , u.email';
            $boletosPaid = DB::select($sql);
            foreach ($boletosPaid as $boleto) {
                try {
                    /** @var User $user */
                    $user = $userModel->newQuery()->find($boleto->owner_id);
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
        $saleModel = new Sale();
        $date      = Carbon::now()->subDay('4')->toDateString();

        $boletos = $saleModel->where([
                                         ['payment_method', '=', '2'],
                                         ['status', '=', '2'],
                                         [DB::raw("(DATE_FORMAT(boleto_due_date,'%Y-%m-%d'))"), '<=', $date],
                                     ])
                             ->get();
        /** @var Sale $boleto */
        foreach ($boletos as $boleto) {
            $boleto->update(['status' => 5]);
        }
    }
}

