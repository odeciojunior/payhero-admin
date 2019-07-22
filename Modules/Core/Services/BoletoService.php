<?php
/**
 * Created by PhpStorm.
 * User: gustavo
 * Date: 08/07/19
 * Time: 15:03
 */

namespace Modules\Core\Services;

use App\Entities\Checkout;
use App\Entities\Company;
use App\Entities\Domain;
use App\Entities\Plan;
use App\Entities\Project;
use App\Entities\Sale;
use App\Entities\Transaction;
use App\Entities\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Notifications\Notifications\boletoCompensatedNotification;
use Pusher\Pusher;
use Vinkla\Hashids\Facades\Hashids;

class BoletoService
{
    public function verifyBoletosExpired()
    {
        //        $date = Carbon::now()->subDay('1')->toDateString();
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

    //Hoje vence o seu boleto
    public function verifyBoletosExpiring()
    {
        try {
            $dateNow = Carbon::now()->toDateString();

            $boletoDueToday = Sale::where([['payment_method', '=', '2'], ['status', '=', '2'], [DB::raw("(DATE_FORMAT(boleto_due_date,'%Y-%m-%d'))"), $dateNow]])
                                  ->with('clientModel', 'plansSales.plan.products')
                                  ->get();
            foreach ($boletoDueToday as $boleto) {
                try {
                    $sendEmail   = new SendgridService();
                    $clientName  = $boleto->clientModel->name;
                    $clientEmail = $boleto->clientModel->email;
                    $products    = [];
                    $data        = [];

                    $subTotal           = 0;
                    $iof                = preg_replace("/[^0-9]/", "", $boleto->iof);
                    $clientNameExploded = explode(' ', $clientName);

                    if ($boleto->iof > '0') {
                        $boleto->total_paid_value = preg_replace("/[^0-9]/", "", $boleto->iof) + preg_replace("/[^0-9]/", "", $boleto->total_paid_value);
                        $boleto->total_paid_value = substr_replace($boleto->total_paid_value, ',', strlen($boleto->total_paid_value) - 2, 0);
                    }
                    foreach ($boleto->plansSales as $plansSale) {
                        $plan    = Plan::find($plansSale['plan']);
                        $project = Project::find($plan['project']);
                        $domain  = Domain::where('project_id', $project->id)->first();

                        $subTotal += str_replace('.', '', $plan['price']) * $plansSale["amount"];
                        foreach ($plansSale->getRelation('plan')->products as $product) {
                            $productArray               = [];
                            $productArray["photo"]      = $product->photo;
                            $productArray["name"]       = $product->name;
                            $productArray["name"]       = $product->name;
                            $productArray["amount"]     = $plansSale->amount;
                            $productArray["plan_value"] = $plansSale->plan_value;
                            $products[]                 = $productArray;
                        }
                        $subTotal                = substr_replace($subTotal, ',', strlen($subTotal) - 2, 0);
                        $iof                     = substr_replace($iof, ',', strlen($iof) - 2, 0);
                        $boleto->shipment_value  = substr_replace($boleto->shipment_value, ',', strlen($boleto->shipment_value) - 2, 0);
                        $boletoDigitableLine     = [];
                        $boletoDigitableLine[0]  = substr($boleto->boleto_digitable_line, 0, 24);
                        $boletoDigitableLine[1]  = substr($boleto->boleto_digitable_line, 24, strlen($boleto->boleto_digitable_line) - 1);
                        $boleto->boleto_due_date = Carbon::parse($boleto->boleto_due_date)->format('d/m/y');

                        $telephoneValidated = FoxUtils::prepareCellPhoneNumber($boleto->clientModel->telephone);

                        if ($telephoneValidated != '') {
                            $zenviaSms = new ZenviaSmsService();
                            Log::warning('verifyBoletosExpiring');

                            $zenviaSms->sendSms('Olá ' . $clientNameExploded[0] . ',  seu boleto vence hoje, não deixe de efetuar o pagamento e garantir seu pedido!' . $boleto->boleto_link, $telephoneValidated);
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
                            "project_logo"          => $project->logo,
                            "project_contact"       => $project->contact,
                            "products"              => $products,
                        ];
                        $emailValidated = FoxUtils::validateEmail($clientEmail);
                        if ($emailValidated) {
                            Log::warning('verifyBoletosExpiring');

                            $sendEmail->sendEmail('noreply@' . $domain['name'], $project['name'], $clientEmail, $clientNameExploded[0], 'd-957fe3c5ecc6402dbd74e707b3d37a9b', $data);
                        }
                    }
                } catch (Exception $e) {
                    Log::error('Erro ao enviar boleto para e-mail no foreach - Boleto vencendo');
                    report($e);
                }
            }
        } catch (Exception $e) {
            Log::error('Erro ao enviar boletos para e-mails - Boleto vencendo');
            report($e);
        }
    }

    //        Já separamos seu pedido
    public function verifyBoletoWaitingPayment()
    {
        try {
            $date                 = Carbon::now()->subDay('1')->toDateString();
            $boletoWaitionPayment = Sale::where([['payment_method', '=', '2'], ['status', '=', '2'], [DB::raw("(DATE_FORMAT(start_date,'%Y-%m-%d'))"), $date]])
                                        ->with('clientModel', 'plansSales.plan.products')->get();
            foreach ($boletoWaitionPayment as $boleto) {
                try {
                    $sendEmail          = new SendgridService();
                    $clientName         = $boleto->clientModel->name;
                    $clientEmail        = $boleto->clientModel->email;
                    $products           = [];
                    $data               = [];
                    $subTotal           = 0;
                    $iof                = preg_replace("/[^0-9]/", "", $boleto->iof);
                    $clientNameExploded = explode(' ', $clientName);
                    if ($boleto->iof > '0') {
                        $boleto->total_paid_value = preg_replace("/[^0-9]/", "", $boleto->iof) + preg_replace("/[^0-9]/", "", $boleto->total_paid_value);
                        $boleto->total_paid_value = substr_replace($boleto->total_paid_value, ',', strlen($boleto->total_paid_value) - 2, 0);
                    }
                    foreach ($boleto->plansSales as $plansSale) {
                        $plan    = Plan::find($plansSale['plan']);
                        $project = Project::find($plan['project']);
                        $domain  = Domain::where('project_id', $project->id)->first();

                        $subTotal += str_replace('.', '', $plan['price']) * $plansSale["amount"];
                        foreach ($plansSale->getRelation('plan')->products as $product) {
                            $productArray               = [];
                            $productArray["photo"]      = $product->photo;
                            $productArray["name"]       = $product->name;
                            $productArray["name"]       = $product->name;
                            $productArray["amount"]     = $plansSale->amount;
                            $productArray["plan_value"] = $plansSale->plan_value;
                            $products[]                 = $productArray;
                        }
                        $subTotal                = substr_replace($subTotal, ',', strlen($subTotal) - 2, 0);
                        $iof                     = substr_replace($iof, ',', strlen($iof) - 2, 0);
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
                            "project_logo"          => $project->logo,
                            "project_contact"       => $project->contact,
                            "products"              => $products,
                        ];
                        $emailValidated          = FoxUtils::validateEmail($clientEmail);

                        if ($emailValidated) {
                            Log::warning('verifyBoletoWaitingPayment');

                            $sendEmail->sendEmail('noreply@' . $domain['name'], $project['name'], $clientEmail, $clientNameExploded[0], 'd-59dab7e71d4045e294cb6a14577da236', $data);
                        }
                    }
                } catch (Exception $e) {
                    Log::error('Erro ao enviar boletos par e-mail no foreach - Já separamos seu pedido');
                    report($e);
                }
            }
        } catch (Exception $e) {
            Log::error('Erro ao enviar boletos para e-mails - Já separamos seu pedido');
            report($e);
        }
    }

