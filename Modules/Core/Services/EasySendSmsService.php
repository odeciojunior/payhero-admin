<?php
/*

namespace Modules\Core\Services;


use Patchwork\PHP\Shim\Iconv;

class EasySendSmsService
{
    public $strUserName;
    public $strPassword;
    public $strSender;
    public $strMessage;
    public $strMobile;

    private function smsUnicode($message)
    {
        $hex1 = '';
        if (function_exists('Iconv')) {
            $latin = @Iconv('UTF-8', 'ISO-8859-1', $message);
            if (strcmp($latin, $message)) {
                $arr = Unpack('H*Hex', @Iconv('UTF-8', 'UCS-2BE', $message));
                $hex1 = strtoupper($arr['Hex']);
            }

            if($hex1 == ''){
                $hex2 = '';
                $hex = ''
            }
        }
    }


}*/
