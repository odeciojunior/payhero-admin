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

    /**
     * @return string|string[]|null
     */
    public static function getDocument($document)
    {
        $document = preg_replace("/\D/", '', $document);

        if (strlen($document) === 11) {
            return preg_replace("/(\d{3})(\d{3})(\d{3})(\d{2})/", "\$1.\$2.\$3-\$4", $document);
        }

        return preg_replace("/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/", "\$1.\$2.\$3/\$4-\$5", $document);
    }

    /**
     * @param $telephone
     * @param bool $ddd
     * @param bool $number
     * @return string
     */
    public static function getTelephone($telephone, $ddd = false, $number = false)
    {
        $telephone = preg_replace("/\D/", '', $telephone);

        if (!$ddd && !$number) {

            $length = strlen(preg_replace("/[^0-9]/", "", $telephone));
            if ($length == 13) { // COM CÓDIGO DE ÁREA NACIONAL E DO PAIS e 9 dígitos
                return "+" . substr($telephone, 0, $length - 11) . "(" . substr($telephone, $length - 11, 2) . ")" . substr($telephone, $length - 9, 5) . "-" . substr($telephone, -4);
            }
            if ($length == 12) { // COM CÓDIGO DE ÁREA NACIONAL E DO PAIS
                return "+" . substr($telephone, 0, $length - 10) . "(" . substr($telephone, $length - 10, 2) . ")" . substr($telephone, $length - 8, 4) . "-" . substr($telephone, -4);
            }
            if ($length == 11) { // COM CÓDIGO DE ÁREA NACIONAL e 9 dígitos
                return "(" . substr($telephone, 0, 2) . ")" . substr($telephone, 2, 5) . "-" . substr($telephone, 7, 11);
            }
            if ($length == 10) { // COM CÓDIGO DE ÁREA NACIONAL
                return "(" . substr($telephone, 0, 2) . ")" . substr($telephone, 2, 4) . "-" . substr($telephone, 6, 10);
            }
            if ($length <= 9) { // SEM CÓDIGO DE ÁREA
                return substr($telephone, 0, $length - 4) . "-" . substr($telephone, -4);
            }
        } else if ($ddd) {
            return substr($telephone, 0, 2);
        } else {
            $length = strlen(preg_replace("/[^0-9]/", "", $telephone));

            if ($length == 11) {
                return substr($telephone, 2, 5) . "-" . substr($telephone, 7, 11);
            }
            if ($length == 10) {
                return substr($telephone, 2, 4) . "-" . substr($telephone, 6, 10);
            }

            return '';
        }
        return '';
    }

    public static function getCep($zipCode)
    {
        return substr($zipCode, 0, 5) . '-' . substr($zipCode, 5, 3);
    }
}
