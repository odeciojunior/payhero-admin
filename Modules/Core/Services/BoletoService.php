<?php
/**
 * Created by PhpStorm.
 * User: gustavo
 * Date: 08/07/19
 * Time: 15:03
 */

namespace Modules\Core\Services;

use App\Entities\Plan;
use App\Entities\Project;
use App\Entities\Sale;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BoletoService
{
    public function verifyBoletosExpired()
    {
        $date = Carbon::now()->subDay('1')->toDateString();

        $boletos = Sale::where([['payment_method', '=', '2'], ['status', '=', '2'], [DB::raw("(DATE_FORMAT(boleto_due_date,'%Y-%m-%d'))"), $date]])
                       ->with('clientModel', 'plansSales.plan.products')
                       ->get();

        foreach ($boletos as $boleto) {
            $sendEmail   = new SendgridService();
            $clientName  = $boleto->clientModel->name;
            $clientEmail = $boleto->clientModel->email;

            $emailValidated = FoxUtils::validateEmail($clientEmail);

            if ($emailValidated) {
                $sendEmail->sendEmail('Verifiquei aqui está pendente o pagamento', 'noreply@cloudfox.app', 'cloudfox', 'luanmaia65@.com', '', '');
            }
        }
    }

    public function verifyBoletosExpiring()
    {
        $dateNow = Carbon::now()->toDateString();

        $boletoDueToday = Sale::where([['payment_method', '=', '2'], ['status', '=', '2'], [DB::raw("(DATE_FORMAT(boleto_due_date,'%Y-%m-%d'))"), $dateNow]])
                              ->with('clientModel', 'plansSales.plan.products')
                              ->get();
        foreach ($boletoDueToday as $boleto) {

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
                $plan     = Plan::find($plansSale['plan']);
                $project  = Project::find($plan['project']);
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
                $boleto->shipment_value  = preg_replace("/[^0-9]/", "", $boleto->iof) + preg_replace("/[^0-9]/", "", $boleto->total_paid_value);
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
                    "products"              => $products,
                ];
            }
            $emailValidated = FoxUtils::validateEmail($clientEmail);
            if ($emailValidated) {
                //                $view       = view('core::emails.boleto', compact('totalValue', 'clientName'));
                $sendEmail->sendEmail('Hoje vence o seu boleto', 'noreply@cloudfox.app', 'cloudfox', 'luanmaia65@hotmail.com', 'Luan', 'd-957fe3c5ecc6402dbd74e707b3d37a9b', $data);
            }
        }
    }

    public function verifyBoletoWaitingPayment()
    {
        $date                 = Carbon::now()->subDay('1')->toDateString();
        $boletoWaitionPayment = Sale::where([['payment_method', '=', '2'], ['status', '=', '2'], [DB::raw("(DATE_FORMAT(start_date,'%Y-%m-%d'))"), $date]])
                                    ->with('clientModel', 'plansSales.plan.products')->get();
        foreach ($boletoWaitionPayment as $boleto) {
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
                $plan     = Plan::find($plansSale['plan']);
                $project  = Project::find($plan['project']);
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
                $boleto->shipment_value  = preg_replace("/[^0-9]/", "", $boleto->iof) + preg_replace("/[^0-9]/", "", $boleto->total_paid_value);
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
                    "products"              => $products,
                ];
            }
            $emailValidated = FoxUtils::validateEmail($clientEmail);
            if ($emailValidated) {
                $sendEmail->sendEmail('Já separamos seu pedido', 'noreply@cloudfox.app', 'cloudfox', 'luanmaia65@hotmail.com', 'Luan', 'd-59dab7e71d4045e294cb6a14577da236', $data);
            }
        }
    }

    public function verifyBoleto2()
    {
        $date    = Carbon::now()->subDay('2')->toDateString();
        $boletos = Sale::where([['payment_method', '=', '2'], ['status', '=', '2'], [DB::raw("(DATE_FORMAT(start_date,'%Y-%m-%d'))"), $date]])
                       ->with('clientModel','plansSales.plan.products')->get();
        //        dd($boletos);
        foreach ($boletos as $boleto) {
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
                $plan     = Plan::find($plansSale['plan']);
                $project  = Project::find($plan['project']);
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
                $boleto->shipment_value  = preg_replace("/[^0-9]/", "", $boleto->iof) + preg_replace("/[^0-9]/", "", $boleto->total_paid_value);
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
                    "products"              => $products,
                ];
            }
            $emailValidated = FoxUtils::validateEmail($clientEmail);
            if ($emailValidated) {
                $sendEmail->sendEmail('Vamos ter que liberar sua mercadoria', 'noreply@cloudfox.app', 'cloudfox', 'luanmaia65@hotmail.com', 'Luan', 'd-690a6140f72643c1af280b079d5e84c5', $data);
            }
        }
    }

    public function verifyBoletoExpired3()
    {
        $date          = Carbon::now()->subDay('3')->toDateString();
        $boletoExpired = Sale::where([['payment_method', '=', '2'], ['status', '=', '2'], [DB::raw("(DATE_FORMAT(boleto_due_date,'%Y-%m-%d'))"), $date]])
                             ->with('clientModel')->get();
        foreach ($boletoExpired as $boleto) {
            $sendEmail       = new SendgridService();
            $clientName      = $boleto->clientModel->name;
            $clientEmail     = $boleto->clientModel->email;
            $clientTelephone = $boleto->clientModel->telephone;

            $emailValidated     = FoxUtils::validateEmail($clientEmail);
            $telephoneValidated = FoxUtils::prepareCellPhoneNumber($clientTelephone);
            if ($telephoneValidated != '') {
                $zenviaSms = new ZenviaSmsService();
                $zenviaSms->sendSms('Promoção relâmpago por 24h', $telephoneValidated);
            }
            if ($emailValidated) {
                $totalValue = $boleto->total_paid_value;
                $view       = view('core::emails.boleto', compact('totalValue', 'clientName'));
                $sendEmail->sendEmail('Promoção relâmpago por 24h', 'noreply@cloudfox.app', 'cloudfox', '', '', '');
            }
        }
    }

    public function verifyBoletoExpired4()
    {
        $date          = Carbon::now()->subDay('4')->toDateString();
        $boletoExpired = Sale::where([['payment_method', '=', '2'], ['status', '=', '2'], [DB::raw("(DATE_FORMAT(boleto_due_date,'%Y-%m-%d'))"), $date]])
                             ->with('clientModel')->get();
        foreach ($boletoExpired as $boleto) {
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
                $plan     = Plan::find($plansSale['plan']);
                $project  = Project::find($plan['project']);
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
                $boleto->shipment_value  = preg_replace("/[^0-9]/", "", $boleto->iof) + preg_replace("/[^0-9]/", "", $boleto->total_paid_value);
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
                    "products"              => $products,
                ];
            }
            $emailValidated = FoxUtils::validateEmail($clientEmail);
            if ($emailValidated) {
                $sendEmail->sendEmail('Últimas horas para acabar', 'noreply@cloudfox.app', 'cloudfox', '', '', 'd-0a12383664cc44538fdee997bd3456d1', $data);
            }
        }
    }
}
