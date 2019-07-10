<?php
/**
 * Created by PhpStorm.
 * User: gustavo
 * Date: 08/07/19
 * Time: 17:26
 */

namespace Modules\Core\Services;

use App\Entities\Sale;
use App\Entities\ZenviaSms;

class ZenviaSmsService
{
    public function sendSms(ZenviaSms $smsService, Sale $sale)
    {
        try {
            $smsFacade = new SmsFacade(getenv('ZENVIA_CREDENTIAL'), getenv('ZENVIA_TOKEN'));
            $sms       = new Sms();
            //            $sms->setTo('55' . preg_replace("/[^0-9]/", "", $client['telephone']));
            //            $sms->setMsg($message);
            $idSms = uniqid();
            $sms->setId($idSms);
            $sms->setCallbackOption(Sms::CALLBACK_NONE);

            try {
                $response = $smsFacade->send($sms);

                //                SmsMessage::create([
                //                                       'id_zenvia' => $idSms,
                //                                       'to'        => '55' . preg_replace("/[^0-9]/", "", $client['telephone']),
                //                                       'message'   => $message,
                //                                       'date'      => $schedule,
                //                                       'status'    => $response->getStatusDescription(),
                //                                       'plan'      => $plan['id'],
                //                                       'event'     => $smsService->event,
                //                                       'type'      => 'Sent',
                //                                   ]);

                return true;
            } catch (\Exception $ex) {

                //                MensagemSms::create([
                //                                        'id_zenvia' => $idSms,
                //                                        'to'        => '55' . preg_replace("/[^0-9]/", "", $client['telephone']),
                //                                        'message'   => $message,
                //                                        'date'      => $schedule,
                //                                        'status'    => 'Erro',
                //                                        'plan'      => $planSale->plano,
                //                                        'event'     => $smsService->event,
                //                                        'type'      => 'Sent',
                //                                    ]);

                return false;
            }
        } catch (Exception $e) {
            report($e);
        }
    }
}