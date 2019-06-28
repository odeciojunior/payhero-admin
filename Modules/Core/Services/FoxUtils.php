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
}
