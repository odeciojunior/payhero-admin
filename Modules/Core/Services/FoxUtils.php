<?php

namespace Modules\Core\Services;

use Egulias\EmailValidator\Exception\NoDNSRecord;
use Egulias\EmailValidator\Warning\NoDNSMXRecord;

class FoxUtils
{
    public static function checkDNS($host)
    {

        $variant = INTL_IDNA_VARIANT_2003;
        if (defined('INTL_IDNA_VARIANT_UTS46')) {
            $variant = INTL_IDNA_VARIANT_UTS46;
        }
        $host = rtrim(idn_to_ascii($host, IDNA_DEFAULT, $variant), '.') . '.';

        $Aresult  = true;
        $MXresult = checkdnsrr($host, 'MX');

        if (!$MXresult) {
            $warnings[NoDNSMXRecord::CODE] = new NoDNSMXRecord();
            $Aresult                       = checkdnsrr($host, 'A') || checkdnsrr($host, 'AAAA');
            if (!$Aresult) {
                $error = new NoDNSRecord();
            }
        }

        return $MXresult || $Aresult;
    }

    public static function validateEmail($email)
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL) && !empty($email)) {
            $emailExploded = explode('@', $email);
            $variant       = INTL_IDNA_VARIANT_2003;
            if (defined('INTL_IDNA_VARIANT_UTS46')) {
                $variant = INTL_IDNA_VARIANT_UTS46;
            }
            $host = rtrim(idn_to_ascii($emailExploded[1], IDNA_DEFAULT, $variant), '.') . '.';

            $checkdnsrr = checkdnsrr($host, 'MX');
            if ($checkdnsrr) {
                return true;
            }

            return false;
        }
    }

    public static function prepareCellPhoneNumber($phoneNumber)
    {
        $number = preg_replace("/[^0-9]/", "", $phoneNumber);
        if (strlen($number) == 11) {
            $number = substr_replace($number, '55', 0, 0);
            return $number;
        } else if (strlen($number) == 10) {
            $subNumber = substr($number, 2, 1);
            if ($subNumber != 2 && $subNumber != 3 && $subNumber != 4 && $subNumber != 5) {
                $number = substr_replace($number, '55', 0, 0);
                $number = substr_replace($number, '9', 4, 0);

                return $number;
            }
        }

        return '';
    }
}
