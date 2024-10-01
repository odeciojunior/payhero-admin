<?php

namespace App\Rules;

use Illuminate\Validation\Validator as BaseValidator;

/**
 * @author Daniel Rodrigues Lima
 * @email danielrodrigues-ti@hotmail.com
 */
class Validator extends BaseValidator
{
    protected function validateCpf($attribute, $value)
    {
        $c = preg_replace("/\D/", "", $value);
        if (strlen($c) != 11 || preg_match("/^{$c[0]}{11}$/", $c)) {
            return false;
        }
        for ($s = 10, $n = 0, $i = 0; $s >= 2; $n += $c[$i++] * $s--);
        if ($c[9] != (($n %= 11) < 2 ? 0 : 11 - $n)) {
            return false;
        }
        for ($s = 11, $n = 0, $i = 0; $s >= 2; $n += $c[$i++] * $s--);
        if ($c[10] != (($n %= 11) < 2 ? 0 : 11 - $n)) {
            return false;
        }

        return true;
    }

    protected function validateCnpj($attribute, $value)
    {
        $c = preg_replace("/\D/", "", $value);
        if (strlen($c) != 14 || preg_match("/^{$c[0]}{14}$/", $c)) {
            return false;
        }
        $b = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        for ($i = 0, $n = 0; $i < 12; $n += $c[$i] * $b[++$i]);
        if ($c[12] != (($n %= 11) < 2 ? 0 : 11 - $n)) {
            return false;
        }
        for ($i = 0, $n = 0; $i <= 12; $n += $c[$i] * $b[$i++]);
        if ($c[13] != (($n %= 11) < 2 ? 0 : 11 - $n)) {
            return false;
        }

        return true;
    }

    protected function validateTemplateStringMinVisitorInFinalizingPurchaseConfig($attribute, $value)
    {
        return strpos($value, "{visitantes}");
    }
}
