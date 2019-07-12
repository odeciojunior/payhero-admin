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
            $totalValue  = $boleto[0]->total_paid_value;
            $view        = view('core::emails.boleto', compact('totalValue','clientName'));
            $sendEmail->sendEmail($view, 'Verifiquei aqui está pendente o pagamento', 'noreply@cloudfox.app', 'cloudfox', '', '');
        }
    }

    public function verifyBoletosExpiring()
    {
        $dateNow = Carbon::now()->toDateString();

        $boletoDueToday = Sale::where([['payment_method', '=', '2'], ['status', '=', '2'], [DB::raw("(DATE_FORMAT(boleto_due_date,'%Y-%m-%d'))"), $dateNow]])
                              ->with('clientModel')
                              ->get();

        foreach ($boletoDueToday as $boleto) {
            $sendEmail   = new SendgridService();
            $clientName  = $boleto->clientModel->name;
            $clientEmail = $boleto->clientModel->email;
            $totalValue  = $boleto->total_paid_value;
            $view        = view('core::emails.boleto', compact('totalValue','clientName'));
            $sendEmail->sendEmail($view, 'Hoje vence o seu boleto', 'noreply@cloudfox.app', 'cloudfox', '', '');
        }
    }

    public function verifyBoletoWaitingPayment()
    {
        $date                 = Carbon::now()->subDay('1')->toDateString();
        $boletoWaitionPayment = Sale::where([['payment_method', '=', '2'], ['status', '=', '2'], [DB::raw("(DATE_FORMAT(boleto_due_date,'%Y-%m-%d'))"), $date]])
                                    ->with('clientModel')->get();
        foreach ($boletoWaitionPayment as $boleto) {
            $sendEmail   = new SendgridService();
            $clientName  = $boleto->clientModel->name;
            $clientEmail = $boleto->clientModel->email;
            $totalValue  = $boleto->total_paid_value;
            $view        = view('core::emails.boleto', compact('totalValue','clientName'));
            $sendEmail->sendEmail($view, 'Já separamos seu pedido', 'noreply@cloudfox.app', 'cloudfox', '', '');
        }
    }

    public function verifyBoletoExpired2()
    {
        $date          = Carbon::now()->subDay('2')->toDateString();
        $boletoExpired = Sale::where([['payment_method', '=', '2'], ['status', '=', '2'], [DB::raw("(DATE_FORMAT(boleto_due_date,'%Y-%m-%d'))"), $date]])
                             ->with('clientModel')->get();
        foreach ($boletoExpired as $boleto) {
            $sendEmail   = new SendgridService();
            $clientName  = $boleto->clientModel->name;
            $clientEmail = $boleto->clientModel->email;
            $totalValue  = $boleto->total_paid_value;
            $view        = view('core::emails.boleto', compact('totalValue','clientName'));
            $sendEmail->sendEmail($view, 'Vamos ter que liberar sua mercadoria', 'noreply@cloudfox.app', 'cloudfox', '', '');
        }
    }

    public function verifyBoletoExpired3()
    {
        $date          = Carbon::now()->subDay('3')->toDateString();
        $boletoExpired = Sale::where([['payment_method', '=', '2'], ['status', '=', '2'], [DB::raw("(DATE_FORMAT(boleto_due_date,'%Y-%m-%d'))"), $date]])
                             ->with('clientModel')->get();
        foreach ($boletoExpired as $boleto) {
            $sendEmail   = new SendgridService();
            $clientName  = $boleto->clientModel->name;
            $clientEmail = $boleto->clientModel->email;
            $totalValue  = $boleto->total_paid_value;
            $view        = view('core::emails.boleto', compact('totalValue','clientName'));
            $sendEmail->sendEmail($view, 'Promoção relâmpago por 24h', 'noreply@cloudfox.app', 'cloudfox', '', '');
        }
    }

    public function verifyBoletoExpired4()
    {
        $date          = Carbon::now()->subDay('4')->toDateString();
        $boletoExpired = Sale::where([['payment_method', '=', '2'], ['status', '=', '2'], [DB::raw("(DATE_FORMAT(boleto_due_date,'%Y-%m-%d'))"), $date]])
                             ->with('clientModel')->get();
        foreach ($boletoExpired as $boleto) {
            $sendEmail   = new SendgridService();
            $clientName  = $boleto->clientModel->name;
            $clientEmail = $boleto->clientModel->email;
            $totalValue  = $boleto->total_paid_value;
            $view        = view('core::emails.boleto', compact('totalValue','clientName'));
            $sendEmail->sendEmail($view, 'Últimas horas para acabar', 'noreply@cloudfox.app', 'cloudfox', '', '');
        }
    }
}
