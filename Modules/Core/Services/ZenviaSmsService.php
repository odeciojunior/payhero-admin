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
use Exception;
use Illuminate\Support\Facades\Log;
use Zenvia\Model\Sms;
use Zenvia\Model\SmsFacade;

class ZenviaSmsService
{
    private $zenviaSms;
    private $smsFacade;

    public function __construct()
    {
        $this->smsFacade = new SmsFacade('healthlab.corp', 'hLQNVb7VQk');
        $this->zenviaSms = new Sms();
    }

    public function sendSms($msg, $to, $link = null)
    {

        try {
            if ($link != null) {
                $linkShortenerService = new LinkShortenerService();
                $link                 = $linkShortenerService->shorten($link);

                if (!$link) {
                    Log::warning('Link URL invalido (ZenviaSmsService - sendSMS) - ' . $link);

                    return false;
                }
            }

            $this->zenviaSms->setTo($to);
            $this->zenviaSms->setMsg($msg . ' ' . $link);
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
