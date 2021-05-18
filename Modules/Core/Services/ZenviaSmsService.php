<?php
/**
 * Created by PhpStorm.
 * User: gustavo
 * Date: 08/07/19
 * Time: 17:26
 */

namespace Modules\Core\Services;

use DateTimeZone;
use Exception;
use Zenvia\Model\Sms;
use Zenvia\Model\SmsFacade;
use Illuminate\Support\Facades\Log;

/**
 * Class ZenviaSmsService
 * @package Modules\Core\Services
 */
class ZenviaSmsService
{
    private $zenviaSms;
    private $smsFacade;

    public function __construct()
    {
        $this->smsFacade = new SmsFacade(getenv('ZENVIA_EMAIL'), getenv('ZENVIA_PASSWORD'));
        $this->zenviaSms = new Sms();
    }

    public function sendSms($number, $message)
    {

        try {
            $this->zenviaSms->setTo(preg_replace("/[^0-9]/", "", $number));
            $this->zenviaSms->setMsg($message);
            $smsId = uniqid();
            $this->zenviaSms->setId($smsId);
            $this->zenviaSms->setCallbackOption(Sms::CALLBACK_NONE);
            $date = new \DateTime();
            $date->setTimeZone(new DateTimeZone('America/Sao_Paulo'));
            $schedule = $date->format("Y-m-d\TH:i:s");
            $this->zenviaSms->setSchedule($schedule);

            try {
                $response = $this->smsFacade->send($this->zenviaSms);
            } catch (Exception $e) {
                Log::warning('Erro ao enviar sms ZenviaService - SendMessage');
                report($e);
            }
        } catch (\Exception $e) {
            Log::warning('erro ao enviar sms para carrinho abandonado');
            report($e);
        }
    }
}
