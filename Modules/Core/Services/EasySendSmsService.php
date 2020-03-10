<?php

namespace Modules\Core\Services;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

/**
 * Class EasySendSmsService
 * @package Modules\Core\Services
 */
class EasySendSmsService
{
    /**
     * @var string
     * url do site
     */
    private $host;
    /**
     * @var array|false|string
     * Username that is to be used for submission
     */
    private $username;
    /**
     * @var array|false|string
     * Password that is to be used
     */
    private $password;
    /**
     * @var
     * Message content that is to be transmitted
     */
    private $sender;
    /**
     * @var
     * Message content that is to be transmitted
     */
    private $message;
    /**
     * Type fo the message that is to bet sent
     * 0 -> means plain text
     * 1 -> means unicode (message content should be in hex)
     * 2 -> means plain flash text
     * 3 -> means unicode flash (message content should be in hex)
     */
    private $messageType;
    /**
     * @var
     * Mobile no is to be transmitted
     */
    public $numberMobile;

    /**
     * EasySendSmsService constructor.
     * @param string $message
     * @param string $numberMobile
     * @param string $sender
     * @param string $msgType
     */
    public function __construct(string $numberMobile, string $message, string $sender = '', string $msgType = '1')
    {
        $this->host         = 'https://www.easysendsms.com/sms/bulksms-api/bulksms-api';
        $this->username     = getenv('USERNAME_EASY_SMS');
        $this->password     = getenv('PASSWORD_EASY_SMS');
        $this->sender       = $sender;
        $this->message      = $message; //URL Encode The Message..
        $this->numberMobile = $numberMobile;
        $this->messageType  = $msgType;
    }

    /**
     * @param $message
     * @return bool|string
     * Transforma message em hexadecimal
     */
    private function smsUnicode($message)
    {
        $hex1 = '';
        if (function_exists('Iconv')) {
            $latin = @\iconv('UTF-8', 'ISO-8859-1', $message);
            if (strcmp($latin, $message)) {
                $arr  = unpack('H*Hex', @iconv('UTF-8', 'UCS-2BE', $message));
                $hex1 = strtoupper($arr['Hex']);
            }

            if ($hex1 == '') {
                $hex2 = '';
                $hex  = '';
                for ($i = 0; $i < strlen($message); $i++) {
                    $hex = dechex(ord($message[$i]));
                    $len = strlen($hex);
                    $add = 4 - $len;
                    if ($len < 4) {
                        for ($j = 0; $j < $add; $j++) {
                            $hex = "0" . $hex;
                        }
                    }
                    $hex2 .= $hex;
                }

                return $hex2;
            } else {
                return $hex1;
            }
        } else {
            return false;
        }
    }

    /**
     * Send SMS for users
     */
    public function submit()
    {

        if ($this->messageType == '1' || $this->messageType == '3') {
            //call the function of string to HEX
            $this->message = $this->smsUnicode($this->message);
        } else {
            $this->message = urlencode($this->message);
        }

        try {
            $url = $this->host . "?username=" . $this->username . "&password=" . $this->password . "&from=" . $this->sender . "&to=" . $this->numberMobile . "&text=" . $this->message . "&type=" . $this->messageType;
            $url = file($url);
        } catch (Exception $e) {
            report($e);
        }
    }
}
