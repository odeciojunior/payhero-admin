<?php
/**
 * Created by PhpStorm.
 * User: gustavo
 * Date: 08/07/19
 * Time: 17:26
 */

namespace Modules\Core\Services;

use Exception;
use Zenvia\Model\Sms;
use Zenvia\Model\SmsFacade;
use Illuminate\Support\Facades\Log;

class ZenviaSmsService
{
    private $zenviaSms;
    private $smsFacade;

    public function __construct()
    {
        $this->smsFacade = new SmsFacade('healthlab.corp', 'hLQNVb7VQk');
        $this->zenviaSms = new Sms();
    }

    public function sendSms($msg, $to)
    {

        try {
            $this->zenviaSms->setTo($to);
            $this->zenviaSms->setMsg($msg);
            $smsId = uniqid();
            $this->zenviaSms->setId($smsId);
            $this->zenviaSms->setCallbackOption(Sms::CALLBACK_NONE);
            try {
                $response = $this->smsFacade->send($this->zenviaSms);
                if ($response) {
                    return true;
                }

                return false;
            } catch (Exception $ex) {
                return false;
            }
        } catch (\Exception $e) {
            Log::warning('erro ao enviar sms para carrinho abandonado');
            report($e);
        }
    }
}