    //Vamos ter que liberar sua mercadoria
    public function verifyBoleto2()
    {
        try {
            $date    = Carbon::now()->subDay('2')->toDateString();
            $boletos = Sale::where([['payment_method', '=', '2'], ['status', '=', '2'], [DB::raw("(DATE_FORMAT(start_date,'%Y-%m-%d'))"), $date]])
                           ->with('clientModel', 'plansSales.plan.products')->get();
            foreach ($boletos as $boleto) {
                try {
                    $clientName         = $boleto->clientModel->name;
                    $clientEmail        = $boleto->clientModel->email;
                    $products           = [];
                    $data               = [];
                    $subTotal           = 0;
                    $iof                = preg_replace("/[^0-9]/", "", $boleto->iof);
                    $clientNameExploded = explode(' ', $clientName);
                    if ($boleto->iof > '0') {
                        $boleto->total_paid_value = preg_replace("/[^0-9]/", "", $boleto->iof) + preg_replace("/[^0-9]/", "", $boleto->total_paid_value);
                        $boleto->total_paid_value = substr_replace($boleto->total_paid_value, ',', strlen($boleto->total_paid_value) - 2, 0);
                    }
                    foreach ($boleto->plansSales as $plansSale) {
                        $plan    = Plan::find($plansSale['plan']);
                        $project = Project::find($plan['project']);
                        $domain  = Domain::where('project_id', $project->id)->first();

                        $subTotal += str_replace('.', '', $plan['price']) * $plansSale["amount"];
                        foreach ($plansSale->getRelation('plan')->products as $product) {
                            $productArray               = [];
                            $productArray["photo"]      = $product->photo;
                            $productArray["name"]       = $product->name;
                            $productArray["name"]       = $product->name;
                            $productArray["amount"]     = $plansSale->amount;
                            $productArray["plan_value"] = $plansSale->plan_value;
                            $products[]                 = $productArray;
                        }
                        $subTotal                = substr_replace($subTotal, ',', strlen($subTotal) - 2, 0);
                        $iof                     = substr_replace($iof, ',', strlen($iof) - 2, 0);
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
                            "project_logo"          => $project->logo,
                            "project_contact"       => $project->contact,
                            "products"              => $products,
                        ];
                        $emailValidated          = FoxUtils::validateEmail($clientEmail);
                        if ($emailValidated) {
                            $sendEmail = new SendgridService();
                            Log::warning('verifyBoleto2');

                            $sendEmail->sendEmail('noreply@' . $domain['name'], $project['name'], $clientEmail, $clientNameExploded[0], 'd-690a6140f72643c1af280b079d5e84c5', $data);
                        }
                    }
                } catch (Exception $e) {
                    Log::error('Erro ao enviar boleto para e-mail no foreach - Vamos ter que liberar sua mercadoria');
                    report($e);
                }
            }
        } catch (Exception $e) {
            Log::error('Erro ao enviar boletos para e-mails - Vamos ter que liberar sua mercadoria');
            report($e);
        }
    }

    public function verifyBoletoExpired3()
    {
        //        $date          = Carbon::now()->subDay('3')->toDateString();
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

    public function verifyBoletoExpired4()
    {
        //        $date          = Carbon::now()->subDay('4')->toDateString();
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

    //Boletos compensados
    public function verifyBoletoPaid()
    {
        try {
            $date        = Carbon::now()->toDateString();
            $sendEmail   = new SendgridService();
            $data        = [];
            $boletosPaid = Sale::select(\DB::raw('count(*) as count'), 'owner', 'id')
                               ->where([['sales.payment_method', '=', '2'], ['sales.status', '=', '1'], [DB::raw("(DATE_FORMAT(sales.end_date,'%Y-%m-%d'))"), $date]])
                               ->groupBy('sales.owner', 'id')->get();
            foreach ($boletosPaid as $boleto) {
                try {
                    $user = User::find($boleto->owner);

                    $emailValidated = FoxUtils::validateEmail($user->email);
                    $message        = '';

                    if ($boleto->count == 1) {
                        $message = 'boleto foi compensado';
                    } else {
                        $message = 'boletos foram compensados';
                    }

                    $data = [
                        "name"              => $user->name,
                        'boleto_count'      => strval($boleto->count),
                        'message'           => $message,
                        'transaction_value' => "R$ 00,00",
                    ];

                    if ($emailValidated && $boleto->count > 0) {
                        Log::warning('verifyBoletoPaid');
                        $user->notify(new boletoCompensatedNotification($boleto->count));

                        $sendEmail->sendEmail('noreply@cloudfox.net', 'cloudfox', $user->email, $user->name, 'd-4ce62be1218d4b258c8d1ab139d4d664', $data);
                    }
                } catch (Exception $e) {
                    Log::error('Erro ao enviar boleto para e-mail no foreach - Boletos compensados');
                    report($e);
                }
            }
        } catch (Exception $e) {
            Log::error('Erro ao enviar boletos para e-mails - Boletos compensados');
            report($e);
        }
    }
}
