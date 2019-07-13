<?php 

namespace Modules\Core\Sms;

use Carbon\Carbon;
use App\Entities\Plan;
use App\Entities\Sale;
use App\Entities\PlanSale;
use App\Entities\ZenviaSms;
use App\Entities\SmsMessage;
use Modules\Core\Sms\ServicoSmsHelper;

class SmsScheduling {

    public static function verifyBoletosExpiring(){

        $boletos = Sale::whereDate('boleto_due_date', '=', Carbon::today()->toDateString())
                        ->where('status','2')
                        ->get();

        foreach($boletos as $boleto){

            $planSale = PlanSale::where('sale',$boleto->id)->first();

            $smsService = ZenviaSms::where([
                ['plan', $planSale['plan']],
                ['event', 'boleto_expiring'],
                ['status', '1']
            ])->first();

            if(!$smsService){
                $plan = Plan::find($planSale['plan']);
                $smsService = ZenviaSms::where([
                    ['project', $plan['project']],
                    ['event', 'boleto_expiring'],
                    ['status', '1']
                ])
                ->whereNull('plan')
                ->first();
            }

            ServicoSmsHelper::enviarSms($smsService,$boleto);
        }

    }

    public static function verifyBoletosExpired(){

        $boletos = Sale::whereDate('boleto_due_date', '<', Carbon::today()->toDateString())
                        ->where('gateway_status','waiting_payment')
                        ->get()->toArray();

        foreach($boletos as $boleto){

            $planSale = PlanSale::where('sale',$boleto['id'])->first();
            $client = Client::find($boleto['client']);

            $sentSms = SmsMessage::where([
                ['plan', $planSale['plan']],
                ['event', 'boleto_expired'],
                ['to',"55".preg_replace("/[^0-9]/", '', $client['telephone'])]
            ])->first();

            if($sentSms){
                continue;
            }

            $smsService = ZenviaSms::where([
                ['plan', $planSale['plan']],
                ['event', 'boleto_expired'],
                ['status', '1']
            ])->first();

            if($smsService){
                if(SmsService::sendSms($smsService,Sale::find($boleto['id']))){
                    $user->update([
                        'sms_zenvia_qtd' => $user['sms_zenvia_qtd'] - 1
                    ]);
                }
            }

        }

    }

}
