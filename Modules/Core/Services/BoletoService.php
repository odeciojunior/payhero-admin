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
    public function verifyBoletos()
    {
        $dateNow   = Carbon::now()->toDateString();
        $yesterday = Carbon::now()->subDay();

        $boletos = Sale::where([['payment_form', '=', 'boleto'], ['status', '=', '2'], ['start_date', '<', $dateNow], ['boleto_due_date', '>', $dateNow]], ['start_date', '!=', $dateNow], ['boleto_due_date', '!=', $dateNow])
                       ->with('clientModel')
                       ->get();

        foreach ($boletos as $boleto) {
            $sendEmail   = new SendgridService();
            $clientName  = $boleto->clientModel->name;
            $clientEmail = $boleto->clientModel->email;
            $totalValue  = $boleto[0]->total_paid_value;
            $view        = view('core::emails.boleto', compact('totalValue'));
            $sendEmail->sendEmail($view, 'Boletos vencidos', 'noreply@cloudfox.app', 'cloudfox', 'luanmaia65@hotmail.com', 'Luan');
        }
    }

    public function verifyBoletosExpiring()
    {
        $dateNow = Carbon::now()->toDateString();

        $boletoDueToday = Sale::where([['payment_form', '=', 'boleto'], ['status', '=', '2'], [DB::raw("(DATE_FORMAT(boleto_due_date,'%Y-%m-%d'))"), $dateNow]])
                              ->with('clientModel')
                              ->get();

        foreach ($boletoDueToday as $boleto) {
            $sendEmail   = new SendgridService();
            $clientName  = $boleto->clientModel->name;
            $clientEmail = $boleto->clientModel->email;
            $totalValue  = $boleto->total_paid_value;
            $view        = view('core::emails.boleto', compact('totalValue'));
            $sendEmail->sendEmail($view, 'Hoje vence o seu boleto', 'noreply@cloudfox.app', 'cloudfox', 'luanmaia65@hotmail.com', 'Luan');
        }
    }

    public function verifyBoletoWaitingPayment()
    {
        $date = Carbon::now()->addDay('1')->toDateString();
        dd($date);
        $boletoWaitionPayment = Sale::where([['payment_form', '=', 'boleto'], ['status', '=', '2']])->with('clientModel')->get();
    }
}
