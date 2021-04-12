<?php

namespace Modules\Core\Services;

use Aws\S3\S3Client;
use Egulias\EmailValidator\Exception\NoDNSRecord;
use Egulias\EmailValidator\Warning\NoDNSMXRecord;
use Exception;
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
        $host = rtrim(idn_to_ascii($host, IDNA_DEFAULT, $variant), '.').'.';

        $Aresult = true;
        $MXresult = checkdnsrr($host, 'MX');

        if (!$MXresult) {
            $warnings[NoDNSMXRecord::CODE] = new NoDNSMXRecord();
            $Aresult = checkdnsrr($host, 'A') || checkdnsrr($host, 'AAAA');
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
            $variant = INTL_IDNA_VARIANT_2003;
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
        } else {
            if (strlen($number) == 10) {
                $subNumber = substr($number, 2, 1);
                if ($subNumber != 2 && $subNumber != 3 && $subNumber != 4 && $subNumber != 5) {
                    $number = substr_replace($number, '55', 0, 0);
                    $number = substr_replace($number, '9', 4, 0);

                    return $number;
                }
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
     * @param  bool  $ddd
     * @param  bool  $number
     * @return string
     */
    public static function getTelephone($telephone, $ddd = false, $number = false)
    {
        $telephone = preg_replace("/\D/", '', $telephone);

        if (!$ddd && !$number) {
            $length = strlen(preg_replace("/[^0-9]/", "", $telephone));
            if ($length == 13) { // COM CÓDIGO DE ÁREA NACIONAL E DO PAIS e 9 dígitos
                return "+".substr($telephone, 0, $length - 11)."(".substr(
                        $telephone,
                        $length - 11,
                        2
                    ).")".substr($telephone, $length - 9, 5)."-".substr($telephone, -4);
            }
            if ($length == 12) { // COM CÓDIGO DE ÁREA NACIONAL E DO PAIS
                return "+".substr($telephone, 0, $length - 10)."(".substr(
                        $telephone,
                        $length - 10,
                        2
                    ).")".substr($telephone, $length - 8, 4)."-".substr($telephone, -4);
            }
            if ($length == 11) { // COM CÓDIGO DE ÁREA NACIONAL e 9 dígitos
                return "(".substr($telephone, 0, 2).")".substr($telephone, 2, 5)."-".substr(
                        $telephone,
                        7,
                        11
                    );
            }
            if ($length == 10) { // COM CÓDIGO DE ÁREA NACIONAL
                return "(".substr($telephone, 0, 2).")".substr($telephone, 2, 4)."-".substr(
                        $telephone,
                        6,
                        10
                    );
            }
            if ($length <= 9) { // SEM CÓDIGO DE ÁREA
                return substr($telephone, 0, $length - 4)."-".substr($telephone, -4);
            }
        } else {
            if ($ddd) {
                return substr($telephone, 0, 2);
            } else {
                $length = strlen(preg_replace("/[^0-9]/", "", $telephone));

                if ($length == 11) {
                    return substr($telephone, 2, 5)."-".substr($telephone, 7, 11);
                }
                if ($length == 10) {
                    return substr($telephone, 2, 4)."-".substr($telephone, 6, 10);
                }

                return '';
            }
        }

        return '';
    }

    /**
     * @param $zipCode
     * @return string
     */
    public static function getCep($zipCode)
    {
        return substr($zipCode, 0, 5).'-'.substr($zipCode, 5, 3);
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

        return substr($cep, 0, 5)."-".substr($cep, 5);
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
        return preg_replace(
            [
                "/(á|à|ã|â|ä)/",
                "/(Á|À|Ã|Â|Ä)/",
                "/(é|è|ê|ë)/",
                "/(É|È|Ê|Ë)/",
                "/(í|ì|î|ï)/",
                "/(Í|Ì|Î|Ï)/",
                "/(ó|ò|õ|ô|ö)/",
                "/(Ó|Ò|Õ|Ô|Ö)/",
                "/(ú|ù|û|ü)/",
                "/(Ú|Ù|Û|Ü)/",
                "/(ñ)/",
                "/(Ñ)/",
                "/(Ç)/",
                "/(ç)/",

            ],
            explode(" ", "a A e E i I o O u U n N C c"),
            $string
        );
    }

    public static function removeSpecialChars($string)
    {
        return preg_replace('/([^a-zà-úA-ZÀ-Ú0-9 ]|[äåæËÎÏÐðÑ×÷ØÝÞßÆøÆø])/u', "", $string);
    }

    /**
     * @param  mixed  $var
     * @return bool
     */
    public static function isEmpty($var)
    {
        if (!isset($var)) {
            return true;
        } else {
            if (empty($var)) {
                return true;
            } else {
                if (is_string($var) && trim($var) == '') {
                    return true;
                } else {
                    if (is_array($var) && count($var) == 0) {
                        return true;
                    } else {
                        if (is_object($var) && ($var instanceof Collection) && count($var) == 0) {
                            return true;
                        }
                    }
                }
            }
        }

        return false;
    }

    public static function isProduction(): bool
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
     * @param  string  $type
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
        $valueLength = strlen($value);
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
        $k = 0;
        for ($i = 0; $i <= strlen($mask) - 1; $i++) {
            if ($mask[$i] == '#') {
                if (isset($val[$k])) {
                    $maskared .= $val[$k++];
                }
            } else {
                if (isset($mask[$i])) {
                    $maskared .= $mask[$i];
                }
            }
        }

        return $maskared;
    }

    /**
     * @param $value
     * @param  string  $locale
     * @param  string  $currency
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

    public static function formatCellPhoneGetNet($number)
    {
        $number = self::onlyNumbers($number);
        return [
            'dd' => substr($number, 2, 2),
            'number' => substr($number, 4)
        ];
    }

    /**
     * @param  string  $value
     * @return array
     */
    public static function splitName($value)
    {
        $name = trim($value);
        $last_name = (strpos($name, ' ') === false) ? '' : preg_replace(
            '#.*\s([\wáàâãéèêíïóôõöúçñÁÀÂÃÉÈÍÏÓÔÕÖÚÇÑ\'\s]*)$#',
            '$1',
            $name
        );
        if ($last_name == $name) {
            return [$name, $name];
        }
        $qtdd = strlen($last_name);
        $first_name = trim(substr($name, 0, -$qtdd));
        if ($first_name == '' || $last_name == '') {
            return [$name, $name];
        }

        return [$first_name, $last_name];
    }

    /**
     * @param $url
     * @param null $expiration
     * @param null $type
     * @return string
     */
    public static function getAwsSignedUrl($url, $expiration = null, $type = 'digital-products')
    {
        try {
            if (!empty($url)) {

                $urlKey = str_replace("https://cloudfox-${type}.s3.amazonaws.com/", '', $url);

                $client = new S3Client([
                    'credentials' => [
                        'key' => env('AWS_ACCESS_KEY_ID'),
                        'secret' => env('AWS_SECRET_ACCESS_KEY'),
                    ],
                    'region' => env('AWS_DEFAULT_REGION'),
                    'version' => '2006-03-01',
                ]);

                $command = $client->getCommand('GetObject', [
                    'Bucket' => "cloudfox-${type}",
                    'Key' => $urlKey,
                ]);

                $urlExpirationTime = $expiration ?? 24;

                $signedRequest = $client->createPresignedRequest($command, "+$urlExpirationTime hours");

                $signedUrl = (string)$signedRequest->getUri();

                return $signedUrl;
            }
            return '';
        } catch (Exception $ex) {
            return '';
        }
    }

    public static function getFormatState($uf)
    {
        switch ($uf) {
            case 'Acre':
                return 'AC';
            case 'Alagoas':
                return 'AL';
            case 'Amapá':
                return 'AP';
            case 'Amazonas':
                return 'AM';
            case 'Bahia':
                return 'BA';
            case 'Ceará':
                return 'CE';
            case 'Distrito Federal':
                return 'DF';
            case 'Espírito Santo':
                return 'ES';
            case 'Goiás':
                return 'GO';
            case 'Maranhão':
                return 'MA';
            case 'Mato Grosso':
                return 'MT';
            case 'Mato Grosso do Sul':
                return 'MS';
            case 'Minas Gerais':
                return 'MG';
            case 'Pará':
                return 'PA';
            case 'Paraíba':
                return 'PB';
            case 'Paraná':
                return 'PR';
            case 'Pernambuco':
                return 'PE';
            case 'Piauí':
                return 'PI';
            case 'Rio de Janeiro':
            case 'Rio de janeiro':
                return 'RJ';
            case 'Rio Grande do Norte':
                return 'RN';
            case 'Rio Grande do Sul':
            case 'rio grande do sul':
                return 'RS';
            case 'Rondônia':
                return 'RO';
            case 'Roraima':
                return 'RR';
            case 'santa catarina':
            case 'Santa Catarina':
                return 'SC';
            case 'Sao Paulo':
            case 'São Paulo':
                return 'SP';
            case 'Sergipe':
                return 'SE';
            case 'Tocantins':
                return 'TO';
            default:
                return $uf;
        }
    }

    public static function formatCellPhoneBraspag($number)
    {
        $number = self::onlyNumbers($number);
        return substr($number, 2);
    }

    public static function getPortionOfString($string, $start, $length = null)
    {
        return $length != null ? substr($string, $start, $length) : substr($string, $start);
    }

    public static function calcTime($time) {
        $currentDate = date("Y-m-d H:i:s");
        $dateDiff = $time->diff($currentDate);

        $return = '';
        if ( $dateDiff->y > 1 ) {
            $return = 'Há ' . $dateDiff->y .' anos';
        }
        elseif ( $dateDiff->y === 1 ) {
            $return = 'Há um ano';
        }
        elseif ( $dateDiff->m > 1 ) {
            $return = 'Há ' . $dateDiff->m .' meses';
        }
        elseif ( $dateDiff->m === 1 ) {
            $return = 'Há um mês';
        }
        elseif ( $dateDiff->d >= 14 ) {
             $semanas = floor($dateDiff->d / 7);
            $return = 'Há ' . $semanas .' semanas';
        }
        elseif ( $dateDiff->d >= 7 ) {
           $return = 'Há uma semana';
       }
       elseif ( $dateDiff->d > 1 ) {
            $return = 'Há ' . $dateDiff->d .' dias';
        }
        elseif ( $dateDiff->d === 1 ) {
            $return = 'Há um dia';
        }
        elseif ( $dateDiff->h > 1 ) {
            $return = 'Há ' . $dateDiff->h .' horas';
        }
        elseif ( $dateDiff->h === 1 ) {
            $return = 'Há um hora';
        }
        elseif ( $dateDiff->i >= 1 ) {
            $return = 'Há ' . $dateDiff->i  . ' minutos';
        }
        elseif ( $dateDiff->i < 1 ) {
            $return = 'Há um minuto';
        }

        return $return;
    }

    public static function getnetReasonByCode($code)
    {
        switch($code) {
            case '4837':
            case '4863':
            case '81':
            case '83':
            case '74':
            case '103':
            case '104':
            case '4540':
            case '4755':
                return 'Portador não reconhece a transação';
            case '4840':
            case '57':
                return 'Múltiplas transações fraudulentas';
            case '4860':
            case '75':
            case '136':
            case '137':
            case '85':
            case '4513':
                return 'Cancelamento / crédito não processado';
            case '4855':
            case '79':
            case '131':
            case '30':
            case '4554':
                return 'Mercadoria / serviços não prestados';
            case '4841':
            case '132':
            case '41':
            case '4544':
                return 'Cancelamento de transações recorrentes';
            case '133':
            case '134':
            case '53':
            case '4553':
                return 'Mercadoria falsificada / defeituosa ou não conforme com o descrito';
            case '4853':
            case '135':
                return 'Desacordo comercial (no geral)';
            case '4859':
                return 'Valor adicional cobrado por um serviço prestado ou NO SHOW';
            case '4834':
            case '73':
            case '1261':
            case '82':
            case '4512':
                return 'Duplicidade da transação';
            case '4831':
            case '1262':
            case '86':
            case '4515':
                return 'Pagamentos por outros meios';
            case '123':
            case '4530':
                return 'Moeda incorreta';
            case '124':
            case '4507':
            case '4523':
                return 'Valor da transação ou número de conta incorreta ou inexistente';
            case '4527':
                return 'Falta de impressão';
            case '4534':
                return 'Múltiplos comprovantes';
            case '80':
            case '4753':
                return 'Erro / divergência de processamento';
            case '125':
            case '77':
                return 'Valor incorreto';
            case '4850':
                return 'Transação fraudulenta/sem autorização';
            default:
                return isset($reason) ? str_replace("?", "Ã", $reason) : '';

        }
    }

    /**
     * @param $name
     * @return array|false
     */
    public static function getFirstName($name)
    {
        $parts = [];

        while ( strlen( trim($name)) > 0 ) {
            $name = trim($name);
            $string = preg_replace('#.*\s([\w-]*)$#', '$1', $name);
            $parts[] = $string;
            $name = trim( preg_replace('#'.preg_quote($string,'#').'#', '', $name ) );
        }

        if (empty($parts)) {
            return false;
        }

        $parts = array_reverse($parts);
        $name = array();
        $name['first_name'] = $parts[0];
        $name['middle_name'] = (isset($parts[2])) ? $parts[1] : '';
        $name['last_name'] = (isset($parts[2])) ? $parts[2] : ( isset($parts[1]) ? $parts[1] : '');

        return $name;
    }
}
