<?php

namespace Modules\Core\Sms;

use DateTimeZone;
use Zenvia\Model\Sms;
use App\Entities\Client;
use App\Entities\Domain;
use Zenvia\Model\SmsFacade;
use App\Entities\SmsMessage;

class SmsService
{

    public static function sendSms(ZenviaSms $smsService, Sale $sale)
    {

        $client = Client::find($sale->customer);
        $planSale = PlanSale::where('sale', $sale->id)->first();
        $plan = Plan::find($planSale->plan);

        $names = explode(" ", $client['name']);
        $domain = Domain::where('projeto', $plan['projeto'])->first();
        $checkoutUrl = "https://checkout." . $domain->name . "/" . $plan['code'];

        $message = str_replace("{primeiro_nome}", $names[0], $smsService->message);
        $message = str_replace("{nome_completo}", $client['nome'], $message);
        $message = str_replace("{email}", $client['email'], $message);
        $message = str_replace("{url_checkout}", $checkoutUrl, $message);
        $message = str_replace("{url_boleto}", $sale['link_boleto'], $message);
        $message = str_replace("{data_vencimento}", $sale['boleto_due_date'], $message);
        $message = str_replace("{linha_digitavel}", $sale['boleto_digitable_line'], $message);

        $smsFacade = new SmsFacade('healthlab.corp', 'hLQNVb7VQk');
        $sms = new Sms();
        $sms->setTo('55' . preg_replace("/[^0-9]/", "", $client['telephone']));
        $sms->setMsg($message);
        $idSms = uniqid();
        $sms->setId($idSms);
        $sms->setCallbackOption(Sms::CALLBACK_NONE);
        $date = new \DateTime();
        $date->setTimeZone(new DateTimeZone('America/Sao_Paulo'));
        if ($smsService->time != '0') {
            $date->modify("+{$smsService->time} {$smsService->period}");
        }
        $schedule = $date->format("Y-m-d\TH:i:s");
        $sms->setSchedule($schedule);

        try {
            $response = $smsFacade->send($sms);

            SmsMessage::create([
                'id_zenvia' => $idSms,
                'to' => '55' . preg_replace("/[^0-9]/", "", $client['telephone']),
                'message' => $message,
                'date' => $schedule,
                'status' => $response->getStatusDescription(),
                'plan' => $plan['id'],
                'event' => $smsService->event,
                'type' => 'Sent'
            ]);

            return true;
        } catch (\Exception $ex) {

            MensagemSms::create([
                'id_zenvia' => $idSms,
                'to' => '55' . preg_replace("/[^0-9]/", "", $client['telephone']),
                'message' => $message,
                'date' => $schedule,
                'status' => 'Erro',
                'plan' => $planSale->plano,
                'event' => $smsService->event,
                'type' => 'Sent'
            ]);

            return false;
        }

    }

}


