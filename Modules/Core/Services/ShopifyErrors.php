<?php

namespace Modules\Core\Services;

/**
 * Class ShopifyErrors
 * @package Modules\Core\Services
 */
class ShopifyErrors
{
    /**
     * @param $error
     * @return string
     */
    public static function FormatErrors($error)
    {
        $message = "Problema ao refazer integração, tente novamente mais tarde";
        if (strstr($error, "[API] Invalid API key or access token (unrecognized login or wrong password)", true)) {
            $message = 'Chave API inválida ou token de acesso (login não reconhecido ou senha incorreta)';
        }

        return $message;
    }
}
