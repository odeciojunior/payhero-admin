<?php

namespace Modules\Core\Services;

use Egulias\EmailValidator\Exception\NoDNSRecord;
use Egulias\EmailValidator\Warning\NoDNSMXRecord;
use Illuminate\Encryption\Encrypter;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use NumberFormatter;
use Vinkla\Hashids\Facades\Hashids;

class FoxUtils
{
    /**
     * @param $host
     * @return bool
     */
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
                Log::warning(print_r($error));
            }
        }

        return $MXresult || $Aresult;
    }

    /**
     * @param $email
     * @return bool
     */
    public static function validateEmail($email)
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL) && !empty($email)) {
            $emailExploded = explode('@', $email);
            $variant       = INTL_IDNA_VARIANT_2003;
            if (defined('INTL_IDNA_VARIANT_UTS46')) {
                $variant = INTL_IDNA_VARIANT_UTS46;
            }
            $host = rtrim(idn_to_ascii($emailExploded[1], IDNA_DEFAULT, $variant), '.');

            return checkdnsrr($host, 'MX');
        }

        return false;
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

    /**
     * @param $zipCode
     * @return string
     */
    public static function getCep($zipCode)
    {
        return substr($zipCode, 0, 5) . '-' . substr($zipCode, 5, 3);
    }

    /**
     * @param $cep
     * @return string|null
     */
    public static function formatCEP($cep)
    {
        if (self::isEmpty($cep) || strlen($cep) < 8) {
            return null;
        }

        return substr($cep, 0, 5) . "-" . substr($cep, 5);
    }

    /**
     * @param $hash
     * @return mixed
     */
    public static function decodeHash($hash)
    {
        return current(Hashids::decode($hash));
    }

    /**
     * @param $dateString
     * @return bool|mixed
     */
    public static function validateDateRange($dateString)
    {
        preg_match_all('/(0[1-9]|[1-2][0-9]|3[0-1])\/(0[1-9]|1[0-2])\/((19|20)[0-9]{2})/', $dateString, $matches);
        $dateRange = current($matches);
        if (count($dateRange) == 2) {
            $dateRange[0] = date('Y-m-d', strtotime(str_replace('/', '-', $dateRange[0])));
            $dateRange[1] = date('Y-m-d', strtotime(str_replace('/', '-', $dateRange[1])));

            return $dateRange;
        }

        return false;
    }

    public static function removeAccents($string)
    {
        return preg_replace(["/(á|à|ã|â|ä)/", "/(Á|À|Ã|Â|Ä)/", "/(é|è|ê|ë)/", "/(É|È|Ê|Ë)/", "/(í|ì|î|ï)/", "/(Í|Ì|Î|Ï)/", "/(ó|ò|õ|ô|ö)/", "/(Ó|Ò|Õ|Ô|Ö)/", "/(ú|ù|û|ü)/", "/(Ú|Ù|Û|Ü)/", "/(ñ)/", "/(Ñ)/"], explode(" ", "a A e E i I o O u U n N"), $string);
    }

    public static function removeSpecialChars($string)
    {

        return preg_replace('/([^a-zà-úA-ZÀ-Ú0-9 ]|[äåæËÎÏÐðÑ×÷ØÝÞßÆøÆø])/u', "", $string);
    }

    /**
     * @param mixed $var
     * @return bool
     */
    public static function isEmpty($var)
    {
        if (!isset($var)) {
            return true;
        } else if (empty($var)) {
            return true;
        } else if (is_string($var) && trim($var) == '') {
            return true;
        } else if (is_array($var) && count($var) == 0) {
            return true;
        } else if (is_object($var) && ($var instanceof Collection) && count($var) == 0) {
            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    public static function isProduction()
    {
        if (env("APP_ENV", "local") == "production") {
            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    public static function urlCheckout()
    {
        if (self::isProduction()) {
            $url = 'https://checkout.cloudfox.net';
        } else {
            $url = 'http://checkout.devcloudfox.net';
        }

        return $url;
    }

    /**
     * @param $value
     * @param string $type
     * @return null
     */
    public static function xorEncrypt($value, $type = "encrypt")
    {
        $customKey = getenv("CUSTOM_CRYPT_KEY", null);
        if (self::isEmpty($customKey)) {
            return null;
        }
        if ($type == "decrypt") {
            $value = base64_decode($value);
        }
        $valueLength     = strlen($value);
        $customKeyLength = strlen($customKey);
        for ($i = 0; $i < $valueLength; $i++) {
            for ($j = 0; $j < $customKeyLength; $j++) {
                if ($type == "decrypt") {
                    $value[$i] = $customKey[$j] ^ $value[$i];
                } else {
                    $value[$i] = $value[$i] ^ $customKey[$j];
                }
            }
        }

        $result = $value;

        if ($type == "encrypt") {
            $result = base64_encode($value);
        }

        return $result;
    }

    //mask($cnpj,'##.###.###/####-##');
    //mask($cpf,'###.###.###-##');
    //mask($cep,'#####-###');
    //mask($data,'##/##/####');
    public static function mask($val, $mask)
    {
        $maskared = '';
        $k        = 0;
        for ($i = 0; $i <= strlen($mask) - 1; $i++) {
            if ($mask[$i] == '#') {
                if (isset($val[$k]))
                    $maskared .= $val[$k++];
            } else {
                if (isset($mask[$i]))
                    $maskared .= $mask[$i];
            }
        }

        return $maskared;
    }

    /**
     * @param $value
     * @param string $locale
     * @param string $currency
     * @return string
     * https://www.php.net/manual/pt_BR/class.numberformatter.php
     */
    public static function formatMoney($value, $locale = 'pt_BR', $currency = 'BRL')
    {

        $formatter = new NumberFormatter($locale, NumberFormatter::CURRENCY);

        return $formatter->formatCurrency($value, $currency);
    }

    /**
     * @param $value
     * @return int|null
     */
    public static function onlyNumbers($value)
    {
        return preg_replace("/[^0-9]/", "", $value);
    }

}


