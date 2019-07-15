<?php
/**
 * Created by PhpStorm.
 * User: gustavo
 * Date: 08/07/19
 * Time: 15:03
 */

namespace Modules\Core\Services;

use App\Entities\Sale;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BoletoService
{
    public function verifyBoletosExpired()
    {
        $date = Carbon::now()->subDay('1')->toDateString();

        $boletos = Sale::where([['payment_method', '=', '2'], ['status', '=', '2'], [DB::raw("(DATE_FORMAT(boleto_due_date,'%Y-%m-%d'))"), $date]])
                       ->with('clientModel')
                       ->get();

        foreach ($boletos as $boleto) {
            $sendEmail   = new SendgridService();
            $clientName  = $boleto->clientModel->name;
            $clientEmail = $boleto->clientModel->email;

            $emailValidated = FoxUtils::validateEmail($clientEmail);

            if ($emailValidated) {
                $totalValue = $boleto->total_paid_value;
                $view       = view('core::emails.boleto', compact('totalValue', 'clientName'));
                $sendEmail->sendEmail($view, 'Verifiquei aqui está pendente o pagamento', 'noreply@cloudfox.app', 'cloudfox', '', '');
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
            foreach ($boleto->plansSales as $plansSale) {
                //                dd($plansSale->getRelation('plan')->products[0]);
                foreach ($plansSale->getRelation('plan')->products as $product) {
                    $productArray               = [];
                    $productArray["photo"]      = $product->photo;
                    $productArray["name"]       = $product->name;
                    $productArray["name"]       = $product->name;
                    $productArray["amount"]     = $plansSale->amount;
                    $productArray["plan_value"] = $plansSale->plan_value;
                    $products[]                 = $productArray;
                }
            }
            $data = [
                "name"                  => $clientName,
                "boleto_link"           => $boleto['boleto_link'],
                "boleto_digitable_line" => $boleto['boleto_digitable_line'],
                "boleto_due_date"       => $boleto['boleto_due_date'],
                "total_paid_value"      => $boleto['total_paid_value'],
            ];
            dd($products);
            $emailValidated = FoxUtils::validateEmail($clientEmail);
            if ($emailValidated) {
                //                $view       = view('core::emails.boleto', compact('totalValue', 'clientName'));
                $sendEmail->sendEmail('Hoje vence o seu boleto', 'noreply@cloudfox.app', 'cloudfox', 'luanmaia65@hotmail.com', 'Luan', 'd-957fe3c5ecc6402dbd74e707b3d37a9b', $data, $products);
            }
        }
    }

    public function verifyBoletoWaitingPayment()
    {
        $date                 = Carbon::now()->subDay('1')->toDateString();
        $boletoWaitionPayment = Sale::where([['payment_method', '=', '2'], ['status', '=', '2'], [DB::raw("(DATE_FORMAT(boleto_due_date,'%Y-%m-%d'))"), $date]])
                                    ->with('clientModel')->get();
        foreach ($boletoWaitionPayment as $boleto) {
            $sendEmail      = new SendgridService();
            $clientName     = $boleto->clientModel->name;
            $clientEmail    = $boleto->clientModel->email;
            $emailValidated = FoxUtils::validateEmail($clientEmail);
            if ($emailValidated) {
                $totalValue = $boleto->total_paid_value;
                $view       = view('core::emails.boleto', compact('totalValue', 'clientName'));
                $sendEmail->sendEmail($view, 'Já separamos seu pedido', 'noreply@cloudfox.app', 'cloudfox', '', '');
            }
        }
    }

    public function verifyBoletoExpired2()
    {
        $date          = Carbon::now()->subDay('2')->toDateString();
        $boletoExpired = Sale::where([['payment_method', '=', '2'], ['status', '=', '2'], [DB::raw("(DATE_FORMAT(boleto_due_date,'%Y-%m-%d'))"), $date]])
                             ->with('clientModel')->get();
        foreach ($boletoExpired as $boleto) {
            $sendEmail      = new SendgridService();
            $clientName     = $boleto->clientModel->name;
            $clientEmail    = $boleto->clientModel->email;
            $emailValidated = FoxUtils::validateEmail($clientEmail);
            if ($emailValidated) {
                $totalValue = $boleto->total_paid_value;
                $view       = view('core::emails.boleto', compact('totalValue', 'clientName'));
                $sendEmail->sendEmail($view, 'Vamos ter que liberar sua mercadoria', 'noreply@cloudfox.app', 'cloudfox', '', '');
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
                $sendEmail->sendEmail($view, 'Promoção relâmpago por 24h', 'noreply@cloudfox.app', 'cloudfox', '', '');
            }
        }
    }

    public function verifyBoletoExpired4()
    {
        $date          = Carbon::now()->subDay('4')->toDateString();
        $boletoExpired = Sale::where([['payment_method', '=', '2'], ['status', '=', '2'], [DB::raw("(DATE_FORMAT(boleto_due_date,'%Y-%m-%d'))"), $date]])
                             ->with('clientModel')->get();
        foreach ($boletoExpired as $boleto) {
            $sendEmail      = new SendgridService();
            $clientName     = $boleto->clientModel->name;
            $clientEmail    = $boleto->clientModel->email;
            $emailValidated = FoxUtils::validateEmail($clientEmail);
            if ($emailValidated) {
                $totalValue = $boleto->total_paid_value;
                $view       = view('core::emails.boleto', compact('totalValue', 'clientName'));
                $sendEmail->sendEmail($view, 'Últimas horas para acabar', 'noreply@cloudfox.app', 'cloudfox', '', '');
            }
        }
    }
}
